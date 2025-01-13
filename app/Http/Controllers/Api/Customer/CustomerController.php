<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;

use App\Models\Customer\Customer;
use App\Models\Customer\ChangeManager;
use App\Models\Customer\CustomerStatus;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

use App\Services\Business\BusinessPartnerService;
use App\Services\Customer\CustomerService;

use App\Constants\Customer\Active;
use App\Constants\Customer\Type;
use App\Models\Customer\CustomerHistory;
use App\Models\Products;

class CustomerController extends Controller
{
    protected $businessPartnerService;
    protected $customerService;

    public function __construct(BusinessPartnerService $businessPartnerService, CustomerService $customerService)
    {
        $this->businessPartnerService = $businessPartnerService;
        $this->customerService = $customerService;
    }

    public function isDepot($cutomer)
    {
        return $cutomer->type == Type::DEPOT || ($cutomer->active_date && (Carbon::parse($cutomer->active_date)->isToday() || Carbon::parse($cutomer->active_date)->isPast()));
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $contract = $request->contract;

        $customer = Customer::orderBy('id', 'DESC')
            ->where('manage_id', $user->id)
            ->where(function ($query) use ($contract) {
                if ($contract) {
                    $query->where('contract', 1);
                }
            })
            ->with([
                'management' => function ($query) {
                    $query->select('id', 'name', 'department_id', 'email')->with(['department' => function ($query2) {
                        $query2->select('id', 'title')->with(['campuses' => function ($query3) {
                            $query3->select('campuses.id', 'campuses.code');
                        }]);
                    }]);
                },
            ])
            ->get();

        foreach ($customer as $item) {
            $item->consulting_detail =  json_decode($item->consulting_detail);
            $item->source;
        }

        return $customer;
    }

    public function store(Request $request)
    {
        return $this->handleTransaction(function () use ($request) {
            $customer = $this->customerService->store($request->all());

            DB::commit();
            return response()->json(array(
                'error' => false,
                'data' => $customer,
                'message' => 'Đã thêm mới khách hàng!',
            ));
        }, 'Chưa thêm được khách hàng!');
    }

    public function detail(Request $request)
    {
        $user = Auth::user();
        $data = Customer::where('phone', $request->telephone)
            ->with([
                'management' => function ($query) {
                    $query->select('id', 'name', 'department_id', 'email')->with(['department' => function ($queryDepartment) {
                        $queryDepartment->select('id', 'title')->with(['campuses' => function ($queryCampus) {
                            $queryCampus->select('campuses.id', 'campuses.code');
                        }]);
                    }]);
                },
                'country' => function ($query) {
                    $query->select('id', 'name');
                },
                'city' => function ($query) {
                    $query->select('id', 'name', 'code');
                },
                'district' => function ($query) {
                    $query->select('id', 'name', 'code');
                },
                'segment' => function ($query) {
                    $query->with([
                        'district' => function ($queryDistrict) {
                            $queryDistrict->select('id', 'name', 'code');
                        },
                        'market' => function ($queryMarket) {
                            $queryMarket->select('id', 'title');
                        },
                    ]);
                },
                'students',
            ])
            ->first();

        if ($data) {
            $data->source;
            $data->consulting_detail = json_decode($data->consulting_detail);
            foreach ($data->segment as $segmentItem) {
                $segmentItem->parent = json_decode($segmentItem->parent);
            }
        }

        return response()->json(array(
            'error' => false,
            'message' => 'Thành công',
            'data' => $data,
            'user_request_id' => $user->id
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $customer = Customer::findOrFail($request->id);

        if (!$customer->id) {
            return response()->json(array(
                'error' => true,
                'message' => 'Không tìm thấy khách hàng!',
            ));
        }

        $data = $request->all();

        if ($request->consulting_detail) {
            $data['consulting_detail'] = json_encode($request->consulting_detail, JSON_UNESCAPED_UNICODE);
        }

        if ($request->consulting_date) {
            $data['consulting_date'] = Carbon::parse($request->consulting_date)->format('Y-m-d');
        }

        if (isset($request->contract)) {
            $data['contract'] = $request->contract ? 1 : 0;
        } 

        if ($request->date_registration) {
            $data['date_registration'] = Carbon::parse($request->date_registration)->format('Y-m-d');
        }

        if ($this->isDepot($customer)) {
            $data['manage_id'] = $user->id;
            $data['type'] = Type::NEW;
            $data['active'] = Active::CARE;
            $data['active_date'] = Carbon::now()->addDay(30)->format('Y-m-d'); 
        }

        $handle = $this->handleTransaction(function () use ($data, $customer) {
            $update = $this->customerService->update($data, $customer);
            return response()->json(array(
                'error' => false,
                'data' => $update,
                'message' => 'Cập nhật khách hàng thành công!',
            ));
        }, 'Cập nhật khách hàng không thành công!');

        return $handle;
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        $record = Customer::where('id', $request->id)
            ->where('manage_id', $user->id)
            ->first();

        if (!$record) {
            return response()->json(array(
                'error' => false,
                'message' => 'Không tìm thấy khách hàng hoặc bạn không có quyền xóa khách hàng này!',
                'data' => []
            ));
        }

        $handle = $this->handleTransaction(function () use ($request) {
            return $this->customerService->destroy($request);
        }, 'Xóa khách hàng thành công!', 'Xóa khách hàng không thành công!');

        return $handle;
    }

    public function statistics()
    {
        $user = Auth::user();

        $data = CustomerStatus::where('manage_id', $user->id)
            ->select([
                DB::raw('SUM(primary_school) as primary_school_total'),
                DB::raw('SUM(secondary_school) as secondary_school_total'),
                DB::raw('SUM(high_school) as high_school_total'),
                DB::raw('SUM(college) as college_total'),
                DB::raw('SUM(working) as working_total'),
                DB::raw('SUM(customer_success) as customer_success_total'),
                DB::raw('SUM(customer_new) as customer_new_total'),
                DB::raw('SUM(customer_depot) as customer_depot_total'),
                DB::raw('SUM(customer_total) as customer_total_total'),
                DB::raw('SUM(contract_total) as contract_total_total'),
                DB::raw('SUM(contract_success) as contract_success_total'),
                DB::raw('SUM(contract_expired) as contract_expired_total'),
            ])
            ->first();

        return response()->json(array(
            'error' => false,
            'message' => "Thành công!",
            'data' => $data
        ));
    }

    public function historyUpdate(Request $request)
    {
        $customer_id = $request->customer_id;
        $telephone = $request->telephone;

        $data = CustomerHistory::where(function ($query) use ($customer_id, $telephone) {
                if ($telephone) {
                    $customer = Customer::where('phone', $telephone)->first();
                    $query->where('customer_id', $customer->id);
                } else {
                    $query->where('customer_id', $customer_id);
                }
            })
            ->with('user')
            ->orderBy('id', 'DESC')
            ->get();

        $history = [];
        $productIds = [];
        $productCategoryIds = [];

        foreach ($data as $item) {
            $fields = json_decode($item->field);
            $oldValue = json_decode($item->old_value);
            $newValue = json_decode($item->new_value);

            $data = [];
            foreach ($fields as $i => $field) {
                if ($field == 'product_id') {
                    $productIds[] = $oldValue[$i];
                    $productIds[] = $newValue[$i];
                }

                if ($field == 'product_category_id') {
                    $productCategoryIds[] = $oldValue[$i];
                    $productCategoryIds[] = $newValue[$i];
                }

                $data[] = [
                    'field' => $field,
                    'old_value' => $oldValue[$i],
                    'new_value' => $newValue[$i],
                ];
            }

            $history[] = [
                'id' => $item->id,
                'customer_id' => $item->customer_id,
                'user_id' => $item->user_id,
                'user' => $item->user,
                'created_at' => $item->created_at,
                'data' => $data,
            ];
        }

        $productIds = array_filter(array_unique($productIds));
        $productCategoryIds = array_filter(array_unique($productCategoryIds));

        $products = Products::whereIn('id', $productIds)->get();
        $productCategories = Products::whereIn('id', $productCategoryIds)->get();

        foreach ($history as $ih => $itemHistory) {
            foreach ($itemHistory['data'] as $id => $itemData) {
                foreach ($products as $product) {
                    if ($itemData['field'] == 'product_id' && $itemData['old_value'] == $product->id) {
                        $history[$ih]['data'][$id]['old_value'] = $product->title;
                    }

                    if ($itemData['field'] == 'product_id' && $itemData['new_value'] == $product->id) {
                        $history[$ih]['data'][$id]['new_value'] = $product->title;
                    }
                }

                foreach ($productCategories as $productCategory) {
                    if ($itemData['field'] == 'product_category_id' && $itemData['old_value'] == $productCategory->id) {
                        $history[$ih]['data'][$id]['old_value'] = $productCategory->title;
                    }

                    if ($itemData['field'] == 'product_category_id' && $itemData['new_value'] == $productCategory->id) {
                        $history[$ih]['data'][$id]['new_value'] = $productCategory->title;
                    }
                }
            }
        }

        return response()->json(array(
            'error' => false,
            'message' => "Thành công!",
            'data' => $history
        ));
    }

    public function search(Request $request)
    {
        $user = Auth::user();

        $data = Customer::select('id', 'title', 'phone', 'manage_id', 'segment_id', 'type', 'active_date', 'consulting', 'consulting_detail', 'consulting_date', 'created_at')
            ->where('phone', 'like', "%$request->telephone%")
            ->with([
                'management' => function ($query) {
                    $query->select('id', 'name', 'department_id', 'email')->with(['department' => function ($queryDepartment) {
                        $queryDepartment->select('id', 'title')->with(['campuses' => function ($queryCampus) {
                            $queryCampus->select('campuses.id', 'campuses.code');
                        }]);
                    }]);
                }, 
            ])
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($data as $item) {
            $item->consulting_detail =  json_decode($item->consulting_detail);
            $item->user_search_id = $user->id;
        }

        return response()->json(array(
            'error' => false,
            'message' => 'Thành công',
            'data' => $data
        ));
    }
}
