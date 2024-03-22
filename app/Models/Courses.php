<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategories::class, 'cat_id', 'id');
    }

    public function products()
    {
        return $this->belongsToMany(Products::class,'product_course','course_id','product_id');
    }
}
