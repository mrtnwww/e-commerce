@php
    $links = [
        [
            'icon' => 'fa-solid fa-table-columns',
            'name' => 'Dashboard',
            'route' => route('admin.dashboard'),
            'active' => request()->routeIs('admin.dashboard'), // validar si la ruta actual corresponde a la ruta con nombre `admin.dashboard`
        ],
        // Familias de productos
        [
            'icon' => 'fa-solid fa-box-open',
            'name' => 'Familias',
            'route' => route('admin.families.index'),
            'active' => request()->routeIs('admin.families.*'),
        ],
        // Categorias de productos
        [
            'icon' => 'fa-solid fa-tags',
            'name' => 'Categorías',
            'route' => route('admin.categories.index'),
            'active' => request()->routeIs('admin.categories.*'),
        ],
        // Subcategorias de productos}
        [
            'icon' => 'fa-solid fa-tag',
            'name' => 'Subcategorías',
            'route' => route('admin.subcategories.index'),
            'active' => request()->routeIs('admin.subcategories.*')
        ]
    ];
@endphp

<aside id="top-bar-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-full transition-transform -translate-x-full bg-white dark:bg-gray-900 sm:translate-x-0"
    :class="{
        'translate-x-0 ease-in': siderbarOpen,
        '-traslate-x-full ease-out': !siderbarOpen
    }"
    aria-label="Sidebar">
    <div class="h-full px-3 py-4 overflow-y-auto border-e border-default">
        <ul class="space-y-2 font-medium mt-[4rem]">
            @foreach ($links as $link)
                <li>
                    <a href="{{ $link['route'] }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ $link['active'] ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <span class="inline-flex w-6 h-6 justify-center items-center">
                            <i class="{{ $link['icon'] }}"></i>
                        </span>
                        <span class="ms-3">{{ $link['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</aside>
