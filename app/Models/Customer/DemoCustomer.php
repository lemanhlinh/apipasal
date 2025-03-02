<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class DemoCustomer extends Model
{
    use HasFactory;

    protected $table = 'customer_demo_customer';
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function demo()
    {
        return $this->belongsTo(Demo::class, 'demo_id');
    }
}
