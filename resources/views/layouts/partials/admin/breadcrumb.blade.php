<nav class="mb-4">
    @if (count($breadcrumbs))
        <nav>
            <ol class="flex flex-wrap">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li
                        class="text-sm leading-normal mr-2 text-slate-700 dark:text-white {{ !$loop->first ? 'before:float-left before:content-["/"] before:pr-2' : '' }}">
                        @isset($breadcrumb['route'])
                            <a href="{{ $breadcrumb['route'] }}" class="opacity-50">{{ $breadcrumb['name'] }}</a>
                        @else
                            {{ $breadcrumb['name'] }}
                        @endisset
                    </li>
                @endforeach
            </ol>

            @if (count($breadcrumbs) > 1)
                <h6 class="font-bold dark:text-white">
                    {{-- acceder al ultimo elemento del array --}}
                    {{ end($breadcrumbs)['name'] }}
                </h6>
            @endif
        </nav>
    @endif
</nav>
