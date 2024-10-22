<?php

namespace App\Models\Customer;

use App\Constants\Customer\BillActive;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\ProductCategories;
use App\Models\BusinessPolicy;
use App\Models\Products;

use App\Constants\Customer\BillTransaction;
use App\Constants\Customer\BillType;
use Illuminate\Support\Carbon;

class Contract extends Model
{
    use HasFactory;
    protected $table = 'customer_contracts';
    protected $guarded = ['id'];

    protected $appends = [
        'amount_paid',
        'amount_end',
        'amount_spent',
        'amount_debt',
        'percent_debt',
        'percent_discount',
        'total_bills_tuition',
        'days_debt',
        'next_day_debt',
        'next_amount_debt'
    ]; 
    
    public function updateDebtsAmountReal()
    {
        $amountBillArr = [];

        foreach ($this->bills as $bill) {
            $monthYearBill = Carbon::parse($bill->date)->format('mY');

            if ($bill->transaction_type == BillTransaction::TUITITION && $bill->bill_type == BillType::RECEIVE && $bill->active == BillActive::ACCEPT) {
                if (isset($amountBillArr[$monthYearBill])) {
                    $amountBillArr[$monthYearBill] += $bill->amount_payment;
                } else {
                    $amountBillArr[$monthYearBill] = $bill->amount_payment;
                }
            }
        } 

        foreach ($this->debts as $debt) {
            $monthYearDebt = Carbon::parse($debt->date)->format('mY');

            $amount_real = @$amountBillArr[$monthYearDebt] ?? 0;
            $debt->amount_real = $amount_real;
        }
    }

    public function bills()
    {
        return $this->hasMany(ContractBill::class, 'contract_id', 'id');
    }

    public function debts()
    {
        return $this->hasMany(ContractDebt::class, 'contract_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function product_category()
    {
        return $this->belongsTo(ProductCategories::class, 'product_category_id', 'id');
    }

    public function management()
    {
        return $this->belongsTo(User::class, 'manage_id', 'id');
    }

    public function user_create()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function special()
    {
        return $this->belongsTo(BusinessPolicy::class, 'special_id', 'id');
    }

    public function promtion()
    {
        return $this->belongsTo(BusinessPolicy::class, 'promotion_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }

    public function getAmountPaidAttribute()
    {
        $amountPaid = 0;
        foreach ($this->bills as $bill) {
            if ($bill->transaction_type == BillTransaction::TUITITION && $bill->bill_type == BillType::RECEIVE && $bill->active == BillActive::ACCEPT) {
                $amountPaid += $bill->amount_payment;
            }
        }

        return $amountPaid;
    }

    public function getAmountEndAttribute()
    {
        return $this->amount - $this->amount_offer - $this->amount_promotion - $this->amount_special;
    }

    public function getAmountSpentAttribute()
    {
        $amount_spent = 0;
        foreach ($this->bills as $bill) {
            if ($bill->transaction_type == BillTransaction::TUITITION && $bill->bill_type == BillType::PAYMENT) {
                $amount_spent += $bill->amount_payment;
            }
        }

        return $amount_spent;
    }

    public function getAmountDebtAttribute()
    {
        $amount_debt = $this->amount_end - $this->amount_paid;

        return $amount_debt > 0 ? $amount_debt : 0;
    }

    public function getPercentDebtAttribute()
    {
        $percent = $this->amount_end > 0 ? ($this->amount_end - $this->amount_paid) / $this->amount_end * 100 : 0;
        return round($percent, 1) . '%';
    }

    public function getPercentDiscountAttribute()
    {
        $percent = $this->amount_end > 0 ? ($this->amount - $this->amount_end) / $this->amount * 100 : 100;
        return round($percent, 1) . '%';
    }

    public function getTotalBillsTuitionAttribute()
    {
        $totalBillTuition = 0;
        foreach ($this->bills as $bill) {
            if ($bill->transaction_type == BillTransaction::TUITITION && $bill->bill_type == BillType::RECEIVE && $bill->active == BillActive::ACCEPT) {
                $totalBillTuition++;
            }
        }

        return $totalBillTuition;
    }

    public function getDaysDebtAttribute()
    {
        if ($this->paid < $this->amount_end) {
            $date = Carbon::now();
        } else {
            $lastBill = end($this->bills);
            $date = Carbon::parse($lastBill->created_at);
        }

        $createdAt = Carbon::parse($this->date_contract);
        $daysDebt = $createdAt->diffInDays($date);

        return $daysDebt;
    }

    public function getNextDayDebtAttribute()
    {
        $firstDebt = $this->debts->first();
        return $firstDebt ? $firstDebt->date : null;
    }

    public function getNextAmountDebtAttribute()
    {
        $firstDebt = $this->debts->first();
        return $firstDebt ? $firstDebt->amount : null;
    }
}
