<x-slot name="title">Productos</x-slot>

<div class="space-y-4">
    <div class="flex flex-wrap items-center gap-3">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre o SKU..."
            class="flex-1 min-w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select wire:model.live="subcategory"
            class="border border-gray-300 rounded-lg px-3 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todas las subcategorías</option>
            @foreach ($subcategories as $sub)
                <option value="{{ $sub->id }}">{{ $sub->category->name }} › {{ $sub->name }}</option>
            @endforeach
        </select>

        <select wire:model.live="stockFilter"
            class="border border-gray-300 rounded-lg px-3 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todo el stock</option>
            <option value="low">Stock bajo</option>
            <option value="out">Sin stock</option>
        </select>

        <button wire:click="openCreate"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
            <i class="fa-solid fa-plus"></i>
            <span>Nuevo producto</span>
        </button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3">Producto</th>
                        <th class="text-left px-5 py-3">Categoría</th>
                        <th class="text-center px-5 py-3">Stock</th>
                        <th class="text-right px-5 py-3">Precio</th>
                        <th class="text-center px-5 py-3">Activo</th>
                        <th class="text-right px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($product->images)
                                        <img src="{{ asset('storage/' . $product->images[0]) }}"
                                            class="w-10 h-10 rounded-lg object-cover bg-gray-100"
                                            alt="{{ $product->name }}">
                                    @else
                                        <div
                                            class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                                            IMG</div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-400">SKU: {{ $product->sku ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500">
                                {{ $product->subcategory?->category?->name }} ›
                                {{ $product->subcategory?->name ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span
                                    class="text-xs font-semibold px-2 py-0.5 rounded-full
                                    {{ $product->stock <= 0
                                        ? 'bg-red-100 text-red-700'
                                        : ($product->is_low_stock
                                            ? 'bg-amber-100 text-amber-700'
                                            : 'bg-green-100 text-green-700') }}">
                                    {{ $product->stock }} uds.
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold">
                                ${{ number_format($product->price, 0, ',', '.') }}
                                @if ($product->compare_price)
                                    <span
                                        class="text-xs text-gray-400 line-through ml-1">${{ number_format($product->compare_price, 0, ',', '.') }}</span>
                                @endif
                            </td>

                            <td class="px-5 py-3 text-center">
                                <button wire:click="toggleActive({{ $product->id }})"
                                    class="relative inline-flex h-5 w-9 rounded-full transition-colors {{ $product->active ? 'bg-indigo-600' : 'bg-gray-300' }}">
                                    <span
                                        class="w-4 h-4 rounded-full bg-white shadow inline-block mt-0.5 transition-transform {{ $product->active ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                </button>
                            </td>

                            <td class="px-5 py-3 text-right flex justify-end gap-2">
                                <button wire:click="openEdit({{ $product->id }})"
                                    class="text-xs text-indigo-600 hover:underline">Editar</button>
                                <button wire:click="handleConfirmDelete({{ $product->id }})"
                                    class="text-xs text-red-500 hover:underline">Eliminar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400">No se encontraron productos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $products->links() }}
        </div>
    </div>

    @if ($showForm)
        <x-modal title="{{ $editId ? 'Editar' : 'Nuevo' }} producto" maxWidth="max-w-2xl">
            <form wire:submit="save">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-xs font-medium text-gray-600">Nombre *</label>
                        <input wire:model="name" type="text"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-400 @enderror">
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-600">Precio *</label>
                        <input wire:model="price" type="number" step="0.01" min="0"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('price') border-red-400 @enderror">
                        @error('price')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-600">Precio anterior (tachado)</label>
                        <input wire:model="comparePrice" type="number" step="0.01" min="0"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-600">Stock *</label>
                        <input wire:model="stock" type="number" min="0"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-600">Alerta stock bajo</label>
                        <input wire:model="lowStockThreshold" type="number" min="1"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-600">SKU</label>
                        <input wire:model="sku" type="text"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('sku') border-red-400 @enderror">
                        @error('sku')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-600">Subcategoría</label>
                        <select wire:model="subcategoryId"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Sin subcategoría</option>
                            @foreach ($subcategories as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->category->name }} ›
                                    {{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="text-xs font-medium text-gray-600">Descripción corta</label>
                        <textarea wire:model="shortDescription" rows="2"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="col-span-2">
                        <label class="text-xs font-medium text-gray-600">Descripción completa</label>
                        <textarea wire:model="description" rows="4"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="col-span-2">
                        <label class="text-xs font-medium text-gray-600">Imágenes</label>
                        <input wire:model="uploadedImages" type="file" multiple accept="image/*"
                            class="mt-1 w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <div wire:loading wire:target="uploadedImages" class="text-xs text-indigo-600 mt-1">
                            Cargando imágenes...</div>
                    </div>

                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input wire:model="active" type="checkbox"
                                class="rounded border-gray-300 text-indigo-600">
                            Activo
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input wire:model="featured" type="checkbox"
                                class="rounded border-gray-300 text-indigo-600">
                            Destacado
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" wire:click="$set('showForm', false)"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed" wire:target="save, uploadedImages"
                        class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                        {{ $editId ? 'Actualizar' : 'Crear producto' }}
                    </button>
                </div>
            </form>
        </x-modal>
    @endif

    @if ($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl">
                <h2 class="font-semibold text-gray-800 mb-2">¿Eliminar producto?</h2>
                <p class="text-sm text-gray-500 mb-6">Esta acción no se puede deshacer.</p>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmDelete', false)"
                        class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                    <button wire:click="delete"
                        class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">Eliminar</button>
                </div>
            </div>
        </div>
    @endif
</div>
