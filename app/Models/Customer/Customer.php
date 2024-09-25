<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\BusinessSettingSourceCustomer;
use App\Models\Cities;
use App\Models\Countries;
use App\Models\Districts;
use App\Constants\Customer\Source;
use App\Models\BusinessPartner;

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

    public function source_info()
    {
        switch ($this->source) {
            case Source::OTHER:
                return $this->belongsTo(BusinessSettingSourceCustomer::class, 'source_detail', 'id');
            case Source::PARTNER:
                return $this->belongsTo(BusinessPartner::class, 'source_detail', 'id');
            case Source::STUDENT:
                return $this->belongsTo(Student::class, 'source_detail', 'id');
            default:
                return null;
        }
    }

    public function segment_info()
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
}
