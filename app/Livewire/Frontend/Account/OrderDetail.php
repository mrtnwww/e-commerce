<?php

namespace App\Livewire\Frontend\Account;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderDetail extends Component
{
    public Order $order;

    public function mount(string $number): void
    {
        $this->order = Order::with('items.product')
            ->where('number', $number)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.frontend.account.order-detail')
            ->layout('layouts.store');
    }
}
