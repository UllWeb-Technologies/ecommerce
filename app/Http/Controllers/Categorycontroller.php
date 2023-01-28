<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Category;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;


class Categorycontroller extends Controller
{
    
    public function get_parent_category(){
        $category=Category::select('id','name')->whereNull('parent_id')->get();
        
        return response()->json($category);
    }
    public function get_subcategories(Request $request){
              $id=$request->query('id');
              $subcategory=Category::where('parent_id',$id)->get();
              
             
             $results=[
                 "sub_category"=>$subcategory,
                
                 ];
              return response()->json($results);
    }
    
    
    //create category for superadmin
    public function create_category(Request $request){
                    $parent_category = $request->parent_category;
                    $sub_category =  $request->sub_category;
                    $child_sub_category = $request->child_sub_category;
                    
                    if($parent_category){
                          Category::create([
                              "name"=>$parent_category
                              ]);
                    }elseif($parent_category && $sub_category){
                          $category = Category::create([
                              "name"=>$parent_category
                              ]);
                              
                             Category::create([
                              "name"=>$sub_category,
                              "parent_id"=>$category->id
                              ]);
                              
                              return response()->json([
                                  "status" => "success"
                                  ]);
                    }elseif($parent_category && $sub_category && $child_sub_category){
                            $category=  Category::create([
                              "name"=>$parent_category
                              ]);
                              
                              $category1= Category::create([
                              "name"=>$sub_category,
                              "parent_id"=>$category->id
                              ]);
                              
                               $category2= Category::create([
                              "name"=>$child_sub_category,
                              "parent_id"=>$category->id
                              ]);
                              
                              return response()->json([
                                  "status" => "success"
                                  ]);
                    }else{
                        return response()->json([
                                  "status" => "failed"
                                  ]);
                    }
    }
    
    
    
    
    
}