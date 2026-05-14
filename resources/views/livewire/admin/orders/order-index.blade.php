<x-slot name="title">Pedidos</x-slot>

<div class="space-y-4">

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-wrap items-center gap-3">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por #pedido, nombre o email..."
            class="flex-1 min-w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">

        <select wire:model.live="status"
            class="border border-gray-300 rounded-lg px-3 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todos los estados</option>
            @foreach ($statuses as $key => $info)
                <option value="{{ $key }}">{{ $info['label'] }} ({{ $countsByStatus[$key] ?? 0 }})</option>
            @endforeach
        </select>

        <select wire:model.live="perPage"
            class="border border-gray-300 rounded-lg px-3 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="15">15 por página</option>
            <option value="30">30 por página</option>
            <option value="50">50 por página</option>
        </select>
    </div>

    {{-- Status tabs --}}
    <div class="flex gap-2 flex-wrap">
        <button wire:click="$set('status', '')"
            class="text-xs px-3 py-1.5 rounded-full border transition-colors
                       {{ $status === '' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}">
            Todos
        </button>
        @foreach ($statuses as $key => $info)
            <button wire:click="$set('status', '{{ $key }}')"
                class="text-xs px-3 py-1.5 rounded-full border transition-colors
                           {{ $status === $key ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}">
                {{ $info['label'] }} <span class="opacity-70">({{ $countsByStatus[$key] ?? 0 }})</span>
            </button>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 cursor-pointer hover:text-gray-800"
                            wire:click="sortBy('number')">
                            # Pedido @if ($sortBy === 'number')
                                {{ $sortDir === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th class="text-left px-5 py-3 cursor-pointer hover:text-gray-800"
                            wire:click="sortBy('customer_name')">
                            Cliente @if ($sortBy === 'customer_name')
                                {{ $sortDir === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th class="text-left px-5 py-3">Estado</th>
                        <th class="text-left px-5 py-3">Pago</th>
                        <th class="text-right px-5 py-3 cursor-pointer hover:text-gray-800"
                            wire:click="sortBy('total')">
                            Total @if ($sortBy === 'total')
                                {{ $sortDir === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th class="text-right px-5 py-3 cursor-pointer hover:text-gray-800"
                            wire:click="handleSortBy('created_at')">
                            Fecha @if ($sortBy === 'created_at')
                                {{ $sortDir === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th class="text-right px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-700',
                                'processing' => 'bg-blue-100 text-blue-700',
                                'shipped' => 'bg-purple-100 text-purple-700',
                                'delivered' => 'bg-green-100 text-green-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                'refunded' => 'bg-gray-100 text-gray-600',
                            ];
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3 font-mono text-xs font-semibold text-indigo-700">{{ $order->number }}
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $order->customer_name }}</p>
                                <p class="text-xs text-gray-400">{{ $order->customer_email }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <select wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                    class="text-xs rounded-full px-2 py-1 border-0 cursor-pointer
                                               {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    @foreach (\App\Models\Order::STATUSES as $key => $info)
                                        <option value="{{ $key }}"
                                            {{ $order->status === $key ? 'selected' : '' }}>
                                            {{ $info['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="text-xs {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $order->payment_method ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold">
                                ${{ number_format($order->total, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right text-xs text-gray-400">
                                {{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3 text-right">
                                <button wire:click="viewOrder({{ $order->id }})"
                                    class="text-xs text-indigo-600 hover:underline">Ver</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400">No se encontraron pedidos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
    </div>

    {{-- Order detail modal --}}
    @if ($showModal && $selectedOrder)
        <x-modal title="Pedido {{ $selectedOrder->number }}" maxWidth="max-w-2xl">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-500">Cliente</p>
                        <p class="font-medium">{{ $selectedOrder->customer_name }}</p>
                        <p class="text-gray-500">{{ $selectedOrder->customer_email }}</p>
                        <p class="text-gray-500">{{ $selectedOrder->customer_phone }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Dirección de envío</p>
                        <p class="font-medium">{{ $selectedOrder->shipping_address }}</p>
                        <p class="text-gray-500">{{ $selectedOrder->shipping_city }},
                            {{ $selectedOrder->shipping_department }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-2">Productos</p>
                    <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-50 text-xs text-gray-500">
                            <tr>
                                <th class="text-left px-4 py-2">Producto</th>
                                <th class="text-center px-4 py-2">Cant.</th>
                                <th class="text-right px-4 py-2">Precio</th>
                                <th class="text-right px-4 py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($selectedOrder->items as $item)
                                <tr class="border-t border-gray-100">
                                    <td class="px-4 py-2">{{ $item->product_name }}</td>
                                    <td class="px-4 py-2 text-center">{{ $item->quantity }}</td>
                                    <td class="px-4 py-2 text-right">
                                        ${{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-right font-medium">
                                        ${{ number_format($item->total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end gap-8 text-sm border-t border-gray-100 pt-4">
                    <div class="text-right space-y-1">
                        <p class="text-gray-500">Subtotal: <span
                                class="text-gray-800">${{ number_format($selectedOrder->subtotal, 0, ',', '.') }}</span>
                        </p>
                        @if ($selectedOrder->discount > 0)
                            <p class="text-gray-500">Descuento: <span
                                    class="text-green-600">-${{ number_format($selectedOrder->discount, 0, ',', '.') }}</span>
                            </p>
                        @endif
                        <p class="text-gray-500">Envío: <span
                                class="text-gray-800">${{ number_format($selectedOrder->shipping_cost, 0, ',', '.') }}</span>
                        </p>
                        <p class="font-semibold text-lg">Total:
                            ${{ number_format($selectedOrder->total, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </x-modal>
    @endif
</div>
