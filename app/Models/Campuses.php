<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CampusesDepartment;

class Campuses extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $guarded = ['id'];

    public function classrooms()
    {
        return $this->hasMany(CampusesClassroom::class, 'campuses_id', 'id');
    }


    public function campusDepartment()
    {
        return $this->hasMany(CampusesDepartment::class, 'campuses_id');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }

    public function businessPolicy()
    {
        return $this->belongsToMany(BusinessPolicy::class);
    }
}
