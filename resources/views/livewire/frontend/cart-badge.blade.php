<span>
    @if ($count > 0)
        <span
            class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</span>
