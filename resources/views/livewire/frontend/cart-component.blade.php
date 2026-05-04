<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-semibold text-gray-900 mb-8">Mi carrito</h1>

    @if (empty($items))
        <div class="text-center py-20">
            <p class="text-gray-400 text-lg mb-4">Tu carrito está vacío</p>
            <a href="{{ route('shop') }}"
                class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                Ir a la tienda
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Items list --}}
            <div class="lg:col-span-2 space-y-3">
                @foreach ($items as $item)
                    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4">
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                            class="w-20 h-20 rounded-lg object-cover bg-gray-100 shrink-0">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-800 truncate">{{ $item['name'] }}</h3>
                            <p class="text-sm text-gray-500 mt-0.5">${{ number_format($item['price'], 0, ',', '.') }}
                                c/u</p>
                            <div class="flex items-center gap-3 mt-2">
                                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                    <button
                                        wire:click="updateQuantity({{ $item['cart_id'] }}, {{ $item['quantity'] - 1 }})"
                                        class="px-2.5 py-1 text-gray-600 hover:bg-gray-50 transition-colors leading-none">−</button>
                                    <span
                                        class="px-3 py-1 text-sm font-medium border-x border-gray-300">{{ $item['quantity'] }}</span>
                                    <button
                                        wire:click="updateQuantity({{ $item['cart_id'] }}, {{ $item['quantity'] + 1 }})"
                                        @if ($item['quantity'] >= $item['stock']) disabled @endif
                                        class="px-2.5 py-1 text-gray-600 hover:bg-gray-50 transition-colors leading-none disabled:opacity-40">+</button>
                                </div>
                                <button wire:click="removeItem({{ $item['cart_id'] }})"
                                    class="text-xs text-red-500 hover:underline">Eliminar</button>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-semibold text-gray-900">${{ number_format($item['subtotal'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Summary --}}
            <div class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                    <h2 class="font-semibold text-gray-800">Resumen del pedido</h2>

                    {{-- Coupon --}}
                    <div>
                        <label class="text-xs font-medium text-gray-600">Cupón de descuento</label>
                        <div class="flex gap-2 mt-1">
                            <input wire:model="couponCode" type="text" placeholder="CÓDIGO"
                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <button wire:click="applyCoupon"
                                class="bg-gray-800 hover:bg-gray-900 text-white px-3 py-2 rounded-lg text-xs font-medium transition-colors">
                                Aplicar
                            </button>
                        </div>
                        @if ($couponError)
                            <p class="text-xs text-red-500 mt-1">{{ $couponError }}</p>
                        @endif
                        @if ($appliedCoupon)
                            <p class="text-xs text-green-600 mt-1">✓ Cupón
                                <strong>{{ $appliedCoupon['code'] }}</strong> aplicado
                            </p>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($this->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if ($this->discountAmount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Descuento</span>
                                <span>−${{ number_format($this->discountAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-gray-600">
                            <span>Envío</span>
                            <span class="text-gray-400">Se calcula en el checkout</span>
                        </div>
                        <div
                            class="flex justify-between font-semibold text-base text-gray-900 pt-2 border-t border-gray-100">
                            <span>Total</span>
                            <span>${{ number_format($this->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout') }}"
                        class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg font-medium text-sm transition-colors">
                        Proceder al pago
                    </a>

                    <a href="{{ route('shop') }}"
                        class="block text-center text-xs text-gray-500 hover:text-indigo-600 transition-colors">
                        ← Seguir comprando
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
