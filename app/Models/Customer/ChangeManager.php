<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class ChangeManager extends Model
{
    use HasFactory;
    protected $table = 'customer_change_manager';
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function new_user()
    {
        return $this->belongsTo(User::class, 'new_user_id')->with('department.campuses');
    }

    public function old_user()
    {
        return $this->belongsTo(User::class, 'old_user_id')->with('department.campuses');
    }
}
