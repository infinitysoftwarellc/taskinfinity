<div class="bg-white p-6 rounded-lg shadow-lg h-full flex flex-col">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Relatório de Foco</h2>

    {{-- Estatísticas do Dia --}}
    <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="bg-indigo-50 p-4 rounded-lg text-center">
            <span class="text-3xl font-bold text-indigo-600">{{ $this->stats['pomodoros_today'] }}</span>
            <p class="text-sm text-gray-600">Pomodoros Hoje</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg text-center">
            <span class="text-3xl font-bold text-green-600">{{ $this->stats['total_time_today_formatted'] }}</span>
            <p class="text-sm text-gray-600">Tempo de Foco</p>
        </div>
    </div>

    {{-- Histórico de Sessões --}}
    <h3 class="text-lg font-semibold text-gray-700 mb-4">Histórico de Sessões de Foco</h3>
    <div class="flex-grow overflow-y-auto pr-2">
        <ul class="space-y-4">
            @forelse ($this->sessions as $session)
                <li class="border-b pb-3">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-800">
                            {{-- Mostra a data apenas para o primeiro item do dia --}}
                            @if ($loop->first || $session->started_at->format('Y-m-d') !== $this->sessions[$loop->index - 1]->started_at->format('Y-m-d'))
                                <p class="text-sm font-bold text-gray-500 mb-1">{{ $session->started_at->format('d/m/Y') }}</p>
                            @endif
                            Duração: {{ round($session->actual_duration / 60) }} min
                        </span>
                        <span class="text-sm text-gray-500">
                            {{ $session->started_at->format('H:i') }} - {{ $session->stopped_at ? $session->stopped_at->format('H:i') : '' }}
                        </span>
                    </div>
                </li>
            @empty
                <li class="text-center text-gray-500 pt-8">
                    <p>Nenhuma sessão de foco concluída ainda.</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>
