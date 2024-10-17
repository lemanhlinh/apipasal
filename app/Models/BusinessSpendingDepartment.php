<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSpendingDepartment extends Model
{
    use HasFactory;
    protected $table = 'business_spending_department';
    protected $guarded = ['id'];

    protected $casts = [
        'kpi_doanh_thu' => 'integer',
        'kpi_hoc_vien' => 'integer',
        'kpi_dataKH' => 'integer',
        'kpi_ty_le_chot' => 'integer'
    ];
    public function departments()
    {
        return $this->hasMany(Department::class, 'department_id', 'id');
    }
}
