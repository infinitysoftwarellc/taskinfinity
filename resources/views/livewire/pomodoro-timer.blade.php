<div class="p-4 sm:p-10 bg-gray-50 rounded-lg shadow-inner"
     x-data="pomodoroTimer()"
     x-on:state-loaded.window="handleStateUpdate($event.detail.state)">

    {{-- Seção de Configurações --}}
    <div class="mb-8 p-4 border rounded-lg bg-white">
        <h3 class="text-lg font-semibold mb-4 text-center">Configurações</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
            <div>
                <label for="workMinutes" class="block text-sm font-medium text-gray-700">Foco (min)</label>
                <input type="number" id="workMinutes" wire:model.live="workMinutes" class="mt-1 block w-full text-center rounded-md border-gray-300 shadow-sm" :disabled="state.timerRunning">
            </div>
            <div>
                <label for="shortBreakMinutes" class="block text-sm font-medium text-gray-700">Pausa Curta</label>
                <input type="number" id="shortBreakMinutes" wire:model.live="shortBreakMinutes" class="mt-1 block w-full text-center rounded-md border-gray-300 shadow-sm" :disabled="state.timerRunning">
            </div>
            <div>
                <label for="longBreakMinutes" class="block text-sm font-medium text-gray-700">Pausa Longa</label>
                <input type="number" id="longBreakMinutes" wire:model.live="longBreakMinutes" class="mt-1 block w-full text-center rounded-md border-gray-300 shadow-sm" :disabled="state.timerRunning">
            </div>
            <div>
                <label for="cyclesUntilLongBreak" class="block text-sm font-medium text-gray-700">Ciclos</label>
                <input type="number" id="cyclesUntilLongBreak" wire:model.live="cyclesUntilLongBreak" class="mt-1 block w-full text-center rounded-md border-gray-300 shadow-sm" :disabled="state.timerRunning">
            </div>
        </div>
    </div>

    {{-- Seção do Timer --}}
    <div class="flex flex-col items-center justify-center">
        <div class="mb-6 flex space-x-2">
            <button wire:click="setTimer('work')" :class="{ 'bg-indigo-600 text-white shadow-md': state.sessionType === 'work', 'bg-gray-200 text-gray-700 hover:bg-gray-300': state.sessionType !== 'work' }" class="px-4 py-2 rounded-md text-sm font-medium transition-colors" :disabled="state.timerRunning">
                Foco
            </button>
            <button wire:click="setTimer('short_break')" :class="{ 'bg-indigo-600 text-white shadow-md': state.sessionType === 'short_break', 'bg-gray-200 text-gray-700 hover:bg-gray-300': state.sessionType !== 'short_break' }" class="px-4 py-2 rounded-md text-sm font-medium transition-colors" :disabled="state.timerRunning">
                Pausa Curta
            </button>
            <button wire:click="setTimer('long_break')" :class="{ 'bg-indigo-600 text-white shadow-md': state.sessionType === 'long_break', 'bg-gray-200 text-gray-700 hover:bg-gray-300': state.sessionType !== 'long_break' }" class="px-4 py-2 rounded-md text-sm font-medium transition-colors" :disabled="state.timerRunning">
                Pausa Longa
            </button>
        </div>

        <div class="text-8xl font-mono font-bold text-gray-800 mb-4">
            <span x-text="display.minutes">--</span>:<span x-text="display.seconds">--</span>
        </div>
        
        <div class="mb-8 text-gray-500 font-semibold">
            {{-- Lógica de ciclo pode ser adicionada depois --}}
        </div>

        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
            {{-- Botão START / RESUME --}}
            <button x-show="!state.timerRunning || state.isPaused" wire:click="startTimer" class="px-10 py-3 text-lg font-semibold bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600 transition-transform transform hover:scale-105">
                <span x-show="!state.isPaused">Começar</span>
                <span x-show="state.isPaused">Retomar</span>
            </button>
            
            {{-- Botão PAUSE --}}
            <button x-show="state.timerRunning && !state.isPaused" wire:click="pauseTimer" class="px-10 py-3 text-lg font-semibold bg-yellow-500 text-white rounded-lg shadow-md hover:bg-yellow-600 transition-transform transform hover:scale-105">
                Pausar
            </button>

            {{-- Botão STOP --}}
            <button x-show="state.timerRunning" wire:click="stopTimer" class="px-10 py-3 text-lg font-semibold bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 transition-transform transform hover:scale-105">
                Parar
            </button>
        </div>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pomodoroTimer', () => ({
            state: {
                remainingSeconds: 0,
                timerRunning: false,
                isPaused: false,
                sessionType: 'work'
            },
            display: { minutes: '00', seconds: '00' },
            interval: null,

            handleStateUpdate(newState) {
                this.stopCountdown(); // Para o contador antigo
                this.state = newState;
                this.updateDisplay(this.state.remainingSeconds);

                // Se o timer estiver rodando e não pausado, inicia o contador visual
                if (this.state.timerRunning && !this.state.isPaused) {
                    this.startCountdown();
                }
            },

            startCountdown() {
                if (this.interval) clearInterval(this.interval);
                
                this.interval = setInterval(() => {
                    if (this.state.remainingSeconds > 0) {
                        this.state.remainingSeconds--;
                        this.updateDisplay(this.state.remainingSeconds);
                    } else {
                        this.stopCountdown();
                        // Quando o tempo acabar, pede para o servidor carregar o próximo estado
                        // (ex: ir de 'work' para 'short_break')
                        @this.call('loadStateFromServer');
                    }
                }, 1000);
            },

            stopCountdown() {
                clearInterval(this.interval);
                this.interval = null;
            },

            updateDisplay(seconds) {
                if (seconds < 0) return;
                this.display.minutes = Math.floor(seconds / 60).toString().padStart(2, '0');
                this.display.seconds = (seconds % 60).toString().padStart(2, '0');
            }
        }));
    });
    </script>
</div>