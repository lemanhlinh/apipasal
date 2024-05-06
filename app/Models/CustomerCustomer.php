<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCustomer extends Model
{
    use HasFactory;
    protected $table = 'customer_customer';
    protected $guarded = ['id'];


    public function clue()
    {
        return $this->hasMany(BusinessPartnerClue::class, 'partner_id', 'id');
    }

    public function campuses()
    {
        return $this->hasOne(Campuses::class, 'id', 'campuses_id');
    }
}
