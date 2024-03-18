<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayShiftLearn extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'day_shift_learn';
}
