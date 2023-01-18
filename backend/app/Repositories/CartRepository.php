<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Support\Collection;
use Stevebauman\Location\Facades\Location;

class CartRepository implements CartRepositoryInterface
{
    protected $items;
    protected $offers;
    protected $isQuantityBasedOfferApplied;
    protected $isTotalBasedOffer;
    protected $discount_percentage;
    protected $totalMoreThan;
    protected $countryFreeShipping;
    protected $isFreeShippingTotalBased;
    protected $isFreeShippingCountryBased;

    public function __construct()
    {
        $this->items = collect([]);
        $this->offers = Offer::with('options')->get();
        $this->isQuantityBasedOfferApplied = false;
        $this->isTotalBasedOffer = false;
        $this->isFreeShippingTotalBased = false;
        $this->isFreeShippingCountryBased = false;
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
            $offerOptions = $offer->options->groupBy('name');
            if ($offer['name'] === 'Buy X get Y') {
                if (
                    in_array('Product Id', $offerOptions->keys()->toArray()) &&
                    $offerOptions['Product Id']->first()->factor === $product->id
                ) {
                    $randomProduct = Product::inRandomOrder()->first();
                    Cart::firstOrCreate(['product_id' => $randomProduct->id])
                        ->increment('quantity', $quantity);
                }
            } elseif ($offer['name'] === 'Quantity-based discount') {
                if (
                    in_array('Quantity more than', $offerOptions->keys()->toArray()) &&
                    $offerOptions['Quantity more than']->first()->factor > $quantity
                ) {
                    $this->isQuantityBasedOfferApplied = true;
                    $this->discount_percentage = $offerOptions['Quantity more than']->first()->percentge_value;
                }
            } elseif ($offer['name'] === 'Order total-based discount') {
                if (in_array('Total more than', $offerOptions->keys()->toArray())) {
                    $this->isTotalBasedOffer = true;
                    $this->totalMoreThan = $offerOptions['Total more than']->first()->factor;
                    $this->discount_percentage = $offerOptions['Total more than']->first()->percentge_value;
                }
            } elseif ($offer['name'] === 'Free Delivery') {
                if (in_array('For Total more than', $offerOptions->keys()->toArray())) {
                    $this->totalMoreThan = $offerOptions['For Total more than']->first()->factor;
                    $this->isFreeShippingTotalBased = true;
                } elseif (in_array('For country', $offerOptions->keys()->toArray())) {
                    $this->countryFreeShipping = $offerOptions['For country']->first()->factor;
                    $this->isFreeShippingCountryBased = true;
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

        if (
            ($this->isTotalBasedOffer && $this->totalMoreThan > $total) || ($this->isQuantityBasedOfferApplied)
        ) {
            $total -= ($total * $this->discount_percentage / 100);
        }

        return $total;
    }

    public function shippingFees()
    {
        $total = $this->total();

        if (($this->isFreeShippingTotalBased && $total > $this->totalMoreThan) ||
            ($this->isFreeShippingCountryBased &&
                Location::get()->countryName === $this->countryFreeShipping
            )
        ) {
            return 0;
        }


    }
}
