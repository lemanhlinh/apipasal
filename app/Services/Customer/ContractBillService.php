<?php

namespace App\Services\Customer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Models\Customer\ContractBill;


class ContractBillService
{
    public function store($request)
    {
        $user = Auth::user();
        $data = [
            'contract_id' => $request['contract_id'],
            'bill_type' => $request['bill_type'] ?: 1,
            'transaction_type' => $request['transaction_type'] ?: 1,
            'amount_payment' => str_replace('.', '', $request['amount_payment']),
            'bill_number' => $request['bill_number'],
            'date_payment' => Carbon::parse($request['date_payment'])->format('Y-m-d'),
            'note' => $request['note'],
            'active' => $request['active'] ?: 0,
            'user_create_id' => $user->id,
            'user_accept_id' => 0
        ];

        $bill = ContractBill::create($data);

        return $bill;
    }
}
