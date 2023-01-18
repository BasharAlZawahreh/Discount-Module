<?php

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

Route::get('/', function () {
    dd(Location::get());
    // $ip = request()->ip();
    // if ($position = Location::get()) {
    //     // Successfully retrieved position.
    //     echo $position->countryName;
    // }
    // $currentUserInfo = Location::get($ip);
    // return view('welcome', [
    //     'info' => $currentUserInfo
    // ]);
});
