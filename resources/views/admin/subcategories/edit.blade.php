<x-admin-layout :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'route' => route('admin.dashboard'),
    ],
    [
        'name' => 'Subcategorías',
        'route' => route('admin.subcategories.index'),
    ],
    [
        'name' => $subcategory->name,
    ],
]">
    <div class="card">
        <form action="{{ route('admin.subcategories.update', $subcategory) }}" method="POST">
            @method('PUT')
            @csrf

            <x-validation-errors class="mb-4"></x-validation-errors>

            <div class="mb-4">
                <x-label class="mb-2">Nombre</x-label>
                <x-input class="w-full" placeholder="Por favor ingrese el nombre de la categoría" name="name"
                    value="{{ old('name', $subcategory->name) }}"></x-input>
            </div>

            <div class="mb-4">
                <x-label class="mb-2">Categoría</x-label>
                <x-select name="category_id" id="category_id" class="w-full">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($category->id == $subcategory->category_id)>{{ $category->name }}</option>
                    @endforeach
                </x-select>
            </div>

            <div class="flex justify-end space-x-2">
                <x-danger-button onclick="confirmDelete()">Eliminar</x-danger-button>
                <x-button>Actualizar</x-button>
            </div>
        </form>
    </div>
</x-admin-layout>
