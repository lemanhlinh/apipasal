<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessMarketVolume extends Model
{
    use HasFactory;

    protected $table = 'business_market_volume';

    public function market()
    {
        return $this->belongsTo(BusinessMarket::class, 'market_id');
    }
}
