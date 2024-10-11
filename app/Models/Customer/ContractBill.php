<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class ContractBill extends Model
{
    use HasFactory;
    protected $table = 'customer_contract_bills';
    protected $guarded = ['id'];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    public function user_create()
    {
        return $this->belongsTo(User::class, 'user_create_id', 'id');
    }

    public function user_accept()
    {
        return $this->belongsTo(User::class, 'user_accept_id', 'id');
    }

    public function getStudentAttribute()
    {
        return $this->contract->student ?? null;
    }

    public function getCustomerAttribute()
    {
        return $this->contract->student->customer ?? null;
    }

    public function getSegmentAttribute()
    {
        return $this->contract->student->segment ?? null;
    }
}
