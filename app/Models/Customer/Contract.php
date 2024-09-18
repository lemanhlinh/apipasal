<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\ProductCategories;
use App\Models\BusinessPolicy;

class Contract extends Model
{
    use HasFactory;
    protected $table = 'customer_contracts';
    protected $guarded = ['id'];

    public function bills()
    {
        return $this->hasMany(ContractBill::class, 'contract_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function product_category()
    {
        return $this->belongsTo(ProductCategories::class, 'product_category_id', 'id');
    }

    public function management()
    {
        return $this->belongsTo(User::class, 'manage_id', 'id');
    }

    public function special()
    {
        return $this->belongsTo(BusinessPolicy::class, 'special_id', 'id');
    }

    public function promtion()
    {
        return $this->belongsTo(BusinessPolicy::class, 'promotion_id', 'id');
    }
}
