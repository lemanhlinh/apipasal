<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPolicy extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'business_policy';

    public function campuses()
    {
        return $this->belongsToMany(Campuses::class);
    }

    public function businessPolicyProduct()
    {
        return $this->hasMany(BusinessPolicyProduct::class);
    }
}
