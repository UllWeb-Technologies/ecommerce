<?php

namespace App\Http\Controllers;
use App\Models\User;

use App\Models\Products;



use App\Models\Sub_product;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use Config;
use Auth;
use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;
class Authcontroller extends Controller
{
   

        public function login_user(Request $request)
        {
            $credentials = $request->only('email', 'password');
           

            try {
                if(auth()->guard()->attempt($credentials)){
                    $user=auth()->user();
                    $payload=[
                        "name"=>$user->name,
                        "role"=>$user->role,
                        "id"=>$user->id,
                        ];
                        
                    $token=JWTAuth::customClaims($payload)->fromUser($user);
                }
                else {
                    return response()->json(['error' => 'invalid_credentials'], 400);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
             $status="success";
             $expirein=  config('jwt.ttl');
            return response()->json(compact('status','token','user','expirein'));
        }
     //refresh token
     public function refresh_token(){
         
         
                try{
                    $token = auth()->refresh();
                } catch (JWTException $e) {
                return response()->json(['error' => 'this token has been blacklisted '], 500);
            }
                 
                 $expirein=  config('jwt.ttl');
                 return response()->json(compact('expirein','token'));
     }
     
     //-----end-----

       
      //register users
        public function register_user(Request $request)
        {
                $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone_number'=>'required',
                'password' => 'required|string|min:6|confirmed',
                'country'=>'required',
                
            ]);

            if($validator->fails()){
                    return response()->json($validator->errors(), 400);
            }else{
             $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number'=>$request->phone_number,
                'country'=> $request->country,
                'password' => Hash::make($request->get('password')),
            ]);
            $userid=$user->id;
            $get_user= User::where('id',$userid)->first();
             $payload=[
                 "name"=>$user->name,
                 "role"=>$get_user->role,
                 "id"=>$user->id
                 ];
            $token = JWTAuth::customClaims($payload)->fromUser($user);
            $status="success";
            $expirein=  config('jwt.ttl');
            return response()->json(compact('user','expirein','token','status'),201);
            }

          
        }

//user dashboard api
public function dashboard_api_for_user(){
     $token = JWTAuth::parseToken()->getPayload()->toArray();
                   $id=$token['id'];
                   
       $user= User::select('id','device_id','name','email','country','state','destination_address','phone_number','device_verified')->where('id', $id)->first();
        $scanned_data = Scanned_data::select('intake_temp','engine_load','batteryvoltage','coolant_temp','rpm','speed','lat','log')->where('deviceid',  $user->device_id)->orderBy('id', 'desc')->first();
       return response()->json(["user"=>$user,"scanned_data"=>$scanned_data,"paystack_token"=>"pk_test_ef3806de9a2e5b1110aa48752a95a84d33fd924b"]);
}




//update password from dashboard 
public function update_password_from_dashboard(Request $request){
               $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:6|confirmed',
                'old_password'=>'required'
                
            ]);
            if($validator->fails()){
                    return response()->json($validator->errors(), 400);
            }else{
                 $token = JWTAuth::parseToken()->getPayload()->toArray();
                   $id=$token['id'];
                   
                 $user= User::where('id', $id)->first();
                $pass=$user->password;
                if(Hash::check($request->old_password, $pass)){
                    
                $id=$user->id;
           
            $admin = User::where('id',$id)->update([
                'password'=>Hash::make($request->password)
            ]);
            return response()->json(["status"=>"success"],200);
                }else{
                    return response()->json([
                        "satus"=> "failed",
                        "message"=>"Incorrect password"
                        ]);
                }
                
                }
}

 //logout customer
 public function customerlogout(){
     auth()->user()->logout();
     
      return response()->json([
          "status"=>"success"
          ]);
 }

//reset password
  public function  user_reset_password(Request $request){
                $validator = Validator::make($request->all(), [
                'email'=>'required',
                'password' => 'required|string|min:6|confirmed'
            ]);

            if($validator->fails()){
                    return response()->json($validator->errors(), 400);
            }else{
                $email=$request->email;
                User::where('email',$email)->update([
                    "password"=> Hash::make($request->get('password')),
                    ]);
                    
                return response()->json([
                    "status"=>"success",
                    "message"=>"updated successfully"
                    ]);
            }
  }


//update customers details
    public function update_details(Request $request){
         $token = JWTAuth::parseToken()->getPayload()->toArray();
                $id=$token['id'];
                $validator = Validator::make($request->all(), [
                'name'=>'required',
                'country'=>'required',
                'phone_number'=>'required',
                'state' => 'required',
                'shipping_address'=>'required'
            ]);

            if($validator->fails()){
                    return response()->json(["status"=>"failed","message"=>"all fields are required "], 400);
            }else{
               
                
                User::where('id',$id)->update([
                    "name"=>$request->name,
                    "country"=>$request->country,
                    "phone_number"=>$request->phone_number,
                    "state"=>$request->state,
                    "destination_address"=>$request->shipping_address
                    ]);
                    
                return response()->json([
                    "status"=>"success"
                    ]);
            }
    }

    
    
   
    

    
    
   
}
