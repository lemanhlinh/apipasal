<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDemo extends Model
{
    use HasFactory;

    protected $table = 'customer_demo_customer';
    protected $guarded = ['id'];
    
    public function user_manage()
    {
        return $this->belongsTo(User::class, 'user_manage_id');
    }

    public function demo()
    {
        return $this->belongsTo(BusinessSettingDemoExperience::class, 'demo_id');
    }

    public function campuses()
    {
        return $this->hasOne(Campuses::class, 'id', 'campuses_id');
    }


    public function demo_customer()
    {
        return $this->hasMany(CustomerDemoCustomer::class, 'demo_id', 'id');
    }

    // public function speaker()
    // {
    //     return $this->belongsTo(Speaker::class, 'speaker_id');
    // }
}
