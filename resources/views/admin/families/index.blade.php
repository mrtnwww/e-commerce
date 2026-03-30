<x-admin-layout :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'route' => route('admin.dashboard'),
    ],
    [
        'name' => 'Familias',
    ],
]">
    {{-- slot con nombre --}}
    <x-slot name="action">
        <a href="{{ route('admin.families.create') }}" type="button" class="btn btn-blue">Nuevo</a>
    </x-slot>

    @if (count($families))
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nombre
                        </th>
                        <th scope="col" class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($families as $family)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                            <td class="px-6 py-4">
                                {{ $family->id }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $family->name }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.families.edit', $family) }}" class="btn btn-blue">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $families->links() }}
        </div>
    @else
        <div class="flex items-start sm:items-center p-4 mb-4 text-blue-900 text-fg-brand-strong rounded-base bg-blue-300"
            role="alert">
            <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <p>Todavía no se han creado familias de productos.</p>
        </div>
    @endif

</x-admin-layout>
