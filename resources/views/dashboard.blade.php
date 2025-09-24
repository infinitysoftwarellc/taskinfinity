<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold">Dashboard</h1>
        <p class="mt-4">Este é o conteúdo de uma página normal, sem a sidebar detalhada.</p>
        <p>Ela ocupa todo o espaço ao lado do menu de ícones.</p>
    </main>

</x-app-layout>
