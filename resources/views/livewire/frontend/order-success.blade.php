<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">

    <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
    </div>

    <h1 class="text-3xl font-semibold text-gray-900 mb-2">¡Pedido confirmado!</h1>
    <p class="text-gray-500 mb-1">Gracias por tu compra, <strong>{{ $order->customer_name }}</strong>.</p>
    <p class="text-gray-400 text-sm mb-8">
        Recibirás un correo de confirmación en <strong>{{ $order->customer_email }}</strong>
    </p>

    {{-- Order summary card --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 text-left mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Número de pedido</p>
                <p class="text-lg font-mono font-semibold text-indigo-700">{{ $order->number }}</p>
            </div>
            @php
                $colors = [
                    'pending' => 'bg-amber-100 text-amber-700',
                    'processing' => 'bg-blue-100 text-blue-700',
                    'shipped' => 'bg-purple-100 text-purple-700',
                    'delivered' => 'bg-green-100 text-green-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                ];
            @endphp
            <span
                class="text-xs px-3 py-1 rounded-full font-medium {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                {{ $order->status_label }}
            </span>
        </div>

        {{-- Items --}}
        <div class="divide-y divide-gray-100">
            @foreach ($order->items as $item)
                <div class="flex justify-between py-3 text-sm">
                    <span class="text-gray-700">{{ $item->product_name }} <span
                            class="text-gray-400">×{{ $item->quantity }}</span></span>
                    <span class="font-medium">${{ number_format($item->total, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        {{-- Totals --}}
        <div class="border-t border-gray-100 mt-2 pt-4 space-y-1.5 text-sm">
            <div class="flex justify-between text-gray-500">
                <span>Subtotal</span>
                <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if ($order->discount > 0)
                <div class="flex justify-between text-green-600">
                    <span>Descuento</span>
                    <span>−${{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between text-gray-500">
                <span>Envío</span>
                <span>{{ $order->shipping_cost > 0 ? '$' . number_format($order->shipping_cost, 0, ',', '.') : 'Gratis' }}</span>
            </div>
            <div class="flex justify-between font-semibold text-base text-gray-900 pt-2 border-t border-gray-100">
                <span>Total pagado</span>
                <span>${{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Shipping address --}}
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Dirección de envío</p>
            <p class="text-sm text-gray-700">{{ $order->shipping_address }}</p>
            <p class="text-sm text-gray-500">{{ $order->shipping_city }}, {{ $order->shipping_department }}</p>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        @auth
            <a href="{{ route('account.orders') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                Ver mis pedidos
            </a>
        @endauth
        <a href="{{ route('shop') }}"
            class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
            Seguir comprando
        </a>
    </div>
</div>
