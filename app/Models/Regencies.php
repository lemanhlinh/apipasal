<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regencies extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'title',
        'code',
        'active'
    ];
    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }
}
