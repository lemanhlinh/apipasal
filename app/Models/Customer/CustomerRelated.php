<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRelated extends Model
{
    use HasFactory;
    protected $table = 'customer_related';
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function related()
    {
        return $this->belongsTo(Customer::class, 'related_id');
    }
}
