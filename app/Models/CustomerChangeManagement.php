<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerChangeManagement extends Model
{
    use HasFactory;
    protected $table = 'customer_change_management';
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(CustomerCustomer::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
