<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';

    public function role_permission() {
        return $this->hasOne(Role::class, 'permission_id', 'id');
    }
}
