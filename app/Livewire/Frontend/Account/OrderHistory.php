<?php

namespace App\Livewire\Frontend\Account;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OrderHistory extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public function render()
    {
        $orders = Order::where('user_id', Auth::id())
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);

        return view('livewire.frontend.account.order-history', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
        ])->layout('layouts.store');
    }
}
