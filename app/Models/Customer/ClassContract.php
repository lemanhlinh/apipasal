<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\TimeStudy;
use App\Models\DayShiftLearn;
use App\Models\CalendarLearn;

class ClassContract extends Model
{
    use HasFactory;
    protected $table = 'customer_class_contract';
    protected $guarded = ['id'];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function time_study()
    {
        return $this->belongsTo(TimeStudy::class, 'time_study_id');
    }

    public function day_shift_learn()
    {
        return $this->belongsTo(DayShiftLearn::class, 'day_shift_learn_id');
    }

    public function calendar_learn()
    {
        return $this->belongsTo(CalendarLearn::class, 'calendar_learn_id');
    }
}
