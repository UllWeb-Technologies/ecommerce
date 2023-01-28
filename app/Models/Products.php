<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sub_product;

class Products extends Model
{
     protected $table= 'products';
    
    public $timestamps= False;
    
    protected $fillable=[
        'product_name',
        'product_image',
        'stock',
        'product_price',
        'product_reviews',
        'parent_category_id',
        'sub_category_id',
        'created_date',
        'created_time',
        'product_type',
        'product_quantity'
        ];
    
    public function get_images(){
         return $this->hasMany(Sub_product::class,'product_id')->select('id','image_url')->get();
    }
}