<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Product_review extends Model
{
     protected $table= 'product_reviews';
    
    public $timestamps= False;
 
    protected $fillable=[
        'reviews',
        'user_id',
        'product_id',
        'ratings',
       
        ];
    
}