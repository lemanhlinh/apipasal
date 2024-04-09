<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSpending extends Model
{
    use HasFactory;

    protected $table = 'business_spending';
    protected $guarded = ['id'];

    public function spendingCampuses()
    {
        return $this->hasMany(BusinessSpendingDepartment::class, 'spending_id', 'id');
    }
}
