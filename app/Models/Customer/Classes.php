<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Courses;
use App\Models\CalendarLearn;
use App\Models\DayShiftLearn;
use App\Models\TimeStudy;
use App\Models\User;
use App\Models\Campuses;
use App\Models\CampusesClassroom;
use App\Models\CourseCategories;
use App\Models\Customer\ClassOpening;

class Classes extends Model
{
    use HasFactory;
    protected $table = 'customer_classes';
    protected $guarded = ['id'];
    protected $appends = ['day_opening'];

    public function class()
    {
        return $this->belongsTo(CampusesClassroom::class, 'class_id');
    }

    public function course()
    {
        return $this->belongsTo(Courses::class, 'course_id');
    }

    public function course_category()
    {
        return $this->belongsTo(CourseCategories::class, 'course_category_id');
    }

    public function calendar_learn()
    {
        return $this->belongsTo(CalendarLearn::class, 'calendar_learn_id');
    }

    public function day_shift_learn()
    {
        return $this->belongsTo(DayShiftLearn::class, 'day_shift_learn_id');
    }

    public function time_study()
    {
        return $this->belongsTo(TimeStudy::class, 'time_study_id');
    }

    public function user_admission()
    {
        return $this->belongsTo(User::class, 'user_admission_id');
    }

    public function campuse()
    {
        return $this->belongsTo(Campuses::class, 'campuse_id');
    }

    public function opening()
    {
        return $this->hasMany(ClassOpening::class, 'class_id');
    }

    public function getDayOpeningAttribute()
    {
        $dayOpening = collect($this->opening)->sum('days');
        return $dayOpening;
    }
}
