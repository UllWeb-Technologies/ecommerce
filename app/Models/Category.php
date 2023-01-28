<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    protected $table= 'category';
    
    public $timestamps= False;
    
    protected $fillable=[
        'name',
        'parent_id',
        ];
    
    // public function get_children($id){
    //     $allcategory=Category::get();
        
        
    //     $children=Category::where('parent_id',$id)->get();
        
    //     self::formatcategory($children,$allcategory);
        
    //     return $children;
    // }
    // private static function formatcategory($children, $allcategory){
    //      foreach($children as $child){
    //          $child->children=$allcategory->where('parent_id',$child->id)->values();
             
    //          if(!empty($child)){
    //              self::formatcategory($child->children,$allcategory);
    //          }
    //      }
    // }
    
}