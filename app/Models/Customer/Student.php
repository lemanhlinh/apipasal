<?php

namespace App\Models\Customer;

use App\Models\BusinessMarket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $table = 'customer_students';
    protected $guarded = ['id'];
    protected $appends = [
        'classes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function segment()
    {
        return $this->belongsTo(CustomerSegment::class, 'customer_segment_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'student_id', 'id');
    }

    public function getClassesAttribute()
    {
        $classes = [];
        foreach ($this->contracts as $contract) {
            if ($contract->classes){
                foreach ($contract->classes as $class) {
                    $classes[] = $class;
                }
            }
        }

        return $classes;
    }
}
