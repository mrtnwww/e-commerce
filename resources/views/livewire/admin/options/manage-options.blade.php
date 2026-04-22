<div>
    <section class="rounded-lg bg-white shadow-lg">
        <header class="border-b border-gray-200 px-6 py-3">
            <h1 class="text-lg font-semibold text-gray-700">Opciones</h1>
        </header>

        <div class="p-6">
            <div class="space-y-6">
                @foreach ($options as $option)
                    <div class="p-6 rounded-lg border border-gray-200 relative">
                        <div class="absolute -top-3 bg-white px-4">
                            <span>{{ $option->name }}</span>
                        </div>

                        {{-- Valores --}}
                        <div class="flex flex-wrap">
                            @foreach ($option->features as $feature)
                                @switch($option->type)
                                    @case(1)
                                        {{-- Texto --}}
                                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 mr-4 text-sm font-medium text-gray-600 inset-ring inset-ring-gray-500/10">
                                            {{ $feature->description}}
                                        </span>
                                        @break
                                    @case(2)
                                        {{-- Color --}}
                                        <span class="inline-block h-6 w-6 shadow-lg rounded-full border-2 border-gray-300 mr-4" style="background-color: {{ $feature->value }}">
                                        </span>
                                        @break
                                    @default

                                @endswitch
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
