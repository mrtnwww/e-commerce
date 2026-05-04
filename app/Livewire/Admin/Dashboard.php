<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public array $metrics = [];

    public array $recentOrders = [];

    public array $lowStockProducts = [];

    public array $weeklySales = [];

    public function mount(): void
    {
        $this->loadMetrics();
        $this->loadRecentOrders();
        $this->loadLowStock();
        $this->loadWeeklySales();
    }

    private function loadMetrics(): void
    {
        $now = now();
        $startMonth = $now->copy()->startOfMonth();
        $lastMonth = $now->copy()->subMonth()->startOfMonth();
        $endLast = $now->copy()->subMonth()->endOfMonth();

        $salesThisMonth = Order::where('created_at', '>=', $startMonth)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->sum('total');

        $salesLastMonth = Order::whereBetween('created_at', [$lastMonth, $endLast])
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->sum('total');

        $ordersThis = Order::where('created_at', '>=', $startMonth)->count();
        $ordersLast = Order::whereBetween('created_at', [$lastMonth, $endLast])->count();

        $clientsThis = User::where('created_at', '>=', $startMonth)->count();

        $this->metrics = [
            'sales' => $salesThisMonth,
            'sales_delta' => $salesLastMonth > 0
                ? round((($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100, 1)
                : 0,
            'orders' => $ordersThis,
            'orders_delta' => $ordersLast > 0
                ? round((($ordersThis - $ordersLast) / $ordersLast) * 100, 1)
                : 0,
            'clients' => $clientsThis,
            'low_stock' => Product::lowStock()->count(),
            'out_of_stock' => Product::outOfStock()->count(),
            'pending_orders' => Order::byStatus('pending')->count(),
        ];
    }

    private function loadRecentOrders(): void
    {
        $this->recentOrders = Order::with('items')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'number' => $o->number,
                'customer' => $o->customer_name,
                'total' => $o->total,
                'status' => $o->status,
                'status_label' => $o->status_label,
                'status_color' => $o->status_color,
                'items_count' => $o->items->count(),
                'created_at' => $o->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    private function loadLowStock(): void
    {
        $this->lowStockProducts = Product::with('subcategory.category')
            ->lowStock()
            ->orWhere(fn ($q) => $q->outOfStock())
            ->limit(8)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'stock' => $p->stock,
                'sku' => $p->sku,
                'category' => $p->subcategory?->category?->name,
                'is_out' => $p->is_out_of_stock,
            ])
            ->toArray();
    }

    private function loadWeeklySales(): void
    {
        $days = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            $total = Order::whereDate('created_at', $date)
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->sum('total');

            return [
                'day' => $date->translatedFormat('D'),
                'date' => $date->format('Y-m-d'),
                'total' => (float) $total,
            ];
        });

        $this->weeklySales = $days->toArray();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin');
    }
}
