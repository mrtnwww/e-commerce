<?php

namespace App\Livewire\Frontend;

use App\Models\Order;
use Livewire\Component;

class OrderSuccess extends Component
{
    public Order $order;

    public function mount(string $number): void
    {
        $this->order = Order::where('number', $number)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.frontend.order-success')
            ->layout('layouts.store');
    }
}
