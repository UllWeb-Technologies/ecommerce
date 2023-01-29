<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Products;

use App\Models\Product_review;
use App\Models\Customer_transaction;



use App\Jobs\Orderreceipt;

use App\Models\Orders;

use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\Validator;

use Tymon\JWTAuth\Facades\JWTAuth;

use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
class Orderscontroller extends Controller
{
    
       public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
    
    
    
      //pay with paysatck
    public function initialize_payments(Request $request){
        
        $email= $request->email;
        $amount=$request->amount;
        
        
        $service_charge=(int)$amount * 0.015 + 100;
        
        if($service_charge > 2500){
            $service_charge= 2500;
        }
        $paystack_amount= (int)$amount + $service_charge;
        $final_amount_to_pay=  $paystack_amount * 100;
        $callback_url=$request->link;
        $url = "https://api.paystack.co/transaction/initialize";
        $fields = [
          'email' => $email,
          'amount' => $final_amount_to_pay,
          'callback_url'=>$callback_url,
        ];
        $fields_string = http_build_query($fields);
        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Authorization: ",
          "Cache-Control: no-cache",
        ));
          //test key
         //sk_test_78d3222355597d8e13ada75b3f02230f6849d4d8
        //live paystack key
        // sk_live_75cd2aea916edd58b62c7dad9ec24f1be553f393
        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        
        //execute post
        $result = curl_exec($ch);
        return $result;
    }

    //public function check calculation
    public function verify_checkout_products(Request $request){
          $validator = Validator::make($request->all(), [
                 'products' => 'required',
                 'total_amount' => 'required',
               
            ]);
             
         if($validator->fails()){
                    return response()->json($validator->errors(), 400);
            }else{
                $carbondate=Carbon::now();
                $date = $carbondate->toDateString();
                $time= $carbondate->now('UTC')->setTimezone('WAT')->format('g:i:s a');
                $token = JWTAuth::parseToken()->getPayload()->toArray();
                $id=$token['id'];
                $customer=User::where('id',$id)->first();
                  $decoded_product= json_decode($request->products, true);
                  $serilize_product= serialize($decoded_product);
                  $message = "";
                   $total_amount_from_app=0;
                   $total_amount_from_backend=0;
                foreach($decoded_product as $value){
                    
                    
                    $get_product= Products::where('id', $value['product_id'])->first();
                    if($value['product_price'] != $get_product->product_price){
                        $message = $value['product_name']."price has changed";
                        break;
                    }
                    
                    $cost_of_actual_product =    $get_product->product_price      * (int)$value['product_quantity'];
                    
                    $total_amount_from_backend += $cost_of_actual_product;
                    
                    $cost_of_product = (int)$value['product_price'] * (int)$value['product_quantity'];
                    
                    $total_amount_from_app += $cost_of_product;
                }
                if($message){
                     return response()->json([
                         "status"=>"failed",
                        "message"=> $message
                        ]);
                }
                elseif($total_amount_from_app == $total_amount_from_backend){
                     Customer_transaction::where('customer_id', $customer->id)->where('transaction_status', false)->delete();
                     $get_id=Customer_transaction::create([
                     "items_bought"=> $serilize_product,
                     "total_amount"=> $total_amount_from_app,
                     "customer_id"=>  $customer->id,
                     
                     "date"=>$date,
                    "time"=>$time,
                    "transaction_status"=>false
                    ]);
                    
                    return response()->json([
                        "cart_id"=> $get_id->id,
                        "status"=>"success",
                        "total_amount_to_pay"=>$total_amount_from_backend
                        ]);
                }else{
                       return response()->json([
                        "status"=>"failed",
                        "total_amount_to_pay"=>$total_amount_from_backend
                        ]);
                }
                
            }
    }
