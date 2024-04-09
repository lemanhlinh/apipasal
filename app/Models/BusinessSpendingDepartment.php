<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSpendingDepartment extends Model
{
    use HasFactory;
    protected $table = 'business_spending_department';
    protected $guarded = ['id'];

    public function departments()
    {
        return $this->hasMany(Department::class, 'department_id', 'id');
    }
}
