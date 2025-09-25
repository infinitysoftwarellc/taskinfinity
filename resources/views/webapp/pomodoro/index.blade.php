<x-app-layout>
    <div class="min-h-screen bg-gray-950 py-12 text-white">
        <div class="mx-auto flex max-w-5xl flex-col gap-8 px-4 sm:px-6 lg:px-8">
            <header class="max-w-3xl">
                <p class="text-xs uppercase tracking-[0.4em] text-white/50">Deep work</p>
                <h1 class="mt-3 text-4xl font-semibold tracking-tight">Pomodoro timer</h1>
                <p class="mt-3 text-sm text-white/60">
                    Run the timer from the server so it keeps going even if you close or reload the page. Customize your focus and
                    break lengths to match your flow.
                </p>
            </header>

            <section>
                <livewire:pomodoro.timer />
            </section>
        </div>
    </div>
</x-app-layout>
