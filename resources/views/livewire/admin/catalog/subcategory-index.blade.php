<x-slot name="title">Subcategorías</x-slot>

<div class="space-y-4">
    <div class="flex flex-wrap items-center gap-3">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar subcategoría..."
            class="flex-1 min-w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select wire:model.live="categoryFilter"
            class="border border-gray-300 rounded-lg px-3 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todas las categorías</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->family->name }} › {{ $cat->name }}</option>
            @endforeach
        </select>
        <button wire:click="openCreate"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fa-solid fa-plus"></i>
            <span>Nueva subcategoría</span>
        </button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-500 bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-5 py-3">Subcategoría</th>
                    <th class="text-left px-5 py-3">Categoría</th>
                    <th class="text-center px-5 py-3">Productos</th>
                    <th class="text-center px-5 py-3">Activa</th>
                    <th class="text-right px-5 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subcategories as $sub)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $sub->name }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $sub->category->family->name }} ›
                            {{ $sub->category->name }}</td>
                        <td class="px-5 py-3 text-center text-xs text-gray-500">{{ $sub->products_count }}</td>
                        <td class="px-5 py-3 text-center">
                            <button wire:click="toggleActive({{ $sub->id }})"
                                class="relative inline-flex h-5 w-9 rounded-full transition-colors {{ $sub->active ? 'bg-indigo-600' : 'bg-gray-300' }}">
                                <span
                                    class="w-4 h-4 rounded-full bg-white shadow inline-block mt-0.5 transition-transform {{ $sub->active ? 'translate-x-4' : 'translate-x-1' }}"></span>
                            </button>
                        </td>
                        <td class="px-5 py-3 text-right flex justify-end gap-2">
                            <button wire:click="openEdit({{ $sub->id }})"
                                class="text-xs text-indigo-600 hover:underline">Editar</button>
                            <button wire:click="confirmDelete({{ $sub->id }})"
                                class="text-xs text-red-500 hover:underline">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-400">No hay subcategorías.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-100">{{ $subcategories->links() }}</div>
    </div>

    @if ($showForm)
        <x-modal title="{{ $editId ? 'Editar' : 'Nueva' }} subcategoría" maxWidth="max-w-2xl">
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="text-xs font-medium text-gray-600">Categoría *</label>
                    <select wire:model="categoryId"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('categoryId') border-red-400 @enderror">
                        <option value="">Selecciona una categoría</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->family->name }} › {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoryId')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Nombre *</label>
                    <input wire:model="name" type="text"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Descripción</label>
                    <textarea wire:model="description" rows="3"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                    <input wire:model="active" type="checkbox" class="rounded border-gray-300 text-indigo-600">
                    Activa
                </label>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" wire:click="$set('showForm', false)"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">{{ $editId ? 'Actualizar' : 'Crear' }}</button>
                </div>
            </form>
        </x-modal>
    @endif

    @if ($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl">
                <h2 class="font-semibold text-gray-800 mb-2">¿Eliminar subcategoría?</h2>
                <p class="text-sm text-gray-500 mb-6">Los productos asociados quedarán sin subcategoría.</p>
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
