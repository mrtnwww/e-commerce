<?php

namespace App\Livewire\Admin\Orders;

use App\Livewire\Admin\PendingOrdersBadge;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $sortBy = 'created_at';

    public string $sortDir = 'desc';

    public int $perPage = 15;

    // For order detail modal
    public ?Order $selectedOrder = null;

    public bool $showModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function handleSortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function viewOrder(int $id): void
    {
        $this->selectedOrder = Order::with('items.product')->find($id);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedOrder = null;
    }

    public function updateStatus(int $orderId, string $status): void
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);

        match ($status) {
            'shipped' => $order->update(['shipped_at' => now()]),
            'delivered' => $order->update(['delivered_at' => now()]),
            default => null,
        };

        $this->dispatch('order-status-updated')->to(PendingOrdersBadge::class);
        $this->dispatch('notify', message: 'Estado actualizado correctamente');
    }

    public function getOrdersProperty()
    {
        return Order::when($this->search, fn ($q) => $q->where('number', 'like', "%{$this->search}%")
            ->orWhere('customer_name', 'like', "%{$this->search}%")
            ->orWhere('customer_email', 'like', "%{$this->search}%"))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }

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
