<div>
    <form wire:submit="save">
        <x-validation-errors class="mb-4"></x-validation-errors>

        <div class="mb-4">
            <x-label class="mb-2">Familias</x-label>
            <x-select class="w-full" wire:model.live="subcategoryEdit.family_id">
                <option value="" disabled>Seleccione una familia</option>
                @foreach ($families as $family)
                    <option value="{{ $family->id }}">{{ $family->name }}</option>
                @endforeach
            </x-select>
        </div>

        <div class="mb-4">
            <x-label class="mb-2">Categorías</x-label>
            <x-select class="w-full" wire:model.live="subcategoryEdit.category_id">
                <option value="" disabled>Seleccione una categoría</option>
                @foreach ($this->categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </x-select>
        </div>

        <div class="mb-4">
            <x-label class="mb-2">Nombre</x-label>
            <x-input class="w-full" placeholder="Ingrese el nombre de la subcategoría"
                wire:model="subcategoryEdit.name"></x-input>
        </div>

        <div class="flex justify-end">
            <x-button>Actualizar</x-button>
        </div>
    </form>
</div>
