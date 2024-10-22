<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

use App\Constants\Customer\BillTransaction;
use App\Constants\Customer\BillType;

class ContractDebt extends Model
{
    use HasFactory;
    protected $table = 'customer_contract_debts';
    protected $guarded = ['id'];
    // protected $appends = ['amount_real'];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    // public function getAmountRealAttribute()
    // {
    //     $amount_real = 0;
    //     $monthDebt = Carbon::parse($this->date)->format('m');
    //     $yearDebt = Carbon::parse($this->date)->format('Y');

    //     $contract = $this->contract;

    //     foreach ($contract->bills as $bill) {
    //         $monthBill = Carbon::parse($bill->date)->format('m');
    //         $yearBill = Carbon::parse($bill->date)->format('Y');
    //         if ($bill->transaction_type == BillTransaction::TUITITION && $bill->bill_type == BillType::RECEIVE) {
    //             if ($monthDebt == $monthBill && $yearDebt == $yearBill) {
    //                 $amount_real += $bill->amount_payment;
    //             }
    //         }
    //     }

    //     return $amount_real;
    // }
}
