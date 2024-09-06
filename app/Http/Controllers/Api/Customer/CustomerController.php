<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerCustomer;
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $customer = CustomerCustomer::orderBy('id', 'DESC')
            ->where('manage_id', $user->id)
            ->with([
                'management' => function ($query) {
                    $query->select('id', 'name', 'department_id')->with(['department' => function ($query2) {
                        $query2->select('id', 'title')->with(['campuses' => function ($query3) {
                            $query3->select('campuses.id', 'campuses.code');
                        }]);
                    }]);
                },
                'source_info' => function ($query) {
                    $query->select('id', 'title', 'code');
                }
            ])
            ->get();

        foreach ($customer as $item) {
            $item->consulting_detail =  json_decode($item->consulting_detail);
        }

        return $customer;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerCustomer  $businessPartner
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerCustomer $businessPartner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

            $customer = CustomerCustomer::findOrFail($request->id);

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $record = CustomerCustomer::where('id', $request->id)
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
}
