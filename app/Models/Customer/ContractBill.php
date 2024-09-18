<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractBill extends Model
{
    use HasFactory;
    protected $table = 'customer_contract_bills';
    protected $guarded = ['id'];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }
}
