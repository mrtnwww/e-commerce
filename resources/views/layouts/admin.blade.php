<!DOCTYPE html>
<html lang="es">

{{-- Google Fonts - Lato --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,700;0,900;1,400&display=swap"
    rel="stylesheet">

{{-- Font Awesome --}}
<script src="https://kit.fontawesome.com/0bb6fe9eb2.js" crossorigin="anonymous"></script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Panel
        Administrativo
    </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .sidebar {
            transition: width 300ms ease;
        }

        .sidebar-fade {
            transition: opacity 200ms ease, max-width 250ms ease;
            white-space: nowrap;
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased" x-data="{ open: true }">

    <div class="flex h-screen overflow-hidden">
        {{-- Menu lateral --}}
        <aside class="sidebar shrink-0 bg-slate-900 flex flex-col overflow-hidden"
            :style="open ? 'width:224px' : 'width:64px'">
            {{-- Logo --}}
            <div class="px-4 py-5 border-b border-slate-700 flex items-center gap-3 overflow-hidden">
                <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-cart-shopping text-white"></i>
                </div>
                <span class="sidebar-fade text-white font-semibold text-sm"
                    :style="open ?
                        'opacity:1;max-width:200px' :
                        'opacity:0;max-width:0'">
                    {{ config('app.name') }}
                </span>
            </div>

            {{-- Navegacion --}}
            <nav class="flex-1 py-3 overflow-y-auto overflow-x-hidden px-2 space-y-0.5">
                @php
                    $sections = [
                        'Principal' => [
                            [
                                'route' => 'admin.dashboard',
                                'label' => 'Dashboard',
                                'icon' => 'fa-solid fa-border-all',
                            ],
                            [
                                'route' => 'admin.orders',
                                'label' => 'Pedidos',
                                'icon' => 'fa-solid fa-bag-shopping',
                            ],
                            [
                                'route' => 'admin.customers',
                                'label' => 'Clientes',
                                'icon' => 'fa-solid fa-user-group',
                            ],
                        ],
                        'Catálogo' => [
                            [
                                'route' => 'admin.products',
                                'label' => 'Productos',
                                'icon' => 'fa-solid fa-box-open',
                            ],
                            [
                                'route' => 'admin.families',
                                'label' => 'Familias',
                                'icon' => 'fa-solid fa-layer-group',
                            ],
                            [
                                'route' => 'admin.categories',
                                'label' => 'Categorías',
                                'icon' => 'fa-solid fa-tag',
                            ],
                            [
                                'route' => 'admin.subcategories',
                                'label' => 'Subcategorías',
                                'icon' => 'fa-solid fa-tags',
                            ],
                        ],
                        'Tienda' => [
                            [
                                'route' => 'admin.discounts',
                                'label' => 'Descuentos',
                                'icon' => 'fa-solid fa-ticket-simple',
                            ],
                            [
                                'route' => 'admin.banners',
                                'label' => 'Banners',
                                'icon' => 'fa-regular fa-images',
                            ],
                        ],
                        'Configuración' => [
                            [
                                'route' => 'admin.settings',
                                'label' => 'Opciones',
                                'icon' => 'fa-solid fa-gear',
                            ],
                        ],
                    ];
                @endphp

                @foreach ($sections as $sectionLabel => $items)
                    <div class="overflow-hidden"
                        :style="open ?
                            'max-height:32px;opacity:1;margin-top:12px' :
                            'max-height:0;opacity:0;margin-top:0'"
                        style="transition:max-height 250ms ease,opacity 200ms ease,margin-top 250ms ease">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest px-2 pb-1">
                            {{ $sectionLabel }}
                        </p>
                    </div>

                    @foreach ($items as $item)
                        @php $active = request()->routeIs($item['route']); @endphp
                        <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                              {{ $active
                                  ? 'bg-indigo-600/20 text-indigo-300 border-l-2 border-indigo-500'
                                  : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="{{ $item['icon'] }}"></i>
                            <span class="sidebar-fade flex-1"
                                :style="open ?
                                    'opacity:1;max-width:200px' :
                                    'opacity:0;max-width:0'">
                                {{ $item['label'] }}
                            </span>
                            @if ($item['route'] === 'admin.orders')
                                {{-- Componente livewire para visualizar cantidad de pedidos pendientes --}}
                                <livewire:admin.pending-orders-badge />
                            @endif
                        </a>
                    @endforeach
                @endforeach
            </nav>

            {{-- Footer --}}
            <div class="border-t border-slate-700 px-3 py-3 flex items-center gap-3 overflow-hidden">
                <div
                    class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center
                        text-white text-xs font-bold shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
                </div>
                <div class="sidebar-fade flex-1 min-w-0"
                    :style="open ?
                        'opacity:1;max-width:200px' :
                        'opacity:0;max-width:0'">
                    <p class="text-sm text-white font-medium truncate">
                        {{ Auth::user()->name ?? 'Admin' }}
                    </p>

                    {{-- Logout --}}
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="text-xs text-slate-400 hover:text-red-400 transition-colors">
                        Cerrar
                        sesión
                    </a>
                    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </aside>

        {{-- Vista del layout --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">
            {{-- Barra superior --}}
            <header class="bg-white border-b border-gray-200 px-5 py-3 flex items-center gap-4 shrink-0">
                {{-- Ocultar/Mostrar sidebar --}}
                <button @click="open = !open"
                    class="text-gray-400 hover:text-gray-700 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 class="text-base font-semibold text-gray-800 flex-1">
                    {{ $title ?? 'Dashboard' }}
                </h1>
                <a href="{{ route('shop') }}" target="_blank"
                    class="text-xs text-indigo-600 hover:text-indigo-800 transition-colors">
                    Ver
                    tienda
                    <i class="fa-solid fa-shop"></i>
                </a>
            </header>

            {{-- Toast --}}
            <x-toast />

            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>

</html>
