<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSettingWareHouseRule extends Model
{
    use HasFactory;
    protected $table = 'business_setting_ware_house_rule';
    protected $guarded = ['id'];
}
