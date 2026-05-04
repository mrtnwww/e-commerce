<!DOCTYPE html>
<html lang="es">

{{-- Google Fonts - Lato --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&display=swap"
    rel="stylesheet">

{{-- Font Awesome --}}
<script src="https://kit.fontawesome.com/0bb6fe9eb2.js" crossorigin="anonymous"></script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {{ $pageTitle ?? config('app.name') }}
    </title>
    <meta name="description" content="{{ $metaDescription ?? '' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50 text-gray-900 antialiased" x-data="{ mobileMenuOpen: false, cartOpen: false }">
    {{-- ── ADMIN BAR ────────────────────────────────── --}}
    @auth
        @if (auth()->user()->is_admin)
            <div class="bg-slate-900 text-slate-300 text-xs px-4 py-2 flex items-center justify-between sticky top-0 z-50">
                <span>🛠️
                    Estás
                    viendo
                    la
                    tienda
                    como
                    <span class="text-white font-medium">administrador</span></span>
                <a href="{{ route('admin.dashboard') }}"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1 rounded-md font-medium transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    Volver
                    al
                    panel
                </a>
            </div>
        @endif
    @endauth

    {{-- ── NAVBAR ──────────────────────────────────── --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('shop') }}" class="text-xl font-bold text-indigo-600">
                    {{ config('app.name') }}
                </a>

                {{-- Search --}}
                <div class="hidden md:flex flex-1 max-w-xl mx-8">
                    <form action="{{ route('shop') }}" method="GET" class="w-full flex">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Buscar productos..."
                            class="w-full rounded-l-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 rounded-r-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('account.dashboard') }}" class="text-sm text-gray-600 hover:text-indigo-600">
                            Mi
                            cuenta
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="text-sm opacity-80 text-red-500 hover:text-red-600" type="submit">Cerrar
                                sesión</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-indigo-600">Ingresar</a>
                        <a href="{{ route('register') }}"
                            class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            Registrarse
                        </a>
                    @endauth

                    {{-- Cart button --}}
                    <a href="{{ route('cart') }}"
                        class="relative flex items-center gap-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-2 rounded-lg text-sm transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Carrito</span>
                        <livewire:frontend.cart-badge />
                    </a>
                </div>
            </div>
        </div>

        {{-- Category bar --}}
        <div class="border-t border-gray-100 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-6 overflow-x-auto py-2 text-sm">
                    <a href="{{ route('shop') }}"
                        class="text-gray-600 hover:text-indigo-600 whitespace-nowrap">Todos</a>
                    @foreach (\App\Models\Family::active()->orderBy('order')->get() as $family)
                        <a href="{{ route('shop', ['familia' => $family->id]) }}"
                            class="text-gray-600 hover:text-indigo-600 whitespace-nowrap {{ request('familia') == $family->id ? 'text-indigo-600 font-medium' : '' }}">
                            {{ $family->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </nav>

    {{-- ── CONTENT ─────────────────────────────────── --}}
    <main>
        {{ $slot }}
    </main>

    {{-- ── FOOTER ──────────────────────────────────── --}}
    <footer class="bg-slate-900 text-slate-300 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-2 md:grid-cols-4 gap-8">
            <div>
                <h4 class="text-white font-semibold mb-3">
                    {{ config('app.name') }}
                </h4>
                <p class="text-sm text-slate-400">
                    Tu
                    tienda
                    de
                    confianza.
                </p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3">
                    Ayuda
                </h4>
                <ul class="space-y-1 text-sm">
                    <li><a href="#" class="hover:text-white">Seguimiento
                            de
                            pedidos</a>
                    </li>
                    <li><a href="#" class="hover:text-white">Devoluciones</a>
                    </li>
                    <li><a href="#" class="hover:text-white">Contacto</a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3">
                    Legal
                </h4>
                <ul class="space-y-1 text-sm">
                    <li><a href="#" class="hover:text-white">Términos
                            y
                            condiciones</a>
                    </li>
                    <li><a href="#" class="hover:text-white">Política
                            de
                            privacidad</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800 py-4 text-center text-xs text-slate-500">
            &copy;
            {{ date('Y') }}
            {{ config('app.name') }}.
            Todos
            los
            derechos
            reservados.
        </div>
    </footer>

    {{-- Flash notification --}}
    <div x-data="{ show: false, message: '' }"
        x-on:notify.window="message = $event.detail.message; show = true; setTimeout(() => show = false, 3500)"
        x-show="show" x-transition
        class="fixed bottom-6 right-6 z-50 bg-green-600 text-white px-4 py-3 rounded-xl shadow-xl text-sm"
        style="display:none">
        <span x-text="message"></span>
    </div>

    @livewireScripts
</body>

</html>
