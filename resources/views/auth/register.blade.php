<x-guest-layout>
    <div class="min-h-screen bg-gray-50 flex">

        {{-- Panel izquierdo --}}
        <div class="hidden lg:flex lg:w-1/2 bg-slate-900 flex-col justify-between p-12 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-violet-600/20 rounded-full blur-3xl">
                </div>
                <div class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl">
                </div>
                <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-indigo-600/10 rounded-full blur-2xl">
                </div>
            </div>

            <div class="relative z-10">
                <a href="{{ route('shop') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z" />
                        </svg>
                    </div>
                    <span class="text-white font-semibold text-lg">{{ config('app.name') }}</span>
                </a>
            </div>

            <div class="relative z-10 space-y-6">
                <div>
                    <h2 class="text-3xl font-bold text-white leading-tight">
                        Únete
                        a
                        nosotros
                    </h2>
                    <p class="text-slate-400 mt-2 leading-relaxed">
                        Crea
                        tu
                        cuenta
                        en
                        segundos
                        y
                        empieza
                        a
                        disfrutar
                        de
                        una
                        experiencia
                        de
                        compra
                        personalizada
                        y
                        segura.
                    </p>
                </div>
                <div class="space-y-4">
                    @foreach ([['icon' => '🎁', 'title' => 'Descuentos exclusivos', 'desc' => 'Ofertas solo para miembros'], ['icon' => '🚚', 'title' => 'Seguimiento de pedidos', 'desc' => 'Sabe dónde está tu compra'], ['icon' => '🔒', 'title' => 'Compra segura', 'desc' => 'Tus datos siempre protegidos']] as $b)
                        <div class="flex items-start gap-3">
                            <div
                                class="w-9 h-9 rounded-lg bg-slate-800 flex items-center justify-center text-base shrink-0">
                                {{ $b['icon'] }}
                            </div>
                            <div>
                                <p class="text-white text-sm font-medium">
                                    {{ $b['title'] }}
                                </p>
                                <p class="text-slate-500 text-xs mt-0.5">
                                    {{ $b['desc'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <p class="relative z-10 text-slate-600 text-xs">
                ©
                {{ date('Y') }}
                {{ config('app.name') }}
            </p>
        </div>

        {{-- Panel derecho --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 overflow-y-auto">
            <div class="w-full max-w-md space-y-8">

                {{-- Logo móvil --}}
                <div class="flex lg:hidden justify-center">
                    <a href="{{ route('shop') }}" class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z" />
                            </svg>
                        </div>
                        <span class="font-semibold text-gray-900 text-lg">{{ config('app.name') }}</span>
                    </a>
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Crear
                        cuenta
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">
                        ¿Ya
                        tienes
                        cuenta?
                        <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">Inicia
                            sesión</a>
                    </p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nombre
                            completo</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                            autocomplete="name" placeholder="Juan Pérez"
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm placeholder-gray-400
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all
                                  @error('name') border-red-400 bg-red-50 @enderror">
                        @error('name')
                            <p class="text-xs text-red-500 mt-1.5">
                                ⚠
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Correo
                            electrónico</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                            autocomplete="email" placeholder="tu@correo.com"
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm placeholder-gray-400
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all
                                  @error('email') border-red-400 bg-red-50 @enderror">
                        @error('email')
                            <p class="text-xs text-red-500 mt-1.5">
                                ⚠
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
                        <div class="relative" x-data="{ show: false }">
                            <input id="password" name="password"
                                :type="show ?
                                    'text' :
                                    'password'"
                                required autocomplete="new-password" placeholder="Mínimo 8 caracteres"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 pr-11 text-sm placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all
                                      @error('password') border-red-400 bg-red-50 @enderror">
                            <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-red-500 mt-1.5">
                                ⚠
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar
                            contraseña</label>
                        <div class="relative" x-data="{ show: false }">
                            <input id="password_confirmation" name="password_confirmation"
                                :type="show ?
                                    'text' :
                                    'password'"
                                required autocomplete="new-password" placeholder="Repite tu contraseña"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 pr-11 text-sm placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium
                               py-3 px-4 rounded-xl transition-colors text-sm shadow-sm shadow-indigo-200">
                        Crear
                        cuenta
                        gratis
                    </button>

                    <p class="text-center text-sm text-gray-500">
                        ¿Ya
                        tienes
                        cuenta?
                        <a href="{{ route('login') }}"
                            class="text-indigo-600 hover:text-indigo-700 font-medium">Inicia
                            sesión</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
