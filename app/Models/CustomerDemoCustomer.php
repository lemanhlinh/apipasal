<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDemoCustomer extends Model
{
    use HasFactory;

    protected $table = 'customer_demo_customer';
    protected $guarded = ['id'];
}
