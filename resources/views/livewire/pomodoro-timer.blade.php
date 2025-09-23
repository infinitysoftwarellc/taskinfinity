<div class="flex flex-col items-center justify-center p-10 bg-gray-50 rounded-lg shadow-inner">

    <div class="mb-8 flex space-x-2">
        <button wire:click="setTimer('work')"
                class="px-4 py-2 rounded-md text-sm font-medium transition-colors
                       {{ $sessionType == 'work' ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Pomodoro
        </button>
        <button wire:click="setTimer('short_break')"
                class="px-4 py-2 rounded-md text-sm font-medium transition-colors
                       {{ $sessionType == 'short_break' ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Pausa Curta
        </button>
        <button wire:click="setTimer('long_break')"
                class="px-4 py-2 rounded-md text-sm font-medium transition-colors
                       {{ $sessionType == 'long_break' ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Pausa Longa
        </button>
    </div>

    <div class="text-8xl font-mono font-bold text-gray-800 mb-8" wire:poll.1s="decrementTime">
        <span>{{ str_pad($minutes, 2, '0', STR_PAD_LEFT) }}</span>:<span>{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}</span>
    </div>

    <div class="flex space-x-4">
        @if (!$timerRunning)
            <button wire:click="startTimer" class="px-10 py-3 text-lg font-semibold bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600 transition-transform transform hover:scale-105">
                Começar
            </button>
        @else
            <button wire:click="pauseTimer" class="px-10 py-3 text-lg font-semibold bg-yellow-500 text-white rounded-lg shadow-md hover:bg-yellow-600 transition-transform transform hover:scale-105">
                Pausar
            </button>
        @endif
        <button wire:click="resetTimer" class="px-10 py-3 text-lg font-semibold bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 transition-transform transform hover:scale-105">
            Resetar
        </button>
    </div>

</div>