<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Customer_transaction extends Model
{
     protected $table= 'customers_transactions';
    
    public $timestamps= False;
    
    protected $fillable=[
        'items_bought',
        'total_amount',
        'customer_id',
        'date',
        'time',
        ];
        
}