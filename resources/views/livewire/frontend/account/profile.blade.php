<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('account.dashboard') }}" class="text-gray-400 hover:text-gray-600">← Mi cuenta</a>
        <h1 class="text-2xl font-semibold text-gray-900">Mi perfil</h1>
    </div>

    {{-- Profile form --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4 mb-4">
        <h2 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-3">Información personal</h2>
        <form wire:submit="saveProfile" class="space-y-4">
            <div>
                <label class="text-xs font-medium text-gray-600">Nombre completo *</label>
                <input wire:model="name" type="text"
                    class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600">Email *</label>
                <input wire:model="email" type="email"
                    class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600">Teléfono</label>
                <input wire:model="phone" type="text"
                    class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    {{-- Password change --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
            <h2 class="text-sm font-semibold text-gray-800">Cambiar contraseña</h2>
            <button wire:click="$toggle('showPasswordForm')" class="text-xs text-indigo-600 hover:underline">
                {{ $showPasswordForm ? 'Cancelar' : 'Cambiar' }}
            </button>
        </div>
        @if ($showPasswordForm)
            <form wire:submit="changePassword" class="space-y-4">
                <div>
                    <label class="text-xs font-medium text-gray-600">Contraseña actual *</label>
                    <input wire:model="currentPassword" type="password"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('currentPassword') border-red-400 @enderror">
                    @error('currentPassword')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Nueva contraseña *</label>
                    <input wire:model="newPassword" type="password"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('newPassword') border-red-400 @enderror">
                    @error('newPassword')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Confirmar contraseña *</label>
                    <input wire:model="confirmPassword" type="password"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('confirmPassword') border-red-400 @enderror">
                    @error('confirmPassword')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="bg-gray-900 hover:bg-black text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                        Actualizar contraseña
                    </button>
                </div>
            </form>
        @else
            <p class="text-sm text-gray-400">••••••••••••</p>
        @endif
    </div>
</div>
