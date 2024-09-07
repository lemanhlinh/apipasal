<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCustomer extends Model
{
    use HasFactory;
    protected $table = 'customer_customer';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'manage_id', 'id');
    }
}
