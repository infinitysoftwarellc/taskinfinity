{{-- This Blade view renders the app settings index interface. --}}
{{-- resources/views/app/settings/index.blade.php --}}
<x-app-layout>
    <div class="flex min-h-screen bg-zinc-100">
        @include('app.shared.navigation')

        <div class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-dashed border-zinc-300 bg-white p-10 text-center shadow-sm">
                <h1 class="text-3xl font-semibold text-zinc-900">Configurações</h1>
                <p class="mt-4 text-base text-zinc-600">
                    Escolha um item no menu para começar a configurar o aplicativo.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
