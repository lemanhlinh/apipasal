<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSettingSourceCustomer extends Model
{
    use HasFactory;
    protected $table = 'business_setting_source_customer';
    protected $guarded = ['id'];
}
