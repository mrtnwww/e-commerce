<x-slot name="title">Descuentos y cupones</x-slot>

<div class="space-y-4">
    <div class="flex items-center gap-3">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar código..."
            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <button wire:click="openCreate"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fa-solid fa-plus"></i>
            <span>Nuevo cupón</span>
        </button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-500 bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-5 py-3">Código</th>
                    <th class="text-left px-5 py-3">Descuento</th>
                    <th class="text-left px-5 py-3">Mínimo</th>
                    <th class="text-center px-5 py-3">Usos</th>
                    <th class="text-left px-5 py-3">Vence</th>
                    <th class="text-center px-5 py-3">Activo</th>
                    <th class="text-right px-5 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($discounts as $d)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono font-semibold text-indigo-700 text-xs">{{ $d->code }}</td>
                        <td class="px-5 py-3 font-medium">
                            @if ($d->type === 'percentage')
                                {{ $d->value }}%
                            @else
                                ${{ number_format($d->value, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">
                            {{ $d->minimum_order ? '$' . number_format($d->minimum_order, 0, ',', '.') : '—' }}
                        </td>
                        <td class="px-5 py-3 text-center text-xs">
                            {{ $d->used_count }}{{ $d->max_uses ? ' / ' . $d->max_uses : '' }}
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">
                            {{ $d->expires_at ? $d->expires_at->format('d/m/Y') : 'Sin vencimiento' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <button wire:click="toggleActive({{ $d->id }})"
                                class="relative inline-flex h-5 w-9 rounded-full transition-colors {{ $d->active ? 'bg-indigo-600' : 'bg-gray-300' }}">
                                <span
                                    class="w-4 h-4 rounded-full bg-white shadow inline-block mt-0.5 transition-transform {{ $d->active ? 'translate-x-4' : 'translate-x-1' }}"></span>
                            </button>
                        </td>
                        <td class="px-5 py-3 text-right flex justify-end gap-2">
                            <button wire:click="openEdit({{ $d->id }})"
                                class="text-xs text-indigo-600 hover:underline">Editar</button>
                            <button wire:click="handleConfirmDelete({{ $d->id }})"
                                class="text-xs text-red-500 hover:underline">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">No hay cupones creados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-100">{{ $discounts->links() }}</div>
    </div>

    @if ($showForm)
        <x-modal title="{{ $editId ? 'Editar' : 'Nuevo' }} cupón" maxWidth="max-w-lg">
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-xs font-medium text-gray-600">Código *</label>
                        <input wire:model="code" type="text" placeholder="VERANO20"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('code') border-red-400 @enderror">
                        @error('code')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600">Tipo *</label>
                        <select wire:model="type"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="percentage">Porcentaje (%)</option>
                            <option value="fixed">Valor fijo ($)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600">Valor *</label>
                        <input wire:model="value" type="number" step="0.01" min="0"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('value') border-red-400 @enderror">
                        @error('value')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600">Pedido mínimo</label>
                        <input wire:model="minimumOrder" type="number" min="0" placeholder="0"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600">Máximo de usos</label>
                        <input wire:model="maxUses" type="number" min="1" placeholder="Ilimitado"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-medium text-gray-600">Fecha de vencimiento</label>
                        <input wire:model="expiresAt" type="date"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('expiresAt') border-red-400 @enderror">
                        @error('expiresAt')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input wire:model="active" type="checkbox" class="rounded border-gray-300 text-indigo-600">
                        Activo
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" wire:click="$set('showForm', false)"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                    <button type="submit" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed" wire:target="save"
                        class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">{{ $editId ? 'Actualizar' : 'Crear' }}</button>
                </div>
            </form>
        </x-modal>
    @endif

    @if ($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl">
                <h2 class="font-semibold text-gray-800 mb-2">¿Eliminar cupón?</h2>
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
