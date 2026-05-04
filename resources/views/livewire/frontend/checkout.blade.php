<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-semibold text-gray-900 mb-2">Checkout</h1>

    {{-- Steps --}}
    <div class="flex items-center gap-2 mb-8">
        @foreach ([1 => 'Datos', 2 => 'Envío', 3 => 'Pago'] as $n => $label)
            <div class="flex items-center gap-2">
                <div
                    class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold
                            {{ $step >= $n ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                    {{ $step > $n ? '✓' : $n }}
                </div>
                <span
                    class="text-sm {{ $step === $n ? 'font-medium text-gray-800' : 'text-gray-400' }}">{{ $label }}</span>
            </div>
            @if ($n < 3)
                <div class="flex-1 h-px {{ $step > $n ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
            @endif
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Form steps --}}
        <div class="lg:col-span-2">

            {{-- Step 1: Customer data --}}
            @if ($step === 1)
                <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
                    <h2 class="font-semibold text-gray-800">Tus datos</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-gray-600">Nombre completo *</label>
                            <input wire:model="customerName" type="text"
                                class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('customerName') border-red-400 @enderror">
                            @error('customerName')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600">Email *</label>
                            <input wire:model="customerEmail" type="email"
                                class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('customerEmail') border-red-400 @enderror">
                            @error('customerEmail')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600">Teléfono</label>
                            <input wire:model="customerPhone" type="text"
                                class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button wire:click="nextStep"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                            Continuar →
                        </button>
                    </div>
                </div>
            @endif

            {{-- Step 2: Shipping --}}
            @if ($step === 2)
                <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
                    <h2 class="font-semibold text-gray-800">Dirección de envío</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-gray-600">Dirección *</label>
                            <input wire:model="shippingAddress" type="text" placeholder="Calle, número, apto..."
                                class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('shippingAddress') border-red-400 @enderror">
                            @error('shippingAddress')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600">Ciudad *</label>
                            <input wire:model="shippingCity" type="text"
                                class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('shippingCity') border-red-400 @enderror">
                            @error('shippingCity')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600">Departamento *</label>
                            <input wire:model="shippingDepartment" type="text"
                                class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('shippingDepartment') border-red-400 @enderror">
                            @error('shippingDepartment')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600">Código postal</label>
                            <input wire:model="shippingZip" type="text"
                                class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="flex justify-between pt-2">
                        <button wire:click="prevStep" class="text-sm text-gray-500 hover:text-gray-700">←
                            Volver</button>
                        <button wire:click="nextStep"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                            Continuar →
                        </button>
                    </div>
                </div>
            @endif

            {{-- Step 3: Payment --}}
            @if ($step === 3)
                <div class="space-y-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
                        <h2 class="font-semibold text-gray-800">Método de pago</h2>
                        <div class="space-y-2">
                            @foreach (['transfer' => 'Transferencia bancaria', 'cash' => 'Contra entrega (efectivo)', 'nequi' => 'Nequi', 'daviplata' => 'Daviplata'] as $value => $label)
                                <label
                                    class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition-colors
                                             {{ $paymentMethod === $value ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">
                                    <input wire:model.live="paymentMethod" type="radio" value="{{ $value }}"
                                        class="text-indigo-600">
                                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>

                        {{-- Coupon --}}
                        <div>
                            <label class="text-xs font-medium text-gray-600">Cupón de descuento</label>
                            <div class="flex gap-2 mt-1">
                                <input wire:model="couponCode" type="text" placeholder="CÓDIGO"
                                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <button wire:click="applyCoupon"
                                    class="bg-gray-800 hover:bg-gray-900 text-white px-3 py-2 rounded-lg text-xs font-medium">
                                    Aplicar
                                </button>
                            </div>
                            @if ($couponError)
                                <p class="text-xs text-red-500 mt-1">{{ $couponError }}</p>
                            @endif
                            @if ($appliedCoupon)
                                <p class="text-xs text-green-600 mt-1">✓ Cupón {{ $appliedCoupon['code'] }} aplicado
                                </p>
                            @endif
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-600">Notas del pedido (opcional)</label>
                            <textarea wire:model="notes" rows="2" placeholder="Instrucciones especiales de entrega..."
                                class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button wire:click="prevStep" class="text-sm text-gray-500 hover:text-gray-700">←
                            Volver</button>
                        <button wire:click="placeOrder"
                            class="bg-green-600 hover:bg-green-700 text-white px-8 py-2.5 rounded-lg text-sm font-medium transition-colors">
                            Confirmar pedido
                        </button>
                    </div>
                </div>
            @endif
        </div>

        {{-- Order summary sidebar --}}
        <div class="space-y-3">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-semibold text-gray-800 mb-4">Tu pedido</h2>
                <div class="space-y-2">
                    @foreach ($cartItems as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $item['name'] }} <span
                                    class="text-gray-400">×{{ $item['quantity'] }}</span></span>
                            <span class="font-medium">${{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-100 mt-4 pt-4 space-y-2 text-sm">
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
                    <div
                        class="flex justify-between font-semibold text-base text-gray-900 pt-2 border-t border-gray-100">
                        <span>Total</span>
                        <span>${{ number_format($this->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
