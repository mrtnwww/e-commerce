<span>
    @if ($count > 0)
        <span
            class="ml-auto bg-red-500 text-white text-xs font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 sidebar-fade"
            :style="open ? 'opacity:1;max-width:40px' : 'opacity:0;max-width:0'">
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</span>
