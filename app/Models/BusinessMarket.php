<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessMarket extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function volumes()
    {
        return $this->hasMany(BusinessMarketVolume::class, 'market_id', 'id');
    }
    public function getTotalVolumeAttribute()
    {
        return $this->volumes->sum('total_student');
    }
    public function facebook()
    {
        return $this->hasMany(BusinessMarketFacebook::class, 'market_id', 'id');
    }
    public function histories()
    {
        return $this->hasMany(BusinessMarketHistory::class, 'market_id', 'id');
    }

    public function cities()
    {
        return $this->belongsTo(City::class, 'city_id','code');
    }

    public function districts()
    {
        return $this->belongsTo(District::class, 'district_id','code');
    }
}
