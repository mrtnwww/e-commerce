<div x-data="{ show: false, message: '', type: 'success' }"
    x-on:notify.window="
        message = $event.detail.message;
        type    = $event.detail.type ?? 'success';
        show    = true;
        setTimeout(() => show = false, 3500)
    "
    x-show="show" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150"
    x-transition:leave-end="opacity-0"
    class="fixed bottom-6 right-6 z-50 px-4 py-3 rounded-xl shadow-xl text-sm flex items-center gap-2 cursor-pointer"
    :class="{
        'bg-amber-500 text-white': type === 'warning',
        'bg-gray-900 text-white': type === 'success',
        'bg-red-600 text-white': type === 'error',
    }"
    @click="show = false" style="display:none">

    <span x-show="type === 'success'" class="text-green-400 shrink-0">
        <i class="fa-solid fa-circle-check"></i>
    </span>
    <span x-show="type === 'error'" class="text-red-200 shrink-0">
        <i class="fa-solid fa-circle-xmark"></i>
    </span>
    <span x-show="type === 'warning'" class="text-amber-100 shrink-0">
        <i class="fa-solid fa-triangle-exclamation"></i>
    </span>

    <span x-text="message"></span>

    <button @click.stop="show = false" class="ml-2 opacity-60 hover:opacity-100 transition-opacity shrink-0">
        <i class="fa-solid fa-xmark text-xs"></i>
    </button>
</div>
