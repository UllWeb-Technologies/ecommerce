<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Products;
use App\Models\Product_review;

use App\Models\Orders;
use App\Models\Category;


use Illuminate\Http\Request;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Carbon\Carbon;
class Productcontroller extends Controller
{   
    
    
    //get sub categories
    
    public function get_subcategory(Request $request){
                 $cat_id= $request->id;
                  
               $sub_category=  Category::where('parent_id',$cat_id)->get();
               
               return response()->json($sub_category);
    }
       public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator(array_values($items->forPage($page, $perPage)->toArray()), $items->count(), $perPage, $page, $options);
    }


    //---------------------------------------------------
    
    //get list of product for customer
    public function get_user_product(Request $request){
        $parent_cat_id= $request->query('parent_cat_id');
        
        $sub_category= $request->query('sub_category');
        if($parent_cat_id){
            
            $products= Products::where('parent_category_id',$parent_cat_id)->where('product_aprove_status',true)->get();

            $array_values= array();
            foreach($products as $product){
                 $product_owner_account = Admin::where('id',$product->owner_id)->first();
                      if($product_owner_account->active == false){
                          break;
                      }else{
                     
                $id=$product->id;
                $pq = $product->product_quantity;
                if($pq == 0){
                    
                        Products::where('id',  $id)->update([
                           "stock"=>false
                            ]);
                            
                    
                }else{
                        $num_of_review = Product_review::where('product_id', $product->id)->get()->count();
                        $sum_product_ratings = Product_review::where('product_id', $product->id)->sum('ratings');
                        $rating =0;
                        if($num_of_review > 0){
                            $rating = (int)$sum_product_ratings / (int)$num_of_review ;
                        }
                     $related_products = Products::select('id','product_name','product_price','product_image','stock')->where('parent_category_id', $product->parent_category_id)->inRandomOrder()->limit(10)->get();    
               
                // $product_images= $product->get_images();
                   $array1=[
                       "id"=>$product->id,
                        "owner_id"=>$product->owner_id,
                       "stock"=>$product->stock,
                "product_name" => $product->product_name,
                "product_price" => $product->product_price,
                "product_image"=>$product->product_image,
                "rating"=>$rating,
                "product_details"=> $product->product_details,
                "related_products"=> $related_products
                       
                       ];
                       
                       
                       array_push($array_values, $array1);
                }
            
            }}
            $paginated_data = $this->paginate($array_values);
            return response()->json($paginated_data);
            
            
        }elseif($sub_category){
               
                           
            $products= Products::where('sub_category_id',$sub_category)->where('product_aprove_status',true)->get();
           
            $array_values= array();
            foreach($products as $product){
                 $product_owner_account = Admin::where('id',$product->owner_id)->first();
                      if($product_owner_account->active == false){
                          break;
                      }else{
                     
                $id=$product->id;
                $pq = $product->product_quantity;
                if($pq == 0){
                    
                        Products::where('id',  $id)->update([
                           "stock"=>false
                            ]);
                            
                    
                }else{
                        $num_of_review = Product_review::where('product_id', $product->id)->get()->count();
                        $sum_product_ratings = Product_review::where('product_id', $product->id)->sum('ratings');
                        $rating =0;
                        if($num_of_review > 0){
                            $rating = (int)$sum_product_ratings / (int)$num_of_review ;
                        }
                        
               $related_products = Products::select('id','product_name','product_price','product_image','stock')->where('parent_category_id', $product->parent_category_id)->inRandomOrder()->limit(10)->get();
                // $product_images= $product->get_images();
                   $array1=[
                       "id"=>$product->id,
                        "owner_id"=>$product->owner_id,
                       "stock"=>$product->stock,
                "product_name" => $product->product_name,
                "product_price" => $product->product_price,
                "product_image"=>$product->product_image,
                "product_details"=> $product->product_details,
                "related_products"=> $related_products,
                "rating"=>$rating
                       
                       ];
                       
                       
                       array_push($array_values, $array1);
                }
            
            }}
            $paginated_data = $this->paginate($array_values);
            return response()->json($paginated_data);
            
        }else{
            
           $products= Products::select()->inRandomOrder()->get();
            
            
            $array_values= array();
            foreach($products as $product){
                 $product_owner_account = Admin::where('id',$product->owner_id)->first();
                      if($product_owner_account->active == false){
                          break;
                      }else{
                     
                $id=$product->id;
                $pq = $product->product_quantity;
                if($pq == 0){
                    
                        Products::where('id',  $id)->update([
                           "stock"=>false
                            ]);
                            
                    
                }else{
                        $num_of_review = Product_review::where('product_id', $product->id)->get()->count();
                        $sum_product_ratings = Product_review::where('product_id', $product->id)->sum('ratings');
                        $rating =0;
                        if($num_of_review > 0){
                            $rating = (int)$sum_product_ratings / (int)$num_of_review ;
                        }
                        
               $related_products = Products::select('id','product_name','product_price','product_image','stock')->where('parent_category_id', $product->parent_category_id)->inRandomOrder()->limit(10)->get();
                // $product_images= $product->get_images();
                   $array1=[
                       "id"=>$product->id,
                        "owner_id"=>$product->owner_id,
                       "stock"=>$product->stock,
                "product_name" => $product->product_name,
                "product_price" => $product->product_price,
                "product_image"=>$product->product_image,
                "product_details"=> $product->product_details,
                "related_products"=> $related_products,
                "rating"=>$rating
                       
                       ];
                       
                       
                       array_push($array_values, $array1);
                }
            
            }}
            
           //$this->paginate($array_values);
            $paginated_data = $this->paginate($array_values);
            return response()->json($paginated_data);
            
            
            }
    }
    
    //search products for customers
    
    public function search_user_products(Request $request){
           $name= $request->query('search');
           
           $data=Products::where('product_name','LIKE', "%{$name}%")->get();
           $paginated_data = $this->paginate($data);
           return response()->json($paginated_data);
    }
    
    
    
       
     
       
    // get product details for all users
    public function get_product_details(Products $product){
           
         
           
           $related_products = Products::select('id','product_name','product_price','product_image','stock')->where('parent_category_id', $product->parent_category_id)->inRandomOrder()->limit(10)->get();
           $num_of_review = Product_review::where('product_id', $product->id)->get()->count();
           $sum_product_ratings = Product_review::where('product_id', $product->id)->sum('ratings');
                     $rating =0;
                        if($num_of_review > 0){
                            $rating = (int)$sum_product_ratings / (int)$num_of_review ;
                        }
           $product_details=[
                "id"=>$product->id,
                "stock"=>$product->stock,
                "product_name" => $product->product_name,
                "product_price" => $product->product_price,
                "product_image"=>$product->product_image,
                "product_details"=> $product->product_details,
                "rating"=>$rating,
                "related_products"=> $related_products
               ];
           return response()->json($product_details);
    }
    
    
    
    
    
   
    //review a product
    public function review_product(Request $request){
          $validator = Validator::make($request->all(), [
                'reviews'=>'required',
                'product_id'=>'required',
                'rating'=>'required'
            ]);

            if($validator->fails()){
                    return response()->json($validator->errors(), 400);
            }else{
         $token = JWTAuth::parseToken()->getPayload()->toArray();
                    $id=$token['id'];
                Product_review::where('user_id', $id)->where('product_id', $request->product_id)->delete();
                 Product_review::create([
                     "ratings"=> $request->rating,
                     "product_id"=> $request->product_id,
                     "reviews"=> $request->reviews,
                     "user_id"=> $id
                     ]);
            
            return response()->json([
                "status"=>"success"
                ]);
    }}
    //get list of review for a particular product
    public function get_product_reviews(Request $request){
                $product_id = $request->product_id;
                $token = JWTAuth::parseToken()->getPayload()->toArray();
                    $id=$token['id'];
                $reviews = Product_review::where('product_id',$product_id)->get();
                
                
               $final_array= array();
               foreach($reviews as $review){
                   $get_user= User::where('id', $review->user_id)->first();
                   $array21 =[
                     "ratings"=> $review->ratings,
                     "product_id"=> $review->product_id,
                     "reviews"=> $review->reviews,
                     "user_name"=> $get_user->name
                       
                       ];
                   array_push($final_array,$array21);
                   
               }
                return response()->json($final_array);   
    }

}