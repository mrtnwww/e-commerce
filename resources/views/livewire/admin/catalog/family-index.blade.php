<x-slot name="title">Familias</x-slot>

<div class="space-y-4">
    <div class="flex flex-wrap items-center gap-3">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar familia..."
            class="flex-1 min-w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <button wire:click="openCreate"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fa-solid fa-plus"></i>
            <span>Nueva familia</span>
        </button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-500 bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-5 py-3">Familia</th>
                    <th class="text-left px-5 py-3">Descripción</th>
                    <th class="text-center px-5 py-3">Categorías</th>
                    <th class="text-center px-5 py-3">Activa</th>
                    <th class="text-right px-5 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($families as $family)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if ($family->image)
                                    <img src="{{ asset('storage/' . $family->image) }}"
                                        class="w-9 h-9 rounded-lg object-cover bg-gray-100">
                                @else
                                    <div
                                        class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-400 text-xs font-bold">
                                        {{ strtoupper(substr($family->name, 0, 2)) }}
                                    </div>
                                @endif
                                <span class="font-medium text-gray-800">{{ $family->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-400 max-w-xs truncate">
                            {{ $family->description ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                {{ $family->categories_count }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <button wire:click="toggleActive({{ $family->id }})"
                                class="relative inline-flex h-5 w-9 rounded-full transition-colors {{ $family->active ? 'bg-indigo-600' : 'bg-gray-300' }}">
                                <span
                                    class="w-4 h-4 rounded-full bg-white shadow inline-block mt-0.5 transition-transform {{ $family->active ? 'translate-x-4' : 'translate-x-1' }}"></span>
                            </button>
                        </td>
                        <td class="px-5 py-3 text-right flex justify-end gap-2">
                            <button wire:click="openEdit({{ $family->id }})"
                                class="text-xs text-indigo-600 hover:underline">Editar</button>
                            <button wire:click="handleConfirmDelete({{ $family->id }})"
                                class="text-xs text-red-500 hover:underline">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-400">No hay familias creadas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-100">{{ $families->links() }}</div>
    </div>

    @if ($showForm)
        <x-modal title="{{ $editId ? 'Editar' : 'Nueva' }} familia" maxWidth="max-w-2xl">
            <form wire:submit="save" class="space-y-4">
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
                <div>
                    <label class="text-xs font-medium text-gray-600">Imagen</label>
                    <input wire:model="image" type="file" accept="image/*"
                        class="mt-1 w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700">
                    <div wire:loading wire:target="image" class="text-xs text-indigo-600 mt-1">Cargando imágenes...
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                    <input wire:model="active" type="checkbox" class="rounded border-gray-300 text-indigo-600">
                    Activa
                </label>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" wire:click="$set('showForm', false)"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed" wire:target="save, image"
                        class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                        {{ $editId ? 'Actualizar' : 'Crear familia' }}
                    </button>
                </div>
            </form>
        </x-modal>
    @endif

    @if ($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl">
                <h2 class="font-semibold text-gray-800 mb-2">¿Eliminar familia?</h2>
                <p class="text-sm text-gray-500 mb-6">También se eliminarán sus categorías y subcategorías.</p>
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
