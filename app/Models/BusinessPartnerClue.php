<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPartnerClue extends Model
{
    use HasFactory;

    protected $table = 'business_partner_clue';
    protected $guarded = ['id'];
}
