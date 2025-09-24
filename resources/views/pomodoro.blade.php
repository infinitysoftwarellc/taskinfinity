<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cronômetro Pomodoro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Coluna do Timer --}}
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        @livewire('pomodoro-timer')
                    </div>
                </div>
            </div>

            {{-- Coluna do Histórico --}}
            <div class="lg:col-span-1">
                 @livewire('pomodoro-history')
            </div>
        </div>
    </div>
</x-app-layout>