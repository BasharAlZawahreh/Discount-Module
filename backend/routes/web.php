<?php

use App\Models\Product;
use App\Repositories\CartRepositoryInterface;
use Illuminate\Support\Facades\Route;
use Stevebauman\Location\Facades\Location;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (CartRepositoryInterface $cart) {
    // $product = Product::find(1);
    // $cart->add($product);

    dd($cart->shippingFees());
    return view('welcome');
});
