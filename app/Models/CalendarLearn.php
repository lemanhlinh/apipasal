<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarLearn extends Model
{
    use HasFactory;

    protected $casts = [
      'days' => 'array'
    ];
    protected $guarded = ['id'];
    protected $table = 'calendar_learn';
}
