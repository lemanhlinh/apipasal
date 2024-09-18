<?php

namespace App\Models\Customer;

use App\Models\BusinessMarket;
use App\Models\Districts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSegment extends Model
{
    use HasFactory;
    protected $table = 'customer_segments';
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function district()
    {
        return $this->belongsTo(Districts::class, 'district_id', 'code');
    }

    public function market()
    {
        return $this->belongsTo(BusinessMarket::class, 'market_id');
    }
}
