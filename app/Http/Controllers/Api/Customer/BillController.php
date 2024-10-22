<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Services\Customer\ContractBillService;

use App\Models\Customer\ContractBill;

class BillController extends Controller
{
    protected $contractBillService;

    public function __construct(ContractBillService $contractBillService)
    {
        $this->contractBillService = $contractBillService;
    }

    public function index(Request $request)
    { 
        $model = ContractBill::orderBy('id', 'DESC')
            ->with([
                'user_create',
                'user_accept',
                'contract' => function ($queryContract){
                    $queryContract->with([
                        'product',
                        'product_category',
                        'management' => function ($query) {
                            $query->select('id', 'name', 'department_id')->with(['department' => function ($query2) {
                                $query2->select('id', 'title')->with(['campuses' => function ($query3) {
                                    $query3->select('campuses.id', 'campuses.code');
                                }]);
                            }]);
                        },
                        'student' => function ($query) {
                            $query->with([
                                'segment',
                                'customer' => function ($queryCustomer) {
                                    $queryCustomer->with(['management' => function ($queryManagement){
                                        $queryManagement->select('id', 'name', 'department_id')->with(['department' => function ($query2) {
                                            $query2->select('id', 'title')->with(['campuses' => function ($query3) {
                                                $query3->select('campuses.id', 'campuses.code');
                                            }]);
                                        }]);
                                    }]);
                                },
                            ]);
                        },
                    ]);
                },
               
            ]);

        if ($request->has('keyword') && $request->keyword) {
            $model->whereHas('contract.student.customer', function ($query) use ($request) {
                $query->where('phone', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->has('date_payment') && $request->date_payment) {
            $model->whereDate('date_payment', $request->date_payment);
        }

        if ($request->has('transaction_type') && $request->date_payment) {
            $model->where('transaction_type', $request->transaction_type);
        }

        if ($request->has('bill_type') && $request->bill_type) {
            $model->where('bill_type', $request->bill_type);
        }

        if ($request->has('active') && $request->active != null) {
            $model->where('active', $request->active);
        }

        $bills = $model->paginate(20);

        $totalPages = $bills->lastPage();
        $data = $bills->items();

        return response()->json(array(
            'error' => false,
            'data' => [
                'data' => $data,
                'total_pages' => $totalPages,
            ],
        ));
    }

    public function update(Request $request)
    {
        return $this->handleTransaction(function() use ($request) {
            $bill = ContractBill::findOrFail($request->id);

            if (!$bill) {
                return response()->json(array(
                    'error' => true,
                    'message' => 'Hóa đơn không tồn tại!',
                    'data' => []
                ));
            } 

            $data = [];

            if ($request->has('active')) {
                $data['active'] = $request->active;
                $user = Auth::user();
                $data['user_accept_id'] = $user->id;
            }

            if ($request->has('note')) {
                $data['note'] = $request->note;
            }

            if ($request->has('bill_type')) {
                $data['bill_type'] = $request->bill_type;
            }

            $bill->update($data);

            $bill->user_accept;

            DB::commit();
            return response()->json(array(
                'error' => false,
                'message' => 'Cập nhật hóa đơn thành công!',
                'data' => $bill
            ));            
        }, 'Cập nhật hóa đơn không thành công!');
    }

    public function remove(Request $request)
    {
        return $this->handleTransaction(function() use ($request) {
            $bill = ContractBill::findOrFail($request->id);

            if (!$bill) {
                return response()->json(array(
                    'error' => true,
                    'message' => 'Hóa đơn không tồn tại!',
                    'data' => []
                ));
            } 

            $bill->delete();

            return response()->json(array(
                'error' => false,
                'message' => 'Xóa hóa đơn thành công!',
            ));
        }, 'Xóa hóa đơn không thành công!');
    }

    public function store(Request $request)
    {
        return $this->handleTransaction(function() use ($request) {
            $bill = $this->contractBillService->store($request->all());

            return response()->json(array(
                'error' => false,
                'message' => 'Thêm mới hóa đơn thành công!',
                'data' => $bill
            ));
        }, 'Thêm mới hóa đơn không thành công!');
    }
}
