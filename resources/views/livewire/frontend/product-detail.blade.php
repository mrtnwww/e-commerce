<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <nav class="text-xs text-gray-400 mb-6 flex items-center gap-1">
        <a href="{{ route('shop') }}" class="hover:text-indigo-600">Tienda</a>
        @if ($product->subcategory)
            <span>/</span>
            <a href="{{ route('shop', ['categoria' => $product->subcategory->category_id]) }}"
                class="hover:text-indigo-600">{{ $product->subcategory->category->name }}</a>
            <span>/</span>
            <span class="text-gray-600">{{ $product->name }}</span>
        @endif
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="space-y-3">
            <div class="aspect-square rounded-2xl overflow-hidden bg-gray-100">
                @if ($product->images && count($product->images) > 0)
                    <img src="{{ asset('storage/' . $product->images[$activeImage]) }}" alt="{{ $product->name }}"
                        class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300 text-lg">Sin imagen</div>
                @endif
            </div>
            @if ($product->images && count($product->images) > 1)
                <div class="flex gap-2 overflow-x-auto">
                    @foreach ($product->images as $i => $img)
                        <button wire:click="setImage({{ $i }})"
                            class="w-16 h-16 rounded-lg overflow-hidden border-2 shrink-0 transition-colors
                                       {{ $activeImage === $i ? 'border-indigo-500' : 'border-transparent' }}">
                            <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-5">
            <div>
                @if ($product->subcategory)
                    <p class="text-xs text-indigo-600 font-medium uppercase tracking-wider mb-1">
                        {{ $product->subcategory->name }}
                    </p>
                @endif
                <h1 class="text-2xl font-semibold text-gray-900">{{ $product->name }}</h1>
                @if ($product->short_description)
                    <p class="text-gray-500 mt-2 text-sm">{{ $product->short_description }}</p>
                @endif
            </div>

            <div class="flex items-baseline gap-3">
                <span
                    class="text-3xl font-bold text-gray-900">${{ number_format($product->price, 0, ',', '.') }}</span>
                @if ($product->compare_price)
                    <span
                        class="text-lg text-gray-400 line-through">${{ number_format($product->compare_price, 0, ',', '.') }}</span>
                    <span class="text-sm font-semibold bg-red-100 text-red-700 px-2 py-0.5 rounded-full">
                        -{{ $product->discount_percentage }}%
                    </span>
                @endif
            </div>

            <div>
                @if ($product->is_out_of_stock)
                    <span class="inline-flex items-center gap-1.5 text-sm text-red-600 font-medium">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Agotado
                    </span>
                @elseif($product->is_low_stock)
                    <span class="inline-flex items-center gap-1.5 text-sm text-amber-600 font-medium">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Últimas {{ $product->stock }} unidades
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-sm text-green-600 font-medium">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span> Disponible
                    </span>
                @endif
            </div>

            @if (!$product->is_out_of_stock)
                <div class="flex items-center gap-4">
                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                        <button wire:click="decrementQty"
                            class="px-3 py-2 text-gray-600 hover:bg-gray-50 transition-colors text-lg leading-none">−</button>
                        <span class="px-4 py-2 text-sm font-medium border-x border-gray-300">{{ $quantity }}</span>
                        <button wire:click="incrementQty"
                            class="px-3 py-2 text-gray-600 hover:bg-gray-50 transition-colors text-lg leading-none">+</button>
                    </div>

                    <button wire:click="addToCart"
                        class="flex-1 py-2.5 rounded-lg font-medium text-sm transition-all
                                   {{ $addedToCart ? 'bg-green-600 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white' }}">
                        {{ $addedToCart ? '✓ Añadido al carrito' : 'Agregar al carrito' }}
                    </button>
                </div>
            @endif

            @if ($product->description)
                <div class="border-t border-gray-100 pt-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">Descripción</h3>
                    <div class="text-sm text-gray-600 leading-relaxed prose prose-sm max-w-none">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            @endif

            @if ($product->sku)
                <p class="text-xs text-gray-400">SKU: {{ $product->sku }}</p>
            @endif
        </div>
    </div>

    @if ($relatedProducts->count() > 0)
        <div class="mt-16">
            <h2 class="text-lg font-semibold text-gray-800 mb-5">Productos relacionados</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach ($relatedProducts as $related)
                    <a href="{{ route('product', $related->slug) }}"
                        class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                        <div class="aspect-square bg-gray-100 overflow-hidden">
                            <img src="{{ $related->main_image }}" alt="{{ $related->name }}"
                                class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <div class="p-3">
                            <p class="text-sm font-medium text-gray-800 line-clamp-2">{{ $related->name }}</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">
                                ${{ number_format($related->price, 0, ',', '.') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
