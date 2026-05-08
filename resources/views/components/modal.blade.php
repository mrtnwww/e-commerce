@props([
    'title' => '',
    'maxWidth' => 'max-w-lg',
    'closeMethod' => 'closeModal',
])

<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" style="margin: 0 !important"
    x-on:keydown.escape.window="$wire.{{ $closeMethod }}()">

    <div class="bg-white rounded-2xl w-full {{ $maxWidth }} shadow-xl flex flex-col"
        style="max-height: calc(100vh - 2rem)">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
            <h2 class="font-semibold text-gray-800">{{ $title }}</h2>
            <button wire:click="{{ $closeMethod }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Body scrollable --}}
        <div class="p-6 overflow-y-auto flex-1">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 shrink-0">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
