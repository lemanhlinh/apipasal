<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Contract;
use App\Models\Customer\ContractDebt;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

use App\Constants\Customer\BillTransaction;
use App\Constants\Customer\BillType;

class DebtController extends Controller
{
    public function store(Request $request)
    {
        $handle = $this->handleTransaction(function () use ($request) {
            $data = ContractDebt::create([ 
                'contract_id' => $request->contract_id,
                'date' => Carbon::parse($request->date)->format('Y-m-d'),
                'amount' => str_replace('.', '', $request->amount),
            ]);
            return response()->json(array(
                'error' => false,
                'data' => $data,
                'message' => 'Thêm dự báo thu nợ thành công!',
            ));
        }, 'Thêm dự báo thu nợ không thành công!');

        return $handle;
    }

    public function update(Request $request)
    {
        return $this->handleTransaction(function () use ($request) {
            $data = ContractDebt::find($request->id);

            if (!$data) {
                return response()->json(array(
                    'error' => true,
                    'message' => 'Dữ liệu không tồn tại!',
                ));
            }

            $data->update([ 
                'date' => Carbon::parse($request->date)->format('Y-m-d'),
                'amount' => str_replace('.', '', $request->amount),
            ]);

            return response()->json(array(
                'error' => false,
                'data' => $data,
                'message' => 'Cập nhật dự báo thu nợ thành công!',
            ));
        }, 'Cập nhật dự báo thu nợ không thành công!');
    }

    public function remove(Request $request)
    {
        $handle = $this->handleTransaction(function () use ($request) {
            $data = ContractDebt::find($request->id);

            if (!$data) {
                return response()->json(array(
                    'error' => true,
                    'message' => 'Dữ liệu không tồn tại!',
                ));
            }

            $data->delete();

            return response()->json(array(
                'error' => false,
                'data' => $data,
                'message' => 'Xóa dự báo thu nợ thành công!',
            ));
        }, 'Xóa dự báo thu nợ không thành công!');

        return $handle;
    }

    public function stats(Request $request)
    {
        $month = Carbon::now()->month;
        $data = [
            'debt_total' => 0,
            'debt_current_month_before' => 0,
            'debt_current_month' => 0,

            'debt_receive_current_month_before' => 0,
            'debt_percent' => 0,
            'debt_cancel' => 0,
        ];

        $contracts = Contract::with(['bills' => function ($query) {
            $query->where('active', 1);
        }])->get();

        foreach ($contracts as $contract) {
            if ($contract->active) {
                if (Carbon::parse($contract->date_contract)->month == $month) {
                    $data['debt_current_month'] += $contract->amount_debt;
                } else {
                    $data['debt_current_month_before'] += $contract->amount_debt;
                }
            } else {
                $data['debt_cancel'] += $contract->amount_debt;
            }
            foreach ($contract->bills as $bill) {
                if ($bill->transaction_type == BillTransaction::TUITITION && $bill->bill_type == BillType::RECEIVE && $bill->active) {
                    if (Carbon::parse($bill->date_payment)->month == $month) {
                        $data['debt_receive_current_month_before'] += $bill->amount_payment;
                    }
                }
            }
        }

        $data['debt_total'] = $data['debt_current_month_before'] + $data['debt_current_month'];
        $data['debt_percent'] = $data['debt_current_month_before'] > 0 ? round($data['debt_receive_current_month_before'] / $data['debt_current_month_before'], 2) : 0;

        return response()->json(array(
            'error' => false,
            'data' => $data,
            'message' => 'Thành công!',
        ));
    }

    public function forecast(Request $request)
    {
        return response()->json(array(
            'error' => false,
            'data' => [],
            'message' => 'Thành công!',
        ));
    }
}
