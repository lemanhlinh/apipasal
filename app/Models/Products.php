<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    public function cat()
    {
        return $this->belongsTo(ProductCategories::class,'cat_id','id');
    }

    public function productCourses()
    {
        return $this->hasMany(ProductCourse::class,'product_id','id');
    }
}
