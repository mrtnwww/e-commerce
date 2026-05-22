<x-slot name="title">Dashboard</x-slot>

<div class="space-y-6">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center mb-3">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
            <p class="text-2xl font-semibold text-gray-900">${{ number_format($metrics['sales'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Ventas del mes</p>
            <p class="text-xs mt-2 {{ $metrics['sales_delta'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
                {{ $metrics['sales_delta'] >= 0 ? '▲' : '▼' }} {{ abs($metrics['sales_delta']) }}% vs mes anterior
            </p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center mb-3">
                <i class="fa-solid fa-bag-shopping text-green-600"></i>
            </div>
            <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['orders']) }}</p>
            <p class="text-xs text-gray-500 mt-1">Pedidos del mes</p>
            <p class="text-xs mt-2 {{ $metrics['orders_delta'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
                {{ $metrics['orders_delta'] >= 0 ? '▲' : '▼' }} {{ abs($metrics['orders_delta']) }}% vs mes anterior
            </p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center mb-3">
                <i class="fa-solid fa-user-check text-amber-600"></i>
            </div>
            <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['clients']) }}</p>
            <p class="text-xs text-gray-500 mt-1">Clientes nuevos</p>
            <p class="text-xs text-gray-400 mt-2">Este mes</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center mb-3">
                <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
            </div>
            <p class="text-2xl font-semibold text-gray-900">{{ $metrics['low_stock'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Productos stock bajo</p>
            <p class="text-xs text-red-500 mt-2">{{ $metrics['out_of_stock'] }} sin stock</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="flex flex-col justify-between bg-white rounded-xl border border-gray-200 p-5 lg:col-span-2">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Ventas últimos 7 días</h3>
            @php
                $maxSale = collect($weeklySales)->max('total') ?: 1;
            @endphp

            <div class="flex items-end gap-1">
                @foreach ($weeklySales as $day)
                    @php
                        $hasData = $day['total'] > 0;
                        $maxBarPx = $hasData ? 100 : 0;
                        $barHeight = max(4, round(($day['total'] / $maxSale) * $maxBarPx));
                        $label = $day['total'] > 0 ? '$' . number_format($day['total'] / 1000, 1) . 'k' : '$0';
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="h-5 flex items-end">
                            <span class="text-xs text-gray-400 leading-none">{{ $label }}</span>
                        </div>

                        <div class="w-full flex items-end" style="height: {{ $maxBarPx }}px;">
                            @if ($hasData)
                                <div class="w-full rounded-t-md bg-indigo-500 hover:bg-indigo-600 transition-colors"
                                    style="height: {{ $barHeight }}px"></div>
                            @else
                                <div class="w-full rounded-t-sm bg-indigo-200" style="height: 3px"></div>
                            @endif
                        </div>

                        <span class="text-xs text-gray-400 mt-1">{{ $day['day'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Stock crítico</h3>
                <a href="{{ route('admin.products') }}?stockFilter=low"
                    class="text-xs text-indigo-600 hover:underline">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($lowStockProducts as $product)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-800 truncate">{{ $product['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $product['category'] }}</p>
                        </div>
                        <span
                            class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ $product['is_out'] ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $product['stock'] }} uds.
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Sin productos críticos</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Últimos pedidos</h3>
            <a href="{{ route('admin.orders') }}" class="text-xs text-indigo-600 hover:underline">Ver todos</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 border-b border-gray-100">
                        <th class="text-left px-5 py-3">Pedido</th>
                        <th class="text-left px-5 py-3">Cliente</th>
                        <th class="text-left px-5 py-3">Estado</th>
                        <th class="text-right px-5 py-3">Total</th>
                        <th class="text-right px-5 py-3">Hace</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3 font-mono text-xs font-medium text-indigo-700">
                                <a
                                    href="{{ route('admin.orders') }}?search={{ $order['number'] }}">{{ $order['number'] }}</a>
                            </td>
                            <td class="px-5 py-3 text-gray-700">{{ $order['customer'] }}</td>
                            <td class="px-5 py-3">
                                @php
                                    $colors = [
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'processing' => 'bg-blue-100 text-blue-700',
                                        'shipped' => 'bg-purple-100 text-purple-700',
                                        'delivered' => 'bg-green-100 text-green-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                        'refunded' => 'bg-gray-100 text-gray-600',
                                    ];
                                @endphp
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full {{ $colors[$order['status']] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $order['status_label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-medium">
                                ${{ number_format($order['total'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right text-gray-400 text-xs">{{ $order['created_at'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400">No se encontraron pedidos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
