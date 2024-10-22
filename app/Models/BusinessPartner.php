<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPartner extends Model
{
    use HasFactory;
    protected $table = 'business_partners';
    protected $guarded = ['id'];

    protected $fillable = [
        'title',
        'phone',
        'email',
        'type',
        'type_campuses',
        'segment',
        'info_partner',
        'campuses_id',
        'active'
    ];
    public function clue()
    {
        return $this->hasMany(BusinessPartnerClue::class, 'partner_id', 'id');
    }

    public function campuses()
    {
        return $this->hasOne(Campuses::class, 'id', 'campuses_id');
    }
}
