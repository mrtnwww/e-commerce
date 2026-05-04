<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('account.dashboard') }}" class="text-gray-400 hover:text-gray-600">← Mi cuenta</a>
        <h1 class="text-2xl font-semibold text-gray-900">Mis pedidos</h1>
    </div>

    <div class="flex gap-2 flex-wrap mb-4">
        <button wire:click="$set('statusFilter', '')"
            class="text-xs px-3 py-1.5 rounded-full border transition-colors {{ $statusFilter === '' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300' }}">
            Todos
        </button>
        @foreach ($statuses as $key => $info)
            <button wire:click="$set('statusFilter', '{{ $key }}')"
                class="text-xs px-3 py-1.5 rounded-full border transition-colors {{ $statusFilter === $key ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300' }}">
                {{ $info['label'] }}
            </button>
        @endforeach
    </div>

    <div class="space-y-3">
        @forelse($orders as $order)
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
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-mono text-sm font-semibold text-indigo-700">{{ $order->number }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d \d\e F \d\e Y') }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $order->items->count() }} producto{{ $order->items->count() !== 1 ? 's' : '' }} ·
                            Envío a {{ $order->shipping_city }}, {{ $order->shipping_department }}
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <span
                            class="text-xs px-2 py-0.5 rounded-full {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $order->status_label }}
                        </span>
                        <p class="text-base font-semibold text-gray-900 mt-2">
                            ${{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                </div>
                @if ($order->items->count() > 0)
                    <div class="mt-3 pt-3 border-t border-gray-100 flex gap-3 overflow-x-auto">
                        @foreach ($order->items->take(4) as $item)
                            <div class="text-xs text-gray-500 shrink-0">
                                {{ $item->product_name }} ×{{ $item->quantity }}
                            </div>
                        @endforeach
                        @if ($order->items->count() > 4)
                            <span class="text-xs text-gray-400">+{{ $order->items->count() - 4 }} más</span>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="py-16 text-center text-gray-400">
                <p class="text-lg mb-2">No tienes pedidos aún</p>
                <a href="{{ route('shop') }}" class="text-sm text-indigo-600 hover:underline">Ir a la tienda →</a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
</div>
