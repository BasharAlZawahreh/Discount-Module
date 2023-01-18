<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Support\Collection;

class CartRepository implements CartRepositoryInterface
{
    protected $items;
    protected $offers;
    protected $isQuantityBasedOfferApplied;
    protected $discount_percentage;

    public function __construct()
    {
        $this->items = collect([]);
        $this->offers = Offer::with('options')->get();
        $this->isQuantityBasedOfferApplied = false;
    }

    public function get(): Collection
    {
        if (!$this->items->count()) {
            $this->items = Cart::with('product')->get();
        }

        return $this->items;
    }

    public function add(Product $product, $quantity = 1)
    {
        $cart =  Cart::firstOrCreate(['product_id' => $product->id])
            ->increment('quantity', $quantity);

        foreach ($this->offers as $offer) {
            if ($offer['name'] === 'Buy X get Y') {
                $offerOptions = $offer->options->groupBy('name');
                if (
                    in_array('Product Id', $offerOptions->keys()->toArray()) &&
                    $offerOptions['Product Id']->first()->factor === $product->id
                ) {
                    $randomProduct = Product::inRandomOrder()->first();
                    Cart::firstOrCreate(['product_id' => $randomProduct->id])
                        ->increment('quantity', $quantity);
                }
            } elseif ($offer['name'] === 'Quantity-based discount') {
                $offerOptions = $offer->options->groupBy('name');
                if (
                    in_array('Quantity more than', $offerOptions->keys()->toArray()) &&
                    $offerOptions['Quantity more than']->first()->factor > $quantity
                ) {
                    $this->isQuantityBasedOfferApplied = true;
                    $this->discount_percentage = $offerOptions['Quantity more than']->first()->percentge_value;
                }
            }
        }
    }

    public function update($id, $quantity)
    {
        Cart::where('id', '=', $id)
            ->update([
                'quantity' => $quantity,
            ]);
    }

    public function delete($id)
    {
        Cart::where('id', '=', $id)
            ->delete();
    }

    public function empty()
    {
        Cart::query()->delete();
    }

    public function total(): float
    {
        $total = $this->get()->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        if (!$this->isQuantityBasedOfferApplied) {
            return  $total;
        }
        
        return $total - ($total * $this->discount_percentage / 100);
    }
}
