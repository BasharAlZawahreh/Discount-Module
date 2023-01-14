<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function ceate(Request $request)
    {

    }

    public function checkOffers()
    {
        // $offers = Offer::where('status','active')->get();

        // foreach ($offers as $offer) {
        //     if ($offer['name']==='Buy X get Y') {
        //         return;
        //     }

        // }
        // 'name'=>'Quantity-based discount',
        //  'name'=>'Free Delivery For country',
        //  'name'=>'Free Delivery If Total bigger than value'
    }
}