//verify paystack transactions
    public function verify_payments(Request $request){
        
            $validator = Validator::make($request->all(), [
                'cart_id'=>'required',
                 'reference'=> 'required',
            ]);
        
        
        
         if($validator->fails()){
                    return response()->json($validator->errors(), 400);
            }else{
      
    $reference=$request->reference;
     $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/{$reference}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Authorization: sk_test_78d3222355597d8e13ada75b3f02230f6849d4d8",
      "Cache-Control: no-cache",
    ),
  ));
  //test key
  //sk_test_78d3222355597d8e13ada75b3f02230f6849d4d8
 //live paystack key
// sk_live_75cd2aea916edd58b62c7dad9ec24f1be553f393
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl); 
  
  $result= json_decode($response);
  
  if($result->status=true){
     $token = JWTAuth::parseToken()->getPayload()->toArray();
                $id=$token['id'];
                
                 $customer=User::where('id',$id)->first();
                $encoded_products= Customer_transaction::where('id', $request->cart_id)->first();
                if($encoded_products){
                    Customer_transaction::where('id', $request->cart_id)->update([
                       "transaction_status"=>true
                        ]);
                
                $decoded_product= json_decode($encoded_products->items_bought);
                $serilize_product= serialize($decoded_product);
                $carbondate=Carbon::now();
                $date = $carbondate->toDateString();
                $time= $carbondate->now('UTC')->setTimezone('WAT')->format('g:i:s a');
                
                $total_amount = 0;
                
                foreach($decoded_product as $value){
                    $cost_of_product = (int)$value->product_price * (int)$value->product_quantity;
                    $total_amount += $cost_of_product;
                }
                
                foreach($decoded_product as $product){
                    
                    Orders::create([
                          "product_name"=>$product->product_name,
                          "product_id"=>$product->product_id,
                          "product_price"=>$product->product_price,
                          "image_url"=>$product->image_url,
                          "amount_ordered"=>$product->product_quantity,
                          "owner_id"=>$product->owner_id,
                          "shipping_phone_number"=>$product->phone_number,
                          "customer_name"=>$customer->name,
                          "customer_id"=>$customer->id,
                          "state"=>$product->state,
                          "destination_address"=>$product->destination_address,
                          "date"=>$date,
                          "time"=>$time
                    ]); 
                    
                    
                   
                    $id=$product->product_id;
                    $quantity=$product->product_quantity;
                $product_price= (int)$product->product_quantity *  (int)$product->product_price;
                
              
                
                
                Products::where('id',$id)->decrement('product_quantity',$quantity);
    
                $check_order_exist = Orders::where('id',$id)->where('date',$date)->where('time',$time)->count();
                
                if($check_order_exist == 1){
                     $title = "New Order";
                     //seller
                     $customer_title = "Order Receipt";
                     $customer_info = [
                         "customer_name"=>$customer->name,
                         "email"=>$customer->email,
                         "superadmin_phone_number"=>$superadmin->phone_number,
                         "superadmin_email"=>$superadmin->email
                         ];
                     $order_transaction_details =[
                            "items_bought"=>$decoded_product,
                            "total"=> $encoded_products->total_amount,
                            "subtotal"=> $encoded_products->total_amount,
                            "delivery_fee"=> "Free",
                            "state"=>$product->state,
                            "address"=>$product->destination_address
                            
                         ];
                         
                         
                 
                    $transaction_details=["type"=>""];
                     
                   
                     
                     $this->send_otp($superadmin_name, $superadmin_number);
                     
                
                     
                      dispatch(new Orderreceipt($customer_title,$customer_info,$order_transaction_details));
                     
                 }
                
                }
                  
                       
            return response()->json(
                [ "status"=>"success","message"=>"order has been created"]
                );}
  }
            }
  
     
    }
 
    
