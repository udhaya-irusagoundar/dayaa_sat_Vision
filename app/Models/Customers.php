<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Customers extends Model
{
    use HasFactory,HasApiTokens;

    protected $table = 'customers';
      protected $connection = 'multi';
    protected $fillable = [
        'customerNumber',//(Here the customerNumber is taken as the Box Number)
        'name',
        'mobileNumber',
        'place',
        'amount',    
        'totalAmount',
         'last_paid_by',
        'weeksPaid',
        'paymentDates',
    ];

    protected $casts = [
        'weeksPaid' => 'array',
        'paymentDates' => 'array',
    ];
}