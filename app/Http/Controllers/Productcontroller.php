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


       //list of product for admin
       public function admin_product_list(Request $request){
        $filter=$request->filter;
                $token = JWTAuth::parseToken()->getPayload()->toArray();
                $id=$token['id'];
        if($filter == "In stock"){
             $products=Products::where('owner_id',$id)->where('stock',"In stock")->paginate(10)->toArray();
            $product_array=array();
            foreach($products['data'] as $product){
                    $product_id=$product['id'];
                    $product_image= Sub_product::where('product_id',$product_id)->first();
                    $image_url=$product_image->image_url;
                    $product_order_count=Orders::where('product_id',$product_id)->count();
                    $product_amount_made= Orders::where('product_id',$product_id)->sum('product_price');
                    $date_time_created= $product['created_date'].','.$product['created_time'];
                    $aprove_status= "";
                    if($product['product_aprove_status'] == true){
                       $aprove_status= "Approved";
                    }else{
                        $aprove_status= "Not Approved";
                    }
                    $product_list=[
                        "id"=>$product['id'],
                        "product_name"=>$product['product_name'],
                         "product_price"=>$product['product_price'],
                        "product_image"=>$image_url,
                          "product_stock"=>$product['stock'],
                        "approved_status"=>$aprove_status,
                        "product_amount_sold"=> $product_order_count,
                        "product_amount_made"=> $product_amount_made,
                        "date_time"=>$date_time_created,
                        "product_stock_quantity"=> $product['product_quantity']
                        ];
                    array_push($product_array, $product_list);
            }
            return response()->json(["current_page"=>$products['current_page'],"data"=>$product_array,"first_page_url"=>$products['first_page_url'],"last_page_url"=>$products['last_page_url'],"next_page_url"=>$products['next_page_url'],"prev_page_url"=>$products['prev_page_url'],"per_page"=>$products['per_page'],"total"=>$products['total']]);
        }elseif($filter == "Out of stock"){
                $products=Products::where('owner_id',$id)->where('stock',"Out of stock")->paginate(10)->toArray();
            $product_array=array();
            foreach($products['data'] as $product){
                
                    $product_id=$product['id'];
                    $product_image= Sub_product::where('product_id',$product_id)->first();
                    $image_url=$product_image->image_url;
                    $product_order_count=Orders::where('product_id',$product_id)->count();
                    $product_amount_made= Orders::where('product_id',$product_id)->sum('product_price');
                    $date_time_created= $product['created_date'].','.$product['created_time'];
                     $aprove_status= "";
                     if($product['product_aprove_status'] == true){
                       $aprove_status= "Approved";
                    }else{
                        $aprove_status= "Not Approved";
                    }
                    
                     $product_list=[
                        "id"=>$product['id'],
                        "product_name"=>$product['product_name'],
                         "product_price"=>$product['product_price'],
                        "product_image"=>$image_url,
                          "product_stock"=>$product['stock'],
                        "approved_status"=>$aprove_status,
                        "product_amount_sold"=> $product_order_count,
                        "product_amount_made"=> $product_amount_made,
                        "date_time"=>$date_time_created,
                        "product_stock_quantity"=> $product['product_quantity']
                        ];
                    array_push($product_array, $product_list);
            }
            return response()->json(["current_page"=>$products['current_page'],"data"=>$product_array,"first_page_url"=>$products['first_page_url'],"last_page_url"=>$products['last_page_url'],"next_page_url"=>$products['next_page_url'],"prev_page_url"=>$products['prev_page_url'],"per_page"=>$products['per_page'],"total"=>$products['total']]);
        }elseif($filter == "approved"){
               $products=Products::where('owner_id',$id)->where('product_aprove_status',true)->paginate(10)->toArray();
            $product_array=array();
              foreach($products['data'] as $product){
                
                    $product_id=$product['id'];
                    $product_image= Sub_product::where('product_id',$product_id)->first();
                    $image_url=$product_image->image_url;
                    $product_order_count=Orders::where('product_id',$product_id)->count();
                    $product_amount_made= Orders::where('product_id',$product_id)->sum('product_price');
                    $date_time_created= $product['created_date'].','.$product['created_time'];
                     $aprove_status= "";
                     
                     if($product['product_aprove_status'] == true){
                       $aprove_status= "Approved";
                    }else{
                        $aprove_status= "Not Approved";
                    }
                    
                     $product_list=[
                        "id"=>$product['id'],
                        "product_name"=>$product['product_name'],
                         "product_price"=>$product['product_price'],
                        "product_image"=>$image_url,
                          "product_stock"=>$product['stock'],
                        "approved_status"=>$aprove_status,
                        "product_amount_sold"=> $product_order_count,
                        "product_amount_made"=> $product_amount_made,
                        "date_time"=>$date_time_created,
                        "product_stock_quantity"=> $product['product_quantity']
                        ];
                    array_push($product_array, $product_list);
            }
            return response()->json(["current_page"=>$products['current_page'],"data"=>$product_array,"first_page_url"=>$products['first_page_url'],"last_page_url"=>$products['last_page_url'],"next_page_url"=>$products['next_page_url'],"prev_page_url"=>$products['prev_page_url'],"per_page"=>$products['per_page'],"total"=>$products['total']]);
        }elseif($filter == "not approved"){
               $products=Products::where('owner_id',$id)->where('product_aprove_status',false)->paginate(10)->toArray();
            $product_array=array();
               foreach($products['data'] as $product){
                
                    $product_id=$product['id'];
                    $product_image= Sub_product::where('product_id',$product_id)->first();
                    $image_url=$product_image->image_url;
                    $product_order_count=Orders::where('product_id',$product_id)->count();
                    $product_amount_made= Orders::where('product_id',$product_id)->sum('product_price');
                    $date_time_created= $product['created_date'].','.$product['created_time'];
                     $aprove_status= "";
                     if($product['product_aprove_status'] == true){
                       $aprove_status= "Approved";
                    }else{
                        $aprove_status= "Not Approved";
                    }
                    
                     $product_list=[
                        "id"=>$product['id'],
                        "product_name"=>$product['product_name'],
                         "product_price"=>$product['product_price'],
                        "product_image"=>$image_url,
                          "product_stock"=>$product['stock'],
                        "approved_status"=>$aprove_status,
                        "product_amount_sold"=> $product_order_count,
                        "product_amount_made"=> $product_amount_made,
                        "date_time"=>$date_time_created,
                        "product_stock_quantity"=> $product['product_quantity']
                        ];
                    array_push($product_array, $product_list);
            }
            return response()->json(["current_page"=>$products['current_page'],"data"=>$product_array,"first_page_url"=>$products['first_page_url'],"last_page_url"=>$products['last_page_url'],"next_page_url"=>$products['next_page_url'],"prev_page_url"=>$products['prev_page_url'],"per_page"=>$products['per_page'],"total"=>$products['total']]);
        }

           
    }

    //admin delete product
    public function admin_delete_product(Products $product){
            
        $id=$product->id;
        $check_orders= Orders::where('product_id',$id)->get();
        
        if($check_orders){
               return response()->json([
                "status"=>"failed",
                "message"=>"You cant delete this product because it has be ordered by a customer"
            ]); 
        }else{
            $product->delete();
        Sub_product::where('product_id',$id)->delete();
        
        return response()->json([
            "status"=>"success"
            ]); 
        }
       
}


