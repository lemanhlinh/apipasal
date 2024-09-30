<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;

use App\Models\Customer\Customer;
use App\Models\Customer\ChangeManagement;
use App\Models\Customer\CustomerStatus;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

use App\Services\Business\BusinessPartnerService;
use App\Services\Customer\CustomerService;

class CustomerController extends Controller
{
    protected $businessPartnerService;
    protected $customerService;

    public function __construct(BusinessPartnerService $businessPartnerService, CustomerService $customerService)
    {
        $this->businessPartnerService = $businessPartnerService;
        $this->customerService = $customerService;
    }

    public function index()
    {
        $user = Auth::user();

        $customer = Customer::orderBy('id', 'DESC')
            ->where('manage_id', $user->id)
            ->with([
                'management' => function ($query) {
                    $query->select('id', 'name', 'department_id')->with(['department' => function ($query2) {
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
        DB::beginTransaction();
        try {

            $customer = $this->customerService->store($request);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'data' => $customer,
                'result' => 'Đã thêm mới khách hàng!',
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'result' => 'Chưa thêm được khách hàng!',
            ));
        }
    }

    public function detail(Request $request)
    {
        $data = Customer::where('phone', $request->telephone)
            ->with([
                'management' => function ($query) {
                    $query->select('id', 'name', 'department_id')->with(['department' => function ($queryDepartment) {
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
            $data->source_info;
            $data->consulting_detail =  json_decode($data->consulting_detail);
            foreach ($data->segment as $segmentItem) {
                $segmentItem->parent = json_decode($segmentItem->parent);
            }
        }

        return response()->json(array(
            'error' => false,
            'message' => 'Thành công',
            'data' => $data
        ));
    }

    public function update(Request $request)
    {
        // return response()->json(array(
        //     'error' => false,
        //     'message' => 'Cập nhật khách hàng thành công!',
        //     'data' => $request->all()
        // ));

        DB::beginTransaction();
        try {
            $data = [];

            if ($request->issue) {
                $data['issue'] = $request->issue;
            }

            if ($request->consulting) {
                $data['consulting'] = $request->consulting;
            }

            if ($request->potential) {
                $data['potential'] = $request->potential;
            }

            if ($request->consulting_detail) {
                $data['consulting_detail'] = json_encode($request->consulting_detail);
            }

            if ($request->consulting_date) {
                $data['consulting_date'] = Carbon::createFromFormat('dmY', $request->consulting_date)->format('Y-m-d');
            }

            if (isset($request->contract)) {
                $data['contract'] = $request->contract ? 1 : 0;
            }

            if ($request->product) {
                $data['product'] = $request->product;
            }

            if ($request->product_category) {
                $data['product_category'] = $request->product_category;
            }

            if ($request->date_registration) {
                $data['date_registration'] = Carbon::createFromFormat('d/m/Y', $request->date_registration)->format('Y-m-d');
            }

            $customer = Customer::findOrFail($request->id);

            $customer->update($data);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'message' => 'Cập nhật khách hàng thành công!',
                'data' => $data
            ));
        } catch (\Exception $exception) {
            Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'message' => 'Cập nhật khách hàng không thành công!',
            ));
        }
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

    private function handleTransaction(callable $callback, $successMessage = 'Thao tác thành công!', $errorMessage = 'Thao tác không thành công!')
    {
        DB::beginTransaction();
        try {
            $result = $callback();

            DB::commit();

            return response()->json(array(
                'error' => false,
                'message' => $successMessage,
                'data' => $result
            ));
        } catch (\Exception $exception) {
            Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);

            DB::rollBack();

            return response()->json(array(
                'error' => true,
                'message' => $errorMessage,
            ));
        }
    }

    public function changeManagement(Request $request)
    {
        $user = Auth::user();
        $customer = Customer::where('id', $request->id)->first();

        if (!$customer || $customer->manage_id == $user->id || $customer->active != 0) {
            return response()->json(array(
                'error' => false,
                'message' => 'Không tìm thấy khách hàng hoặc bạn không có quyền thay đổi quản lý khách hàng này!',
                'data' => []
            ));
        }

        $changeManagement = ChangeManagement::where('customer_id', $request->customer_id)
            ->where('user_id', $user->id)
            ->where('status', 0)
            ->first();

        if ($changeManagement) {
            return response()->json(array(
                'error' => false,
                'message' => 'Bạn đã đề xuất thay đổi quản lý khách hàng này!',
                'data' => []
            ));
        }

        try {
            $result = ChangeManagement::create([
                'customer_id' => $request->customer_id,
                'user_id' => $user->id,
                'reason' => $customer->reason,
            ]);

            DB::commit();

            return response()->json(array(
                'error' => false,
                'message' => "Đề xuất thay đổi thành công!",
                'data' => $result
            ));
        } catch (\Exception $exception) {
            Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);

            DB::rollBack();

            return response()->json(array(
                'error' => true,
                'message' => "Đề xuất thay đổi không thành công!",
            ));
        }
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
}
