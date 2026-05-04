<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex gap-8">

            {{-- Sidebar filters --}}
            <aside class="hidden lg:block w-56 shrink-0">
                <div class="sticky top-32 space-y-6">

                    {{-- Families / Categories --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Categorías</h3>
                        <div class="space-y-1">
                            <button wire:click="$set('familyId', null)"
                                class="w-full text-left text-sm px-3 py-1.5 rounded-lg transition-colors
                                           {{ !$familyId ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                                Todos los productos
                            </button>
                            @foreach ($families as $family)
                                <div>
                                    <button wire:click="$set('familyId', {{ $family->id }})"
                                        class="w-full text-left text-sm px-3 py-1.5 rounded-lg font-medium transition-colors
                                                   {{ $familyId == $family->id ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                        {{ $family->name }}
                                    </button>
                                    @if ($familyId == $family->id)
                                        <div class="ml-4 mt-1 space-y-1">
                                            @foreach ($family->categories as $category)
                                                <button wire:click="$set('categoryId', {{ $category->id }})"
                                                    class="w-full text-left text-xs px-3 py-1 rounded-lg transition-colors
                                                               {{ $categoryId == $category->id ? 'text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-800' }}">
                                                    {{ $category->name }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Price range --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Precio</h3>
                        <div class="flex items-center gap-2">
                            <input wire:model.lazy="priceMin" type="number" placeholder="Min"
                                class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <span class="text-gray-400 text-xs">—</span>
                            <input wire:model.lazy="priceMax" type="number" placeholder="Max"
                                class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- In stock only --}}
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input wire:model.live="onlyInStock" type="checkbox"
                            class="rounded border-gray-300 text-indigo-600">
                        Solo disponibles
                    </label>
                </div>
            </aside>

            {{-- Main content --}}
            <div class="flex-1 min-w-0">

                {{-- Top bar --}}
                <div class="flex items-center justify-between mb-5">
                    <p class="text-sm text-gray-500">
                        {{ $products->total() }} producto{{ $products->total() !== 1 ? 's' : '' }}
                    </p>
                    <select wire:model.live="sortBy"
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="featured">Destacados</option>
                        <option value="newest">Más recientes</option>
                        <option value="price_asc">Precio: menor a mayor</option>
                        <option value="price_desc">Precio: mayor a menor</option>
                    </select>
                </div>

                {{-- Products grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($products as $product)
                        <div
                            class="bg-white rounded-xl border border-gray-200 overflow-hidden group hover:shadow-md transition-shadow">
                            <a href="{{ route('product', $product->slug) }}" class="block">
                                <div class="aspect-square bg-gray-100 overflow-hidden relative">
                                    <img src="{{ $product->main_image }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                                    @if ($product->discount_percentage)
                                        <span
                                            class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                                            -{{ $product->discount_percentage }}%
                                        </span>
                                    @endif

                                    @if ($product->is_out_of_stock)
                                        <div class="absolute inset-0 bg-white/70 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-500">Agotado</span>
                                        </div>
                                    @endif
                                </div>
                            </a>

                            <div class="p-3">
                                <a href="{{ route('product', $product->slug) }}">
                                    <h3
                                        class="text-sm font-medium text-gray-800 line-clamp-2 hover:text-indigo-700 transition-colors">
                                        {{ $product->name }}
                                    </h3>
                                </a>

                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-base font-semibold text-gray-900">
                                        ${{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                    @if ($product->compare_price)
                                        <span class="text-xs text-gray-400 line-through">
                                            ${{ number_format($product->compare_price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>

                                <button wire:click="addToCart({{ $product->id }})" @disabled($product->is_out_of_stock)
                                    class="mt-2 w-full text-xs py-1.5 rounded-lg font-medium transition-colors
                                               {{ $product->is_out_of_stock
                                                   ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                                   : 'bg-indigo-600 hover:bg-indigo-700 text-white' }}">
                                    {{ $product->is_out_of_stock ? 'Agotado' : 'Agregar al carrito' }}
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center text-gray-400">
                            <p class="text-lg mb-2">No se encontraron productos</p>
                            <p class="text-sm">Intenta con otros filtros o términos de búsqueda.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
