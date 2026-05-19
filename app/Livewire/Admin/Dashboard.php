<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public array $lowStockProducts = [];
    public array $recentOrders = [];
    public array $weeklySales = [];
    public array $metrics = [];

    public function mount(): void
    {
        $this->loadMetrics(); // Cargar metricas
        $this->loadLowStock(); // Cargar productos con bajo stock
        $this->loadWeeklySales(); // Cargar ventas de los ultimos  7 dias
        $this->loadRecentOrders(); // Cargar pedidos recientes
    }

    private function loadMetrics(): void
    {
        $now = now();

        // Inicio del mes actual
        $startMonth = $now->copy()->startOfMonth();
        // Inicio del mes anterior
        $lastMonth = $now->copy()->subMonth()->startOfMonth();
        // Fin del mes anterior
        $endLast = $now->copy()->subMonth()->endOfMonth();

        // Valor total ventas del mes actual
        $salesThisMonth = Order::where('created_at', '>=', $startMonth)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->sum('total');

        // Valor total ventas mes anterior
        $salesLastMonth = Order::whereBetween('created_at', [$lastMonth, $endLast])
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->sum('total');

        // Cantidad total ventas mes actual
        $ordersThis = Order::where('created_at', '>=', $startMonth)->count();
        // Cantidad total ventas mes anterior
        $ordersLast = Order::whereBetween('created_at', [$lastMonth, $endLast])->count();

        // Usuarios creados en el mes actual
        $clientsThis = User::where('created_at', '>=', $startMonth)->count();

        /**
         * Delta ventas [((ventas mes actual - ventas mes pasado) / ventas mes pasado) * 100]
         * Delta pedidos [((pedidos mes actual - pedidos mes pasado) / pedidos mes pasado) * 100]
        */

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
            'low_stock' => Product::lowStock()->count(), // Total de productos con bajo stock
            'out_of_stock' => Product::outOfStock()->count(), // Total de productos sin stock
            'pending_orders' => Order::byStatus('pending')->count(), // Total de productos en estado pendiente
        ];
    }

    private function loadRecentOrders(): void
    {
        $this->recentOrders = Order::with('items')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'total' => $o->total,
                'status' => $o->status,
                'number' => $o->number,
                'customer' => $o->customer_name,
                'status_label' => $o->status_label,
                'status_color' => $o->status_color,
                'created_at' => $o->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    private function loadLowStock(): void
    {
        $this->lowStockProducts = Product::with('subcategory.category')
            ->lowStock()
            ->orWhere(fn ($q) => $q->outOfStock())
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'sku' => $p->sku,
                'name' => $p->name,
                'stock' => $p->stock,
                'is_out' => $p->is_out_of_stock,
                'category' => $p->subcategory?->category?->name,
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
                'day' => $date->translatedFormat('D'), // Eje. lun., mar., mié. etc.
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
