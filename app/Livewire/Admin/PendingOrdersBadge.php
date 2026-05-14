<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

class PendingOrdersBadge extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->updateCount();
    }

    #[On('order-status-updated')]
    public function onOrderUpdated(): void
    {
        $this->updateCount();
    }

    private function updateCount(): void
    {
        $this->count = Order::byStatus('pending')->count();
    }

    public function render()
    {
        return view('livewire.admin.pending-orders-badge');
    }
}
