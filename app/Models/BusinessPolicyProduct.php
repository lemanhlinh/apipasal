<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPolicyProduct extends Model
{
    use HasFactory;

    protected $table = 'business_policy_product';

    public function products()
    {
        return $this->belongsToMany(Products::class);
    }
}
