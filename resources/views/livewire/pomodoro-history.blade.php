<div class="bg-white p-6 rounded-lg shadow-lg h-full flex flex-col">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Relatório de Foco</h2>

    {{-- Filtro de Data --}}
    <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mb-8">
        <div>
            <label for="startDate" class="block text-sm font-medium text-gray-700">De:</label>
            <input type="date" id="startDate" wire:model.live="startDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div>
            <label for="endDate" class="block text-sm font-medium text-gray-700">Até:</label>
            <input type="date" id="endDate" wire:model.live="endDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
    </div>

    {{-- Estatísticas do Período --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-indigo-50 p-4 rounded-lg text-center">
            <span class="text-3xl font-bold text-indigo-600">{{ $this->stats['total_work_time'] }}</span>
            <p class="text-sm text-gray-600">Tempo de Foco</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg text-center">
            <span class="text-3xl font-bold text-green-600">{{ $this->stats['total_short_break_time'] }}</span>
            <p class="text-sm text-gray-600">Pausas Curtas</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg text-center md:col-span-1 col-span-2">
            <span class="text-3xl font-bold text-yellow-600">{{ $this->stats['total_long_break_time'] }}</span>
            <p class="text-sm text-gray-600">Pausas Longas</p>
        </div>
    </div>


    {{-- Histórico de Sessões --}}
    <h3 class="text-lg font-semibold text-gray-700 mb-4">Histórico de Sessões</h3>
    <div class="flex-grow overflow-y-auto pr-2" style="max-height: 400px;">
        <ul class="space-y-4">
            @forelse ($this->sessions as $session)
                <li class="border-b pb-3">
                    <div class="flex justify-between items-center">
                        <div>
                            @if ($loop->first || $session->started_at->format('Y-m-d') !== $this->sessions[$loop->index - 1]->started_at->format('Y-m-d'))
                                <p class="text-sm font-bold text-gray-500 mb-1">{{ $session->started_at->translatedFormat('d \d\e F') }}</p>
                            @endif
                            <span class="font-semibold text-gray-800">
                               @if ($session->session_type === 'work')
                                  <span class="text-indigo-600">Foco:</span>
                               @elseif ($session->session_type === 'short_break')
                                  <span class="text-green-600">Pausa Curta:</span>
                               @else
                                   <span class="text-yellow-600">Pausa Longa:</span>
                               @endif
                                {{ round($session->actual_duration / 60) }} min
                            </span>
                        </div>
                        <span class="text-sm text-gray-500">
                            {{ $session->started_at->format('H:i') }} - {{ $session->stopped_at ? $session->stopped_at->format('H:i') : '' }}
                        </span>
                    </div>
                </li>
            @empty
                <li class="text-center text-gray-500 pt-8">
                    <p>Nenhuma sessão encontrada no período selecionado.</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>