<?php

namespace App\Livewire\Frontend;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartBadge extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->updateCount();
    }

    #[\Livewire\Attributes\On('cart-add')]
    public function onCartAdd(): void
    {
        $this->updateCount();
    }

    private function updateCount(): void
    {
        $this->count = Cart::when(
            Auth::check(),
            fn ($q) => $q->where('user_id', Auth::id()),
            fn ($q) => $q->where('session_id', session()->getId())
        )->sum('quantity');
    }

    public function render()
    {
        return view('livewire.frontend.cart-badge');
    }
}
