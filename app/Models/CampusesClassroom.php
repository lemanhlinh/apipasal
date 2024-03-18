<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampusesClassroom extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'campuses_classroom';

    public function classrooms()
    {
        return $this->belongsTo(Campuses::class, 'id', 'campuses_id');
    }
}
