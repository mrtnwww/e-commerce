<x-admin-layout :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'route' => route('admin.dashboard'),
    ],
    [
        'name' => 'Categorías',
        'route' => route('admin.categories.index'),
    ],
    [
        'name' => $category->name,
    ],
]">
    <div class="card">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @method('PUT')
            @csrf

            <x-validation-errors class="mb-4"></x-validation-errors>

            <div class="mb-4">
                <x-label class="mb-2">Nombre</x-label>
                <x-input class="w-full" placeholder="Por favor ingrese el nombre de la categoría" name="name"
                    value="{{ old('name', $category->name) }}"></x-input>
            </div>

            <div class="mb-4">
                <x-label class="mb-2">Familia</x-label>
                <x-select name="family_id" id="family_id" class="w-full">
                    @foreach ($families as $family)
                        <option value="{{ $family->id }}" @selected($category->family_id == $family->id)>{{ $family->name }}</option>
                    @endforeach
                </x-select>
            </div>

            <div class="flex justify-end">
                <x-button>Actualizar</x-button>
            </div>
        </form>
    </div>
</x-admin-layout>
