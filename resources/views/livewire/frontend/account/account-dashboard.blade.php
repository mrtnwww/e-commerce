{{-- resources/views/livewire/frontend/account/dashboard.blade.php --}}
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-semibold text-gray-900 mb-8">Mi cuenta</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total pedidos</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">{{ $totalOrders }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total gastado</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">${{ number_format($totalSpent, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Cuenta</p>
            <p class="text-base font-medium text-gray-900 mt-1">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-800">Últimos pedidos</h2>
            <a href="{{ route('account.orders') }}" class="text-xs text-indigo-600 hover:underline">Ver todos</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 border-b border-gray-100">
                        <th class="text-left px-5 py-3">Pedido</th>
                        <th class="text-left px-5 py-3">Fecha</th>
                        <th class="text-left px-5 py-3">Estado</th>
                        <th class="text-right px-5 py-3">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
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
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-5 py-3 font-mono text-xs font-semibold text-indigo-700">{{ $order->number }}
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                            <td class="px-5 py-3">
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold">
                                ${{ number_format($order->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-gray-400">Aún no tienes pedidos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex gap-3">
        <a href="{{ route('account.profile') }}" class="text-sm text-indigo-600 hover:underline">Editar perfil →</a>
        <a href="{{ route('shop') }}" class="text-sm text-gray-500 hover:underline">Ir a la tienda →</a>
    </div>
</div>
