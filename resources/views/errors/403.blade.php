<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        403
        —
        Acceso
        no
        autorizado
    </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen px-4">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-12 h-12 text-red-600">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" />
            </svg>

        </div>
        <h1 class="text-6xl font-bold text-gray-900 mb-2">
            403
        </h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-3">
            Acceso
            no
            autorizado
        </h2>
        <p class="text-gray-500 mb-8">
            No
            tienes
            permisos
            para
            acceder
            a
            esta
            página.
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
