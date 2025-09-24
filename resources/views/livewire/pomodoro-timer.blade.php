<div class="p-4 sm:p-10 bg-gray-50 rounded-lg shadow-inner"
     x-data="{
         remaining: @entangle('timeRemaining'),
         timer: null,
         init() {
             this.timer = setInterval(() => {
                 if (this.$wire.get('timerIsRunning')) {
                     this.remaining--;
                 }
             }, 1000);

             $watch('remaining', value => {
                 if (value <= 0) {
                     // Quando o tempo acabar, chama a função para pular o timer
                     this.$wire.skipTimer();
                 }
             });
         },
         formatTime() {
             let minutes = Math.floor(this.remaining / 60);
             let seconds = this.remaining % 60;
             return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
         }
     }"
     x-init="init()">

    {{-- Botões de Modo --}}
    <div class="flex justify-center space-x-2 sm:space-x-4 mb-8">
        <button wire:click="setSessionType('work')"
                :class="{ 'bg-indigo-600 text-white': '{{ $sessionType }}' === 'work', 'bg-white text-gray-700': '{{ $sessionType }}' !== 'work' }"
                class="px-4 py-2 rounded-md font-semibold text-sm sm:text-base shadow transition-colors"
                :disabled="$wire.get('timerIsRunning') && '{{ $sessionType }}' !== 'work'">
            Foco
        </button>
        <button wire:click="setSessionType('short_break')"
                :class="{ 'bg-green-600 text-white': '{{ $sessionType }}' === 'short_break', 'bg-white text-gray-700': '{{ $sessionType }}' !== 'short_break' }"
                class="px-4 py-2 rounded-md font-semibold text-sm sm:text-base shadow transition-colors"
                :disabled="$wire.get('timerIsRunning') && '{{ $sessionType }}' !== 'short_break'">
            Pausa Curta
        </button>
        <button wire:click="setSessionType('long_break')"
                :class="{ 'bg-yellow-500 text-white': '{{ $sessionType }}' === 'long_break', 'bg-white text-gray-700': '{{ $sessionType }}' !== 'long_break' }"
                class="px-4 py-2 rounded-md font-semibold text-sm sm:text-base shadow transition-colors"
                :disabled="$wire.get('timerIsRunning') && '{{ $sessionType }}' !== 'long_break'">
            Pausa Longa
        </button>
    </div>

    {{-- Cronômetro --}}
    <div class="text-center my-10">
        <h1 class="text-7xl sm:text-9xl font-bold text-gray-800 tracking-wider" x-text="formatTime()">
            {{-- O tempo será exibido aqui pelo Alpine.js --}}
        </h1>
    </div>

    {{-- Botões de Controle --}}
    <div class="flex justify-center items-center space-x-4">
        @if (!$this->timerIsRunning)
            <button wire:click="startTimer" wire:loading.attr="disabled"
                    class="w-32 px-8 py-4 bg-green-500 text-white font-bold rounded-lg shadow-lg hover:bg-green-600 transition-transform transform hover:scale-105">
                INICIAR
            </button>
        @else
            <button wire:click="pauseTimer" wire:loading.attr="disabled"
                    class="w-32 px-8 py-4 bg-yellow-500 text-white font-bold rounded-lg shadow-lg hover:bg-yellow-600 transition-transform transform hover:scale-105">
                PAUSAR
            </button>
        @endif

        @if ($this->timerIsRunning || $this->timerIsPaused)
             <button wire:click="stopTimer"
                    class="p-4 text-gray-500 hover:text-red-600" title="Parar e resetar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" /></svg>
            </button>
            <button wire:click="skipTimer"
                    class="p-4 text-gray-500 hover:text-indigo-600" title="Pular para próxima sessão">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
            </button>
        @endif
    </div>

     {{-- Botão para abrir/fechar configurações --}}
    <div class="text-center mt-8">
        <button wire:click="$toggle('showSettings')" class="text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            Configurações
        </button>
    </div>


    {{-- Seção de Configurações --}}
    @if($showSettings)
    <div class="mt-6 p-4 border rounded-lg bg-white transition-all duration-300">
        <h3 class="text-lg font-semibold mb-4 text-center">Configurações</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
            <div>
                <label for="workMinutes" class="block text-sm font-medium text-gray-700">Foco (min)</label>
                <input type="number" id="workMinutes" wire:model.live="workMinutes" class="mt-1 block w-full text-center rounded-md border-gray-300 shadow-sm" :disabled="$wire.get('timerIsRunning')">
            </div>
            <div>
                <label for="shortBreakMinutes" class="block text-sm font-medium text-gray-700">Pausa Curta</label>
                <input type="number" id="shortBreakMinutes" wire:model.live="shortBreakMinutes" class="mt-1 block w-full text-center rounded-md border-gray-300 shadow-sm" :disabled="$wire.get('timerIsRunning')">
            </div>
            <div>
                <label for="longBreakMinutes" class="block text-sm font-medium text-gray-700">Pausa Longa</label>
                <input type="number" id="longBreakMinutes" wire:model.live="longBreakMinutes" class="mt-1 block w-full text-center rounded-md border-gray-300 shadow-sm" :disabled="$wire.get('timerIsRunning')">
            </div>
            <div>
                <label for="pomodorosUntilLongBreak" class="block text-sm font-medium text-gray-700">Ciclos</label>
                <input type="number" id="pomodorosUntilLongBreak" wire:model.live="pomodorosUntilLongBreak" class="mt-1 block w-full text-center rounded-md border-gray-300 shadow-sm" :disabled="$wire.get('timerIsRunning')">
            </div>
        </div>
        <div class="text-center mt-4">
             <button wire:click="saveSettings" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Salvar
            </button>
        </div>
    </div>
    @endif
</div>