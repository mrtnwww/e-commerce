<x-slot name="title">Clientes</x-slot>

<div class="space-y-4">

    <div class="flex items-center gap-3">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre o email..."
            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 cursor-pointer hover:text-gray-800" wire:click="sortBy('name')">
                            Cliente @if ($sortBy === 'name')
                                {{ $sortDir === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th class="text-left px-5 py-3 cursor-pointer hover:text-gray-800"
                            wire:click="handleSortBy('created_at')">
                            Registrado @if ($sortBy === 'created_at')
                                {{ $sortDir === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th class="text-center px-5 py-3">Pedidos</th>
                        <th class="text-right px-5 py-3">Total gastado</th>
                        <th class="text-right px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $customer->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $customer->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500">
                                {{ $customer->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-medium">
                                    {{ $customer->orders_count }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-800">
                                ${{ number_format($customer->orders_sum_total ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-right">
                                <button wire:click="viewCustomer({{ $customer->id }})"
                                    class="text-xs text-indigo-600 hover:underline">
                                    Ver detalle
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                                No se encontraron clientes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $customers->links() }}
        </div>
    </div>

    {{-- Customer detail modal --}}
    @if ($showModal && $selectedCustomer)
        <x-modal title="Detalle del cliente">
            {{-- Info --}}
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl font-bold shrink-0">
                    {{ strtoupper(substr($selectedCustomer->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-base font-semibold text-gray-800">{{ $selectedCustomer->name }}</p>
                    <p class="text-sm text-gray-500">{{ $selectedCustomer->email }}</p>
                    @if ($selectedCustomer->phone)
                        <p class="text-sm text-gray-400">{{ $selectedCustomer->phone }}</p>
                    @endif
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500">Total pedidos</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $selectedCustomer->orders_count }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500">Total gastado</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">
                        ${{ number_format($selectedCustomer->orders->whereNotIn('status', ['cancelled', 'refunded'])->sum('total'), 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Recent orders --}}
            @if ($selectedCustomer->orders->count() > 0)
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Últimos pedidos
                    </p>
                    <div class="space-y-2">
                        @foreach ($selectedCustomer->orders as $order)
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
                            <div class="flex items-center justify-between text-sm bg-gray-50 rounded-lg px-3 py-2">
                                <span
                                    class="font-mono text-xs font-semibold text-indigo-700">{{ $order->number }}</span>
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $order->status_label }}
                                </span>
                                <span class="font-medium">${{ number_format($order->total, 0, ',', '.') }}</span>
                                <span class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="text-xs text-gray-400 pt-2 border-t border-gray-100">
                Cliente desde {{ $selectedCustomer->created_at->translatedFormat('d \d\e F \d\e Y') }}
            </div>
        </x-modal>
    @endif
</div>
