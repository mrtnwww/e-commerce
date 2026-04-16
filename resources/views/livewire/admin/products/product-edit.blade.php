<div>
    <form wire:submit="store">
        <figure class="mb-4 relative">
            <div class="absolute top-8 right-8">
                <label class="flex items-center px-4 py-2 rounded-lg bg-white text-gray-700 cursor-pointer">
                    <i class="fas fa-camera mr-2"></i>
                    Actualizar imagen

                    <input type="file" accept="image/*" wire:model="image" hidden>
                </label>
            </div>
            <img src="{{ $image ? $image->temporaryUrl() : Storage::url($productEdit['image_path']) }}"
                class="aspect-[16/9] object-cover object-center w-full" alt="">
        </figure>

        <x-validation-errors></x-validation-errors>

        <div class="card">
            <div class="mb-4">
                <x-label class="mb-1">Código</x-label>
                <x-input wire:model.live="productEdit.sku" class="w-full"
                    placeholder="Ingrese el código del producto"></x-input>
            </div>

            <div class="mb-4">
                <x-label class="mb-1">Nombre</x-label>
                <x-input wire:model.live="productEdit.name" class="w-full"
                    placeholder="Ingrese el nombre del producto"></x-input>
            </div>

            <div class="mb-4">
                <x-label class="mb-1">Descripción</x-label>
                <x-textarea wire:model.live="productEdit.description" class="w-full"
                    placeholder="Ingrese el nombre del producto"></x-textarea>
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
                <x-select class="w-full" wire:model.live="category_id">
                    <option value="" disabled>Seleccione una categoría</option>
                    @foreach ($this->categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-select>
            </div>

            <div class="mb-4">
                <x-label>Subcategorías</x-label>
                <x-select class="w-full" wire:model="productEdit.subcategory_id">
                    <option value="" disabled>Seleccione una subcategoría</option>
                    @foreach ($this->subcategories as $subcategory)
                        <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                    @endforeach
                </x-select>
            </div>

            <div class="mb-4">
                <x-label>Precio</x-label>
                <x-input type="number" class="w-full" placeholder="Precio del producto" step="0.01"
                    wire:model="productEdit.price"></x-input>
            </div>
        </div>

        <div class="flex justify-end mt-4">
            <x-button>Actualizar producto</x-button>
        </div>|
    </form>
</div>
