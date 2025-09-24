<div 
    x-data="{
        remaining: @entangle('timeRemaining').live,
        intervalId: null,

        init() {
            this.startInterval();
            this.$watch('remaining', value => {
                if (value <= 0 && this.intervalId) {
                    clearInterval(this.intervalId);
                    this.intervalId = null;
                }
            });
        },

        startInterval() {
            if (this.intervalId) clearInterval(this.intervalId);
            this.intervalId = setInterval(() => {
                // ✅ Corrigido: agora acessa como propriedade
                if (this.$wire.timerIsRunning && this.remaining > 0) {
                    this.remaining--;
                }
            }, 1000);
        }
    }"
    x-init="init()"
    class="p-6 bg-white rounded-xl shadow-md space-y-6"
>
    <!-- Título -->
    <h2 class="text-2xl font-bold text-gray-800 text-center">
        Pomodoro Timer
    </h2>

    <!-- Tempo Restante -->
    <div class="text-center text-5xl font-mono text-gray-900">
        <span x-text="String(Math.floor(remaining / 60)).padStart(2, '0')"></span>:
        <span x-text="String(remaining % 60).padStart(2, '0')"></span>
    </div>

    <!-- Botões de Controle -->
    <div class="flex justify-center gap-4">
        <!-- ✅ Corrigido: sem parênteses -->
        <button 
            x-show="!$wire.timerIsRunning" 
            wire:click="startTimer" 
            class="px-4 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition"
        >
            Iniciar
        </button>

        <button 
            x-show="$wire.timerIsRunning" 
            wire:click="stopTimer" 
            class="px-4 py-2 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition"
        >
            Parar
        </button>

        <button 
            wire:click="skipTimer" 
            class="px-4 py-2 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition"
        >
            Pular
        </button>
    </div>

    <!-- Seleção de Sessão -->
    <div class="flex justify-center gap-2 mt-4">
        <button 
            wire:click="setSessionType('work')" 
            class="px-3 py-1 rounded-lg shadow 
                   {{ $sessionType === 'work' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            Trabalho
        </button>

        <button 
            wire:click="setSessionType('short_break')" 
            class="px-3 py-1 rounded-lg shadow 
                   {{ $sessionType === 'short_break' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            Pausa Curta
        </button>

        <button 
            wire:click="setSessionType('long_break')" 
            class="px-3 py-1 rounded-lg shadow 
                   {{ $sessionType === 'long_break' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            Pausa Longa
        </button>
    </div>

    <!-- Configurações -->
    <div class="text-center mt-6">
        <button 
            wire:click="$toggle('showSettings')" 
            class="px-4 py-2 bg-gray-300 rounded-lg shadow hover:bg-gray-400 transition"
        >
            Configurações
        </button>
    </div>

    @if ($showSettings)
        <div class="mt-6 space-y-4 border-t pt-4">
            <div class="flex items-center justify-between">
                <label class="text-gray-700">Trabalho (min):</label>
                <input type="number" wire:model="workMinutes" class="w-20 p-1 border rounded-lg">
            </div>

            <div class="flex items-center justify-between">
                <label class="text-gray-700">Pausa Curta (min):</label>
                <input type="number" wire:model="shortBreakMinutes" class="w-20 p-1 border rounded-lg">
            </div>

            <div class="flex items-center justify-between">
                <label class="text-gray-700">Pausa Longa (min):</label>
                <input type="number" wire:model="longBreakMinutes" class="w-20 p-1 border rounded-lg">
            </div>

            <div class="flex items-center justify-between">
                <label class="text-gray-700">Pomodoros até Pausa Longa:</label>
                <input type="number" wire:model="pomodorosUntilLongBreak" class="w-20 p-1 border rounded-lg">
            </div>

            <div class="flex justify-end">
                <button 
                    wire:click="saveSettings" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition"
                >
                    Salvar
                </button>
            </div>
        </div>
    @endif
</div>
