<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Stevebauman\Location\Facades\Location;

class CartRepository implements CartRepositoryInterface
{
    protected $items;
    protected $offers;
    protected $isQuantityBasedOffer;
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
        $this->isQuantityBasedOffer = false;
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
        $cart = Cart::firstOrCreate(['product_id' => $product->id]);
        $cart->increment('quantity', $quantity);
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
        $this->applyOffers();
        $total = $this->get()->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        if (
            ($this->isTotalBasedOffer && $total > $this->totalMoreThan) ||
            ($this->isQuantityBasedOffer)
        ) {
            $total -= ($total * $this->discount_percentage / 100);
        }

        return $total;
    }

    public function shippingFees()
    {
        $total = $this->total();

        if (($this->isFreeShippingTotalBased && $total > $this->totalMoreThan
            ) ||
            ($this->isFreeShippingCountryBased &&
                Location::get()->countryCode === $this->countryFreeShipping
            )
        ) {
            return 0;
        }

        //'shipping fees logic'
        return 3;
    }


    public function applyOffers()
    {
        $product_ids = $this->get()->pluck('product.id');
        $total_quantity = $this->get()->sum(function ($item) {
            return $item->quantity;
        });

        foreach ($this->offers as $offer) {
            $offerOptions = $offer->options()
                ->where('status', 'active')
                ->get()
                ->groupBy('name');

            if ($offer['offer_name'] === 'Buy X get Y') {
                if (
                    in_array('Product Id', $offerOptions->keys()->toArray()) &&
                    in_array($offerOptions['Product Id']->first()->factor, $product_ids->toArray())
                ) {
                    $randomProduct = Product::inRandomOrder()->first();
                    Cart::firstOrCreate(['product_id' => $randomProduct->id])
                        ->increment('quantity', 1);
                }
            } elseif ($offer['offer_name'] === 'Quantity-based discount') {
                if (
                    in_array('Quantity more than', $offerOptions->keys()->toArray()) &&
                    $total_quantity > $offerOptions['Quantity more than']->first()->factor
                ) {
                    $this->isQuantityBasedOffer = true;
                    $this->discount_percentage = $offerOptions['Quantity more than']->first()->percentge_value;
                }
            } elseif ($offer['offer_name'] === 'Order total-based discount') {
                if (in_array('Total more than', $offerOptions->keys()->toArray())) {
                    $this->isTotalBasedOffer = true;
                    $this->totalMoreThan = $offerOptions['Total more than']->first()->factor;
                    $this->discount_percentage = $offerOptions['Total more than']->first()->percentge_value;
                }
            } elseif ($offer['offer_name'] === 'Free Delivery') {
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
}
