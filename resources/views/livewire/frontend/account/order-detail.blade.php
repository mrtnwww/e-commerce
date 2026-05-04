<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('account.orders') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Mis pedidos</a>
        <h1 class="text-2xl font-semibold text-gray-900">Pedido {{ $order->number }}</h1>
    </div>

    {{-- Status timeline --}}
    @php
        $steps = [
            'pending' => ['label' => 'Recibido', 'icon' => '📋'],
            'processing' => ['label' => 'En proceso', 'icon' => '⚙️'],
            'shipped' => ['label' => 'Enviado', 'icon' => '🚚'],
            'delivered' => ['label' => 'Entregado', 'icon' => '✅'],
        ];
        $statusOrder = array_keys($steps);
        $currentIndex = array_search($order->status, $statusOrder) ?? -1;
    @endphp

    @if (!in_array($order->status, ['cancelled', 'refunded']))
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
            <div class="flex items-center justify-between">
                @foreach ($steps as $key => $step)
                    @php $stepIndex = array_search($key, $statusOrder); @endphp
                    <div class="flex flex-col items-center flex-1">
                        <div
                            class="w-10 h-10 rounded-full flex items-center justify-center text-lg
                                    {{ $stepIndex <= $currentIndex ? 'bg-indigo-100' : 'bg-gray-100' }}">
                            {{ $step['icon'] }}
                        </div>
                        <p
                            class="text-xs mt-1 font-medium {{ $stepIndex <= $currentIndex ? 'text-indigo-700' : 'text-gray-400' }}">
                            {{ $step['label'] }}
                        </p>
                        @if ($key === 'shipped' && $order->shipped_at)
                            <p class="text-xs text-gray-400">{{ $order->shipped_at->format('d/m/Y') }}</p>
                        @elseif($key === 'delivered' && $order->delivered_at)
                            <p class="text-xs text-gray-400">{{ $order->delivered_at->format('d/m/Y') }}</p>
                        @elseif($key === 'pending')
                            <p class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y') }}</p>
                        @endif
                    </div>
                    @if (!$loop->last)
                        <div
                            class="flex-1 h-0.5 mb-6 {{ $stepIndex < $currentIndex ? 'bg-indigo-400' : 'bg-gray-200' }}">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
            <p class="text-sm font-medium text-red-700">
                {{ $order->status === 'cancelled' ? '❌ Pedido cancelado' : '↩️ Pedido reembolsado' }}
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

        {{-- Customer info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Datos del cliente</h2>
            <p class="text-sm font-medium text-gray-800">{{ $order->customer_name }}</p>
            <p class="text-sm text-gray-500">{{ $order->customer_email }}</p>
            @if ($order->customer_phone)
                <p class="text-sm text-gray-500">{{ $order->customer_phone }}</p>
            @endif
        </div>

        {{-- Shipping address --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Dirección de envío</h2>
            <p class="text-sm font-medium text-gray-800">{{ $order->shipping_address }}</p>
            <p class="text-sm text-gray-500">{{ $order->shipping_city }}, {{ $order->shipping_department }}</p>
            @if ($order->shipping_zip)
                <p class="text-sm text-gray-500">CP: {{ $order->shipping_zip }}</p>
            @endif
        </div>

        {{-- Payment info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Pago</h2>
            <p class="text-sm font-medium text-gray-800 capitalize">{{ $order->payment_method ?? '—' }}</p>
            <p class="text-sm {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">
                {{ $order->payment_status === 'paid' ? '✓ Pagado' : 'Pendiente de pago' }}
            </p>
            @if ($order->paid_at)
                <p class="text-xs text-gray-400 mt-1">{{ $order->paid_at->format('d/m/Y H:i') }}</p>
            @endif
        </div>

        {{-- Order meta --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Información del pedido</h2>
            <p class="text-sm text-gray-500">Fecha: <span
                    class="text-gray-800 font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span></p>
            @if ($order->notes)
                <p class="text-sm text-gray-500 mt-1">Notas: <span class="text-gray-700">{{ $order->notes }}</span></p>
            @endif
        </div>
    </div>

    {{-- Products --}}
    <div class="bg-white rounded-xl border border-gray-200 mb-4">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-800">Productos</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach ($order->items as $item)
                <div class="flex items-center gap-4 px-5 py-4">
                    {{-- Product image --}}
                    <div class="w-14 h-14 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                        @if ($item->product?->images)
                            <img src="{{ asset('storage/' . $item->product->images[0]) }}"
                                alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 text-xs">IMG</div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $item->product_name }}</p>
                        @if ($item->product_sku)
                            <p class="text-xs text-gray-400">SKU: {{ $item->product_sku }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-0.5">
                            ${{ number_format($item->unit_price, 0, ',', '.') }} × {{ $item->quantity }}
                        </p>
                    </div>

                    <div class="text-right shrink-0">
                        <p class="text-sm font-semibold text-gray-900">${{ number_format($item->total, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Totals --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if ($order->discount > 0)
                <div class="flex justify-between text-green-600">
                    <span>Descuento</span>
                    <span>−${{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between text-gray-600">
                <span>Envío</span>
                <span>{{ $order->shipping_cost > 0 ? '$' . number_format($order->shipping_cost, 0, ',', '.') : 'Gratis' }}</span>
            </div>
            <div class="flex justify-between font-semibold text-base text-gray-900 pt-3 border-t border-gray-100">
                <span>Total</span>
                <span>${{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

</div>
