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
        $data = [
            'contract_id' => $request['contract_id'],
            'bill_type' => $request['bill_type'] ?: 1,
            'transaction_type' => $request['transaction_type'] ?: 1,
            'amount_payment' => str_replace('.', '', $request['amount_payment']),
            'bill_number' => $request['bill_number'],
            'date_payment' => Carbon::parse($request['date_payment'])->format('Y-m-d'),
            'note' => $request['note'],
        ];

        $bill = ContractBill::create($data);

        return $bill;
    }
}
