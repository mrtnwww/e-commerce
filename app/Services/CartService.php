<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function add(int $productId): bool
    {
        $product = Product::find($productId);
        if (!$product || $product->stock <= 0) {
            return false;
        }

        $cart = Cart::firstOrNew([
            'product_id' => $productId,
            Auth::check() ? 'user_id' : 'session_id' => Auth::check() ? Auth::id() : session()->getId(),
        ]);

        $newQty = ($cart->quantity ?? 0) + 1;
        if ($newQty > $product->stock) {
            return false;
        }

        $cart->quantity = $newQty;
        if (!Auth::check()) {
            $cart->session_id = session()->getId();
        }
        $cart->save();

        return true;
    }
}
