<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessMarket extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function volume()
    {
        return $this->hasMany(BusinessMarketVolume::class, 'market_id', 'id');
    }
    public function facebook()
    {
        return $this->hasMany(BusinessMarketFacebook::class, 'market_id', 'id');
    }
    public function history()
    {
        return $this->hasMany(BusinessMarketHistory::class, 'market_id', 'id');
    }

    public function campuses()
    {
        return $this->hasOne(Campuses::class, 'id', 'campuses_id');
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