//admin create product
public function create_product(Request $request){
    $validator = Validator::make($request->all(), [
       'product_name' => 'required',
       'stock' => 'required',
       'product_price' => 'required',
       
       'parent_category_id' => 'required',
       'sub_category_id' => 'required',
       'image'=>'required',
   
       'product_type'=>'required',
       'product_quantity'=>'required'
       
   ]);

   if($validator->fails()){
           return response()->json($validator->errors(), 400);
   }else{
        $carbondate=Carbon::now();
        $createdate = $carbondate->toDateString();
        $createtime= $carbondate->now('UTC')->setTimezone('WAT')->format('g:i:s a');
        $serialize_eye_size= serialize($request->eye_size);
        
       $uniqueid=uniqid();
           $token = JWTAuth::parseToken()->getPayload()->toArray();
           $id=$token['id'];
           
           
            $create_product=Products::create([
               'product_name'=>$request->product_name,
               'stock'=>$request->stock,
               'product_price'=>$request->product_price,
               'parent_category_id'=>$request->parent_category_id,
               'sub_category_id'=>$request->sub_category_id,
               'brand'=>$request->brand,
               'created_date'=>$createdate,
               'created_time'=>$createtime,
               'eye_size'=>$serialize_eye_size,
               'owner_id'=>$id,
               'product_type'=>$request->product_type,
               'product_details'=>$request->product_details,
               'product_specifications'=>$request->product_specifications,
               'product_quantity'=>$request->product_quantity
            ]);
             $image=$request->image;
    
            
                  $uniqueid=uniqid();
       $imgextention=$image->getClientOriginalExtension();
        $name=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$imgextention;
        $file= $image->move(
                   public_path('pictures/'), $name
               );
        $base_url="https://codesandbox.com.ng/market_for_opticals/public/pictures/";
               $image=$base_url.$name;
        
        Sub_product::create([
                  "image_url"=>$image,
                  "product_id"=> $create_product->id
            ]);
         $get_admin_details = Admin::where('owner_id',$id)->first();
      Notification::create([
                "notification_date"=>$createdate,
                "notification_name"=>$get_admin_details->company_name ." "."just uploaded a product",
                "notification_type"=>"product",
                "notification_id"=>$create_product->id,
                "owner_id"=>""
              ]);
       
       return response()->json([
           "product_id"=>$create_product->id,
           "status"=>"success",
           "message"=>"product created successfully"
           ]);
       
       
       
           
        
   }
}


 //admin product update
 public function update_product(Request $request){
                   
    $validator = Validator::make($request->all(), [
        'product_name' => 'required',
        'stock' => 'required',
        'product_price' => 'required',
        'parent_category_id' => 'required',
        'sub_category_id' => 'required',
        'image'=>'required',
        'eye_size'=>'required',
        'product_type'=>'required',
        'product_details'=>'required',
        'product_specifications'=>'required',
        'product_id'=>'required'
    ]);

    if($validator->fails()){
            return response()->json($validator->errors(), 400);
    }else{
         $carbondate=Carbon::now();
         $createdate = $carbondate->toDateString();
         $createtime= $carbondate->now('UTC')->setTimezone('WAT')->format('g:i:s a');
         $serialize_eye_size= serialize($request->eye_size);
         $product_id = $request->product_id;
         
        $uniqueid=uniqid();
            $token = JWTAuth::parseToken()->getPayload()->toArray();
            $id=$token['id'];
            
            
             $create_product=Products::where('id',$product_id)->update([
                'product_name'=>$request->product_name,
                'stock'=>$request->stock,
                'product_price'=>$request->product_price,
                'parent_category_id'=>$request->parent_category_id,
                'sub_category_id'=>$request->sub_category_id,
                'brand'=>$request->brand,
                'created_date'=>$createdate,
                'created_time'=>$createtime,
                'eye_size'=>$serialize_eye_size,
                'owner_id'=>$id,
                'product_aprove_status'=>false,
                'product_type'=>$request->product_type,
                'product_details'=>$request->product_details,
                'product_specifications'=>$request->product_specifications
             ]);
              $create_product=Products::where('id',$product_id)->get();
             
             if($request->hasFile('image')){
                   $image=$request->image;
     
             
                   $uniqueid=uniqid();
        $imgextention=$image->getClientOriginalExtension();
         $name=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$imgextention;
         $file= $image->move(
                    public_path('pictures/'), $name
                );
         $base_url="https://codesandbox.com.ng/market_for_opticals/public/pictures/";
                $image=$base_url.$name;
         
            Sub_product::where('id',$request->product_id)->update([
                   "image_url"=>$image,
                  
             ]);
             }else{
                   Sub_product::where('id',$request->product_id)->update([
                   "image_url"=>$request->image,
                  
             ]);
             }
            $get_admin_details = Admin::where('owner_id',$id)->first();
              Notification::create([
                 "notification_date"=>$createdate,
                 "notification_name"=>$get_admin_details->company_name ." "."just updated a product",
                 "notification_type"=>"product",
                 "notification_id"=>$create_product->id,
                 "owner_id"=>""
               ]);
        return response()->json([
            
            "status"=>"success",
            "message"=>"product updated successfully"
            ]);
        
    }
           
}




}