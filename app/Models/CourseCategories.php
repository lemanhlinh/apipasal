<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategories extends Model
{
    use HasFactory;

    public function courses()
    {
        return $this->hasMany(Courses::class, 'cat_id', 'id');
    }
}
