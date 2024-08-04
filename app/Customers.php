<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'payment_service', 'payment_service_customer_id'];

    public static function findCustomerByEmail($email): ?Customers
    {
       return Customers::where('email', $email)->first();
    }
}
