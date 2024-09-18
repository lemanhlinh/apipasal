<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoCustomer extends Model
{
    use HasFactory;

    protected $table = 'customer_demo_customer';
    protected $guarded = ['id'];
}
