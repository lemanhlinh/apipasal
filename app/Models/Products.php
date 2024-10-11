<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function cat()
    {
        return $this->belongsTo(ProductCategories::class,'cat_id','id');
    }

    public function courses()
    {
        return $this->belongsToMany(Courses::class,'product_course','product_id','course_id')->withPivot('ordering');
    }

    public function businessPolicyProducts()
    {
        return $this->hasMany(BusinessPolicyProduct::class, 'product_id');
    }
}
