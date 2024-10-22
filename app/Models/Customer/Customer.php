<?php

namespace App\Models\Customer;

use App\Models\BusinessPartner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\BusinessSettingSourceCustomer;
use App\Models\Cities;
use App\Models\Countries;
use App\Models\Districts;
use App\Constants\Customer\Source;
use App\Models\ProductCategories;
use App\Models\Products;
use Illuminate\Support\Facades\Log;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customer_customer';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'manage_id', 'id');
    }

    public function management()
    {
        return $this->belongsTo(User::class, 'manage_id');
    }

    public function source()
    {
        switch ($this->source_type_id) {
            case Source::OTHER:
                return $this->belongsTo(BusinessSettingSourceCustomer::class, 'source_id', 'id');
            case Source::PARTNER:
                return $this->belongsTo(BusinessPartner::class, 'source_id', 'id');
            case Source::STUDENT:
                return $this->belongsTo(Student::class, 'source_id', 'id');
            default:
                return null;
        }
    }

    public function segment()
    {
        return $this->hasMany(CustomerSegment::class, 'customer_id', 'id'); 
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'customer_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }

    public function city()
    {
        return $this->belongsTo(Cities::class, 'city_id', 'code');
    }

    public function district()
    {
        return $this->belongsTo(Districts::class, 'district_id', 'code');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function product_category()
    {
        return $this->belongsTo(ProductCategories::class, 'product_category_id');
    }
}
