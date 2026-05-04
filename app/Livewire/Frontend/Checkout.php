<?php

namespace App\Livewire\Frontend;

use App\Models\Cart;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Checkout extends Component
{
    // Step control
    public int $step = 1;

    // Step 1 - Customer data
    public string $customerName = '';

    public string $customerEmail = '';

    public string $customerPhone = '';

    // Step 2 - Shipping
    public string $shippingAddress = '';

    public string $shippingCity = '';

    public string $shippingDepartment = '';

    public string $shippingZip = '';

    // Step 3 - Payment
    public string $paymentMethod = 'transfer';

    public string $couponCode = '';

    public string $notes = '';

    public ?array $appliedCoupon = null;

    public string $couponError = '';

    public array $cartItems = [];

    public function mount(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->customerName = $user->name;
            $this->customerEmail = $user->email;
        }
        $this->loadCart();
        if (empty($this->cartItems)) {
            $this->redirect(route('shop'));
        }
    }

    protected function rulesStep1(): array
    {
        return [
            'customerName' => 'required|string|max:255',
            'customerEmail' => 'required|email',
            'customerPhone' => 'nullable|string|max:20',
        ];
    }

    protected function rulesStep2(): array
    {
        return [
            'shippingAddress' => 'required|string|max:500',
            'shippingCity' => 'required|string|max:100',
            'shippingDepartment' => 'required|string|max:100',
        ];
    }

    public function nextStep(): void
    {
        match ($this->step) {
            1 => $this->validate($this->rulesStep1()),
            2 => $this->validate($this->rulesStep2()),
            default => null,
        };
        $this->step++;
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function applyCoupon(): void
    {
        $this->couponError = '';
        $this->appliedCoupon = null;

        $discount = Discount::where('code', strtoupper($this->couponCode))
            ->where('active', true)
            ->first();

        if (! $discount || ($discount->expires_at && $discount->expires_at->isPast())) {
            $this->couponError = 'Cupón no válido o expirado.';

            return;
        }

        $this->appliedCoupon = [
            'id' => $discount->id,
            'code' => $discount->code,
            'type' => $discount->type,
            'value' => $discount->value,
        ];
    }

    public function placeOrder(): void
    {
        $this->validate(array_merge(
            $this->rulesStep1(),
            $this->rulesStep2(),
            ['paymentMethod' => 'required|string']
        ));

        DB::transaction(function () {
            $subtotal = collect($this->cartItems)->sum('subtotal');
            $discountAmount = 0;

            if ($this->appliedCoupon) {
                $discountAmount = $this->appliedCoupon['type'] === 'percentage'
                    ? $subtotal * ($this->appliedCoupon['value'] / 100)
                    : $this->appliedCoupon['value'];

                Discount::find($this->appliedCoupon['id'])
                    ->increment('used_count');
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'pending',
                'customer_name' => $this->customerName,
                'customer_email' => $this->customerEmail,
                'customer_phone' => $this->customerPhone,
                'shipping_address' => $this->shippingAddress,
                'shipping_city' => $this->shippingCity,
                'shipping_department' => $this->shippingDepartment,
                'shipping_zip' => $this->shippingZip,
                'subtotal' => $subtotal,
                'shipping_cost' => 0,
                'discount' => $discountAmount,
                'total' => max(0, $subtotal - $discountAmount),
                'payment_method' => $this->paymentMethod,
                'notes' => $this->notes,
            ]);

            foreach ($this->cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_sku' => $item['sku'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total' => $item['subtotal'],
                ]);

                // Decrease stock
                \App\Models\Product::where('id', $item['product_id'])
                    ->decrement('stock', $item['quantity']);
            }

            // Clear cart
            Cart::when(Auth::check(),
                fn ($q) => $q->where('user_id', Auth::id()),
                fn ($q) => $q->where('session_id', session()->getId())
            )->delete();

            $this->redirect(route('order.success', $order->number));
        });
    }

    public function getSubtotalAttribute(): float
    {
        return collect($this->cartItems)->sum('subtotal');
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

    private function loadCart(): void
    {
        $this->cartItems = Cart::with('product')
            ->when(Auth::check(),
                fn ($q) => $q->where('user_id', Auth::id()),
                fn ($q) => $q->where('session_id', session()->getId())
            )
            ->get()
            ->map(fn ($c) => [
                'product_id' => $c->product_id,
                'name' => $c->product->name,
                'sku' => $c->product->sku,
                'price' => $c->product->price,
                'quantity' => $c->quantity,
                'subtotal' => $c->quantity * $c->product->price,
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.frontend.checkout')
            ->layout('layouts.store');
    }
}
