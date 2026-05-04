<x-slot name="title">Opciones de la tienda</x-slot>

<div class="max-w-2xl space-y-6">
    <form wire:submit="save" class="space-y-6">

        {{-- General --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-3">Información general</h2>

            @if ($currentLogo)
                <div>
                    <p class="text-xs text-gray-500 mb-2">Logo actual</p>
                    <img src="{{ asset('storage/' . $currentLogo) }}" class="h-12 object-contain">
                </div>
            @endif

            <div>
                <label class="text-xs font-medium text-gray-600">Logo</label>
                <input wire:model="logo" type="file" accept="image/*"
                    class="mt-1 w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-medium text-gray-600">Nombre de la tienda *</label>
                    <input wire:model="storeName" type="text"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('storeName') border-red-400 @enderror">
                    @error('storeName')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Moneda</label>
                    <select wire:model="storeCurrency"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="COP">COP — Peso colombiano</option>
                        <option value="USD">USD — Dólar</option>
                        <option value="EUR">EUR — Euro</option>
                        <option value="MXN">MXN — Peso mexicano</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Email de contacto *</label>
                    <input wire:model="storeEmail" type="email"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('storeEmail') border-red-400 @enderror">
                    @error('storeEmail')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Teléfono</label>
                    <input wire:model="storePhone" type="text"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600">Dirección</label>
                <textarea wire:model="storeAddress" rows="2"
                    class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
        </div>

        {{-- Shipping --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-3">Envíos</h2>

            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input wire:model.live="freeShipping" type="checkbox" class="rounded border-gray-300 text-indigo-600">
                Envío gratis en todos los pedidos
            </label>

            @if (!$freeShipping)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-600">Costo de envío</label>
                        <input wire:model="shippingCost" type="number" min="0" step="100"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600">Gratis desde (monto mínimo)</label>
                        <input wire:model="freeShippingMin" type="number" min="0" step="1000"
                            placeholder="Dejar vacío para no aplicar"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            @endif
        </div>

        {{-- Social --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-3">Redes sociales</h2>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="text-xs font-medium text-gray-600">Instagram</label>
                    <input wire:model="instagram" type="url" placeholder="https://instagram.com/tutienda"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('instagram') border-red-400 @enderror">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Facebook</label>
                    <input wire:model="facebook" type="url" placeholder="https://facebook.com/tutienda"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('facebook') border-red-400 @enderror">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">WhatsApp (número con código de país)</label>
                    <input wire:model="whatsapp" type="text" placeholder="573001234567"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium text-sm transition-colors">
                Guardar configuración
            </button>
        </div>
    </form>
</div>
