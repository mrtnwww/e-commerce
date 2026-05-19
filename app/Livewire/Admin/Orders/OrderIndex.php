<?php

namespace App\Livewire\Admin\Orders;

use App\Livewire\Admin\PendingOrdersBadge;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderIndex extends Component
{
    use WithPagination;

    public int $perPage = 15;
    public string $search = '';
    public string $status = '';
    public string $sortDir = 'desc';
    public string $sortBy = 'created_at';

    public ?Order $selectedOrder = null;
    public bool $showModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    // Hook de busqueda
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // Hook de cambio de cambio de estado
    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    // Manejo ordenamiento
    public function handleSortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    // Mostrar modal detalle del pedido
    public function viewOrder(int $id): void
    {
        $this->selectedOrder = Order::with('items.product')->find($id);
        $this->showModal = true;
    }

    // Cerrar modal detalle del pedido
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedOrder = null;
    }

    // Actualizar estado del pedido desde el select
    public function updateStatus(int $orderId, string $status): void
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);

        match ($status) {
            'shipped' => $order->update(['shipped_at' => now()]),
            'delivered' => $order->update(['delivered_at' => now()]),
            default => null,
        };

        // Lanzar el evento de actualización de estado al componente [PendingOrdersBadge]
        $this->dispatch('order-status-updated')->to(PendingOrdersBadge::class);

        // Crear notificación toast de cambio de estado de pedido
        $this->dispatch('notify', message: 'Estado actualizado correctamente');
    }

    // Lista de pedidos
    public function getOrdersProperty()
    {
        return Order::when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('number', 'like', "%{$this->search}%")
                    ->orWhere('customer_name', 'like', "%{$this->search}%")
                    ->orWhere('customer_email', 'like', "%{$this->search}%");
            }))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }

    // Contador de pedidos por estado
    public function getCountsByStatusProperty(): array
    {
        return Order::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($total) => (int) $total)
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.orders.order-index', [
            'orders' => $this->orders,
            'countsByStatus' => $this->countsByStatus,
            'statuses' => Order::STATUSES,
        ])->layout('layouts.admin');
    }
}
