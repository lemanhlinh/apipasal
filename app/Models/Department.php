<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Department extends Model
{
    use HasFactory;
    use NodeTrait;
    protected $guarded = ['id'];
    protected $table = 'department';

    public function regencies()
    {
        return $this->hasMany(Regencies::class, 'department_id', 'id');
    }

    public function user_manage()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'department_id', 'id');
    }

    public function campuses()
    {
        return $this->belongsToMany(Campuses::class);
    }
}
