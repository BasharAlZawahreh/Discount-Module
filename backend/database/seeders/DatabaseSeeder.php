<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Offer;
use App\Models\OfferOption;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Product::factory(10)->create();
        Offer::create([
            'offer_name' => 'Buy X get Y',
        ])->options()->create([
            'name' => "Product Id",
            "factor" => "1",
        ]);

        Offer::create([
            'offer_name' => 'Quantity-based discount',
        ])->options()->create([
            'name' => "Quantity more than",
            "factor" => "2",
            'percentge_value' => '20'
        ]);

        Offer::create([
            'offer_name' => 'Order total-based discount',
        ])->options()->create([
            'name' => "Total more than",
            "factor" => "2",
            'percentge_value' => 20
        ]);


        $offer_free = Offer::create([
            'offer_name' => 'Free Delivery'
        ]);
        $offer_free->options()->create([
            'name' => 'For country',
            'factor' => 'US',
        ]);
        $offer_free->options()->create([
            'name' => 'For Total more than',
            'factor' => '120',
        ]);
    }
}
