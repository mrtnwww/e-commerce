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

    // Escuchar el evento de actualizacion de estado desde [OrderIndex]
    #[On('order-status-updated')]
    public function onOrderUpdated(): void
    {
        $this->updateCount();
    }

    // Actualizar el contador de pedidos pendientes
    private function updateCount(): void
    {
        $this->count = Order::byStatus('pending')->count();
    }

    public function render()
    {
        return view('livewire.admin.pending-orders-badge');
    }
}
