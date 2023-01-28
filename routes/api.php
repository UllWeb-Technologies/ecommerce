<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authcontroller;
use App\Http\Controllers\Productcontroller;
use App\Http\Controllers\Seller;
use App\Http\Controllers\Customers;
use App\Http\Controllers\Scancontroller;





use App\Http\Controllers\Charts;
use App\Http\Controllers\Orderscontroller;
use App\Http\Controllers\Categorycontroller;

use App\Http\Controllers\Breathalyzer;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//breathalyzer endpoint




Route::group(['middleware'=>['auth.customer:users']], function(){

     Route::get('customer_order_history',[Orderscontroller::class, 'order_history']);

     Route::post('verify_payments',[Orderscontroller::class, 'verify_payments']);

     Route::post('initialize_payment',[Orderscontroller::class, 'initialize_payments']);
     
     Route::post('update_customer_details',[Authcontroller::class, 'update_details']);
     

     
     Route::post('review_ordered_product',[Orderscontroller::class, 'review_ordered_product']);

     Route::get('delete_review',[Orderscontroller::class, 'delete_review']);

     Route::get('get_product_reviews',[Productcontroller::class, 'get_product_reviews']);

     
  
     
     Route::get('dashboard_api_for_user',[Authcontroller::class, 'dashboard_api_for_user']);
  
        
     Route::get('get_user_product',[Productcontroller::class, 'get_user_product']);
     
  
    Route::post('verify_checkout_products',[Orderscontroller::class, 'verify_checkout_products']);

     Route::post('review_product',[Productcontroller::class, 'review_product']);
     

    
     Route::post('update_password_from_dashboard',[Authcontroller::class, 'update_password_from_dashboard']);

});







