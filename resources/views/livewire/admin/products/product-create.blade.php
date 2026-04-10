<div class="card">
    <div class="mb-4">
        <x-label class="mb-1">Código</x-label>
        <x-input wire:model.live="product.sku" class="w-full" placeholder="Ingrese el código del producto"></x-input>
    </div>

    <div class="mb-4">
        <x-label class="mb-1">Nombre</x-label>
        <x-input wire:model.live="product.name" class="w-full" placeholder="Ingrese el nombre del producto"></x-input>
    </div>

    <div class="mb-4">
        <x-label class="mb-1">Descripción</x-label>
        <x-textarea wire:model.live="product.description" class="w-full" placeholder="Ingrese el nombre del producto"></x-textarea>
    </div>

    <div class="mb-4">
        <x-label class="mb-2">Familias</x-label>
        <x-select class="w-full" wire:model.live="family_id">
            <option value="" disabled>Seleccione una familia</option>
            @foreach ($families as $family)
                <option value="{{ $family->id }}">{{ $family->name }}</option>
            @endforeach
        </x-select>
    </div>

    <div class="mb-4">
        <x-label>Categorías</x-label>
        <x-select class="w-full" wire:model="product.subcategory_id">
            <option value="" disabled>Seleccione una categoría</option>
            @foreach ($this->categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </x-select>
    </div>
</div>
