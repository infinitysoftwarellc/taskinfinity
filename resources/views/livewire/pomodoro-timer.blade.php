<div 
    x-data="{
        remaining: @entangle('timeRemaining').live,
        intervalId: null,
        syncIntervalId: null,

        init() {
            this.startTimer();
            this.startSync();
        },

        startTimer() {
            if (this.intervalId) clearInterval(this.intervalId);
            this.intervalId = setInterval(() => {
                if ($wire.timerIsRunning && this.remaining > 0) {
                    this.remaining--;
                } else if (this.remaining <= 0 && $wire.timerIsRunning) {
                    $wire.syncWithDatabase(); // Verifica se completou
                }
            }, 1000);
        },

        startSync() {
            // Sincroniza com banco a cada 5 segundos
            if (this.syncIntervalId) clearInterval(this.syncIntervalId);
            this.syncIntervalId = setInterval(() => {
                $wire.syncWithDatabase();
            }, 5000);
        }
    }"
    x-init="init()"
    class="p-6 bg-white rounded-xl shadow-md space-y-6 max-w-md mx-auto"
>
    <!-- Título -->
    <h2 class="text-2xl font-bold text-gray-800 text-center">
        Pomodoro Timer
    </h2>

    <!-- Info da Sessão Atual -->
    @if($currentSession)
        <div class="text-center text-sm text-gray-600 bg-gray-50 p-2 rounded">
            Sessão #{{ $currentSession->id }} - {{ ucfirst($currentSession->status) }}
        </div>
    @endif

    <!-- Timer Display -->
    <div class="text-center">
        <div class="text-5xl font-mono text-gray-900 mb-2">
            <span x-text="String(Math.floor(remaining / 60)).padStart(2, '0')"></span>:
            <span x-text="String(remaining % 60).padStart(2, '0')"></span>
        </div>
        <div class="text-lg text-gray-600 capitalize">
            {{ str_replace('_', ' ', $sessionType) }}
        </div>
    </div>

    <!-- Controles -->
    <div class="flex justify-center gap-3">
        @if(!$timerIsRunning)
            <button wire:click="startTimer" 
                class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                {{ $currentSession && $currentSession->status === 'paused' ? 'Continuar' : 'Iniciar' }}
            </button>
        @else
            <button wire:click="pauseTimer" 
                class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                Pausar
            </button>
        @endif

        <button wire:click="skipTimer" 
            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
            Pular
        </button>

        @if($currentSession && !$timerIsRunning)
            <button wire:click="openSaveDialog" 
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                Salvar
            </button>
        @endif
    </div>

    <!-- Dialog de Salvar -->
    @if($showSaveDialog)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
             x-on:click.self="$wire.closeSaveDialog()">
            <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4">
                <h3 class="text-lg font-bold mb-4">Salvar Sessão</h3>
                
                <textarea wire:model="notes" placeholder="Anotações (opcional)" 
                    class="w-full p-2 border rounded mb-4" rows="3"></textarea>

                <div class="flex gap-3 justify-end">
                    <button wire:click="closeSaveDialog" 
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button wire:click="saveCurrentSession" 
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Salvar
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Seleção de Tipo -->
    <div class="flex justify-center gap-2">
        <button wire:click="setSessionType('work')" 
            class="px-3 py-1 rounded text-sm {{ $sessionType === 'work' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
            Trabalho
        </button>
        <button wire:click="setSessionType('short_break')" 
            class="px-3 py-1 rounded text-sm {{ $sessionType === 'short_break' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
            Pausa Curta
        </button>
        <button wire:click="setSessionType('long_break')" 
            class="px-3 py-1 rounded text-sm {{ $sessionType === 'long_break' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
            Pausa Longa
        </button>
    </div>

    <!-- Stats -->
    <div class="text-center bg-gray-50 p-3 rounded">
        <div class="text-2xl font-bold text-blue-600">{{ $completedPomodoros }}</div>
        <div class="text-sm text-gray-600">Pomodoros Hoje</div>
    </div>

    <!-- Configurações -->
    <div class="text-center">
        <button wire:click="$toggle('showSettings')" 
            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
            Configurações
        </button>
    </div>

    @if($showSettings)
        <div class="space-y-3 border-t pt-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm">Trabalho (min):</label>
                    <input type="number" wire:model="workMinutes" class="w-full p-1 border rounded">
                </div>
                <div>
                    <label class="text-sm">Pausa Curta:</label>
                    <input type="number" wire:model="shortBreakMinutes" class="w-full p-1 border rounded">
                </div>
                <div>
                    <label class="text-sm">Pausa Longa:</label>
                    <input type="number" wire:model="longBreakMinutes" class="w-full p-1 border rounded">
                </div>
                <div>
                    <label class="text-sm">Pomodoros até Pausa Longa:</label>
                    <input type="number" wire:model="pomodorosUntilLongBreak" class="w-full p-1 border rounded">
                </div>
            </div>
            <button wire:click="saveSettings" 
                class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Salvar
            </button>
        </div>
    @endif
</div>