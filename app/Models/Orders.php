<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Orders extends Model
{
     protected $table= 'orders';
    
    public $timestamps= False;
    
    protected $fillable=[
        'product_id',
        'product_name',
        'product_price',
        'image_url',
        'owner_id',
        'customer_name',
        'customer_id',
        'shipping_phone_number',
        'amount_ordered',
        'state',
        'destination_address',
        'date',
        'time',
        'paid_date',
        ];
        
}