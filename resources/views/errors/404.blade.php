<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        404
        —
        Página
        no
        encontrada
    </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen px-4">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 rounded-full bg-indigo-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="text-6xl font-bold text-gray-900 mb-2">
            404
        </h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-3">
            Página
            no
            encontrada
        </h2>
        <p class="text-gray-500 mb-8">
            La
            página
            que
            buscas
            no
            existe
            o
            fue
            movida.
        </p>
        <div class="flex gap-3 justify-center">
            <a href="{{ route('shop') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                Ir
                a
                la
                tienda
            </a>
            <a href="{{ url()->previous() }}"
                class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                Volver
            </a>
        </div>
    </div>
</body>

</html>
