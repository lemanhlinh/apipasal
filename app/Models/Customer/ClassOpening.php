<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassOpening extends Model
{
    use HasFactory;

    protected $table = 'customer_class_opening';
    protected $guarded = ['id'];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}