//get customer order history
 public function order_history(){
         $token = JWTAuth::parseToken()->getPayload()->toArray();
                    $id=$token['id'];
            $orders=  Customer_transaction::where('customer_id',$id)->get();
            
       
                
               $data =[];
            foreach($orders as $value){
                    $unserialize_orders= unserialize($value->items_bought);
                      
                    $array40 = [
                        "id"=> $value->id,
                        "product"=>$unserialize_orders,
                        "total_amount"=> $value->total_amount,
                        "delivery"=>$value->status,
                        "date"=>$value->date,
                        "time"=>$value->time
                        ]; 
                        
                        array_push($data, $array40);
                     
                           
            }
            
           
            return response()->json($data);
 }
    
    
    
    
    //review ordered product
    
    public function review_ordered_product(Request $request){
                 
                 
                 $token = JWTAuth::parseToken()->getPayload()->toArray();
                    $id=$token['id'];
                    $reviews = $request->review;
                    $ratings = $request->rating;
                    $product_id = $request->product_id;
                    $review = Product_review::where('product_id', $product_id )->where('user_id',$id)->first();
                    if($review){
                         return response()->json([
                            "status"=>"failed",
                            "message"=>"You already have a review for  this product"
                           ]);
                    }else{
                         Product_review::create([
                          "ratings"=> $ratings,
                          "reviews"=>$reviews,
                          "user_id"=>$id,
                          "product_id"=>$product_id
                        ]);
                        
                        return response()->json([
                            "status"=>"success"
                           ]);
                    }
                   
    }
    
   
    
    public function delete_review(Product_review $product_review){
                    $product_review->delete();
                    
                return response()->json([
                    "status"=>"success",
                    "message"=>"review deleted"
                    ]);
                    
                    
    }
    

      //get order list for admin/seller
      public function get_adminorderedproduct(){
        $token = JWTAuth::parseToken()->getPayload()->toArray();
        $id=$token['id'];
        
      $order_list= Orders::get()->groupBy('customer_id');
    
     $arrayvalue = [];
    foreach($order_list as $name=>$value){
        $test2= $value->groupBy('date');
        array_push($arrayvalue , $test2);
        
  }
  $filtered_data=[];
    foreach($arrayvalue as $date8){
        foreach($date8 as $test2=>$test3){
            $test5= $test3->groupBy('time');
        array_push($filtered_data , $test5);
        }
          
        
    }
    
    $final_result =[];
     foreach($filtered_data as $value_data){
        foreach($value_data as $test21=>$test22){
            foreach($test22 as $test24){
                 $array6=["customer_name"=> $test24->customer_name,"delivery_status"=>$test24->delivery_status,"date"=>$test24->date,"customer_id"=>$test24->customer_id,"time"=>$test24->time];
                 array_push($final_result, $array6);
                 break;
            }
        }
    }
       $paginated_data =$this->paginate($final_result);
       return response()->json($paginated_data);
}
    


 //order details
 public function admin_order_details(Request $request){
                 
    $super_admin =  Superadmin::first();
    $commission =   $super_admin->super_admin_commission;
    
    $date= $request->date;
    $id = $request->id;
    $time= $request->time;
    $user = Orders::where('date',$date)->where('customer_id',$id)->first();
   $orders = Orders::where('customer_id',$id)->where('date',$date)->where('time',$time)->get();
   if($orders){
       
  
   $final_result=[];
   foreach($orders as $value){
             $product_price=(int)$value->amount_ordered * (int)$value->product_price;
             $superadmin_amount_made_from_commission = (int)$value->product_price *  $commission;
             $amount_to_be_paid =(int)$product_price - $superadmin_amount_made_from_commission;
             $data = [
                 "customer_name"=>$user->customer_name,
                 "product_name"=>$value->product_name,
                 "image_url"=>$value->image_url,
                 "product_price"=>$value->product_price,
                 "amount_ordered"=>$value->amount_ordered,
                 "payment_status"=>$value->status,
                 "product_size"=>$value->product_size,
                 "superadmin_commission_on_product"=>$superadmin_amount_made_from_commission,
                 "amount_to_be_paid"=>$amount_to_be_paid
                 ];
                 
                 array_push($final_result, $data);
   }
   return response()->json($final_result);
}else{
return response()->json([
"status"=>"failed",
"message"=>"No record found"
]);
}

}




    
}