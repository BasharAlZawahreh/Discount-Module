<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Product;
use App\Repositories\CartRepositoryInterface;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cart;

    public function __construct(CartRepositoryInterface $cart)
    {
        $this->cart = $cart;
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|int|exists:products,id',
            'quantity' => 'nullable|int|min:1'
        ]);

        $product = Product::findOrFail($request['product_id']);
        $this->cart->add($product, $request['quantity']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|int|min:1',
        ]);

        $this->cart->update($id, $request['quantity']);
    }

    public function destroy($id)
    {
        $this->cart->delete($id);

        return [
            'message' => 'Item deleted!',
        ];
    }
}
