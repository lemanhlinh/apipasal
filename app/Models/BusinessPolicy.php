<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPolicy extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'business_policy';
    protected $filable = [
        'title',
        'type_promotion',
        'promotion',
        'date_start',
        'date_end',
        'active',
        'type'
    ];

    public function campuses()
    {
        return $this->belongsToMany(Campuses::class, 'business_policy_campuses', 'business_policy_id', 'campuses_id');
    }

    public function businessPolicyProducts()
    {
        return $this->hasMany(BusinessPolicyProduct::class, 'business_policy_id');
    }

}
