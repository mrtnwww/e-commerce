<x-slot name="title">Banners</x-slot>

<div class="space-y-4">
    <div class="flex justify-end">
        <button wire:click="openCreate"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fa-solid fa-plus"></i>
            <span>Nuevo banner</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($banners as $banner)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="aspect-video bg-gray-100 relative">
                    <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}"
                        class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2 flex gap-2">
                        <span
                            class="text-xs px-2 py-0.5 rounded-full {{ $banner->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $banner->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800">{{ $banner->title }}</h3>
                    @if ($banner->subtitle)
                        <p class="text-sm text-gray-500 mt-1">{{ $banner->subtitle }}</p>
                    @endif
                    <div class="flex items-center justify-between mt-3">
                        <div class="flex gap-2">
                            <button wire:click="openEdit({{ $banner->id }})"
                                class="text-xs text-indigo-600 hover:underline">Editar</button>
                            <button wire:click="toggleActive({{ $banner->id }})"
                                class="text-xs text-gray-500 hover:underline">
                                {{ $banner->active ? 'Desactivar' : 'Activar' }}
                            </button>
                            <button wire:click="handleConfirmDelete({{ $banner->id }})"
                                class="text-xs text-red-500 hover:underline">Eliminar</button>
                        </div>
                        <span class="text-xs text-gray-400">Orden: {{ $banner->order }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 py-16 text-center text-gray-400">No hay banners creados.</div>
        @endforelse
    </div>

    @if ($showForm)
        <x-modal title="{{ $editId ? 'Editar' : 'Nuevo' }} banner" maxWidth="max-w-lg">
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="text-xs font-medium text-gray-600">Título *</label>
                    <input wire:model="title" type="text"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-400 @enderror">
                    @error('title')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Subtítulo</label>
                    <textarea wire:model="subtitle" rows="2"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-600">Texto del botón</label>
                        <input wire:model="buttonText" type="text" placeholder="Ver más"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600">URL del botón</label>
                        <input wire:model="buttonUrl" type="url" placeholder="https://..."
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('buttonUrl') border-red-400 @enderror">
                        @error('buttonUrl')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Imagen
                        {{ $editId ? '(dejar vacío para conservar la actual)' : '*' }}</label>
                    @if ($existingImage && !$image)
                        <img src="{{ asset('storage/' . $existingImage) }}" class="mt-2 h-24 rounded-lg object-cover">
                    @endif
                    <input wire:model="image" type="file" accept="image/*"
                        class="mt-1 w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700">
                    @error('image')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <div wire:loading wire:target="image" class="text-xs text-indigo-600 mt-1">
                        Cargando imágenes...</div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-600">Orden</label>
                        <input wire:model="order" type="number" min="0"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input wire:model="active" type="checkbox" class="rounded border-gray-300 text-indigo-600">
                            Activo
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" wire:click="$set('showForm', false)"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                    <button type="submit" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed" wire:target="image, save"
                        class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">{{ $editId ? 'Actualizar' : 'Crear' }}</button>
                </div>
            </form>
        </x-modal>
    @endif

    @if ($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl">
                <h2 class="font-semibold text-gray-800 mb-2">¿Eliminar banner?</h2>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('confirmDelete', false)"
                        class="px-4 py-2 text-sm border border-gray-300 rounded-lg">Cancelar</button>
                    <button wire:click="delete"
                        class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg">Eliminar</button>
                </div>
            </div>
        </div>
    @endif
</div>
