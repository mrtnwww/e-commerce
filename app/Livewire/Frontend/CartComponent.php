<?php

namespace App\Livewire\Frontend;

use App\Models\Cart;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartComponent extends Component
{
    public array $items = [];

    public string $couponCode = '';

    public ?array $appliedCoupon = null;

    public string $couponError = '';

    public function mount(): void
    {
        $this->loadCart();
    }

    #[\Livewire\Attributes\On('cart-add')]
    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);
        if (! $product || $product->stock <= 0) {
            return;
        }

        $key = Auth::check() ? null : session()->getId();

        $cart = Cart::firstOrNew([
            'product_id' => $productId,
            Auth::check() ? 'user_id' : 'session_id' => Auth::check() ? Auth::id() : $key,
        ]);

        // Don't exceed stock
        $newQty = ($cart->quantity ?? 0) + 1;
        if ($newQty > $product->stock) {
            return;
        }

        $cart->quantity = $newQty;
        if (! Auth::check()) {
            $cart->session_id = $key;
        }
        $cart->save();

        $this->loadCart();
        $this->dispatch('notify', message: 'Producto añadido al carrito');
    }

    public function updateQuantity(int $cartId, int $quantity): void
    {
        $cart = Cart::findOrFail($cartId);
        if ($quantity <= 0) {
            $cart->delete();
        } else {
            $cart->update(['quantity' => min($quantity, $cart->product->stock)]);
        }
        $this->loadCart();
    }

    public function removeItem(int $cartId): void
    {
        Cart::findOrFail($cartId)->delete();
        $this->loadCart();
    }

    public function applyCoupon(): void
    {
        $this->couponError = '';
        $this->appliedCoupon = null;

        $discount = Discount::where('code', strtoupper($this->couponCode))
            ->where('active', true)
            ->first();

        if (! $discount) {
            $this->couponError = 'Cupón no válido o expirado.';

            return;
        }

        if ($discount->expires_at && $discount->expires_at->isPast()) {
            $this->couponError = 'Este cupón ha expirado.';

            return;
        }

        if ($discount->max_uses && $discount->used_count >= $discount->max_uses) {
            $this->couponError = 'Este cupón ha alcanzado el límite de usos.';

            return;
        }

        if ($discount->minimum_order && $this->subtotal < $discount->minimum_order) {
            $this->couponError = 'Pedido mínimo de $'.number_format($discount->minimum_order, 0, ',', '.').' requerido.';

            return;
        }

        $this->appliedCoupon = [
            'id' => $discount->id,
            'code' => $discount->code,
            'type' => $discount->type,
            'value' => $discount->value,
        ];
    }

    public function getSubtotalAttribute(): float
    {
        return collect($this->items)->sum(fn ($i) => $i['quantity'] * $i['price']);
    }

    public function getDiscountAmountAttribute(): float
    {
        if (! $this->appliedCoupon) {
            return 0;
        }

        return $this->appliedCoupon['type'] === 'percentage'
            ? $this->subtotal * ($this->appliedCoupon['value'] / 100)
            : min($this->appliedCoupon['value'], $this->subtotal);
    }

    public function getTotalAttribute(): float
    {
        return max(0, $this->subtotal - $this->discountAmount);
    }

    public function getCartCountAttribute(): int
    {
        return collect($this->items)->sum('quantity');
    }

    private function loadCart(): void
    {
        $query = Cart::with('product')
            ->when(Auth::check(),
                fn ($q) => $q->where('user_id', Auth::id()),
                fn ($q) => $q->where('session_id', session()->getId())
            );

        $this->items = $query->get()->map(fn ($c) => [
            'cart_id' => $c->id,
            'product_id' => $c->product_id,
            'name' => $c->product->name,
            'image' => $c->product->main_image,
            'price' => $c->product->price,
            'stock' => $c->product->stock,
            'quantity' => $c->quantity,
            'subtotal' => $c->quantity * $c->product->price,
        ])->toArray();
    }

    public function render()
    {
        return view('livewire.frontend.cart-component')
            ->layout('layouts.store');
    }
}
