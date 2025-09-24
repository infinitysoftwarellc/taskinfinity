{{-- resources/views/livewire/pomodoro-history.blade.php --}}
<div>
    <div class="p-6 bg-white rounded-xl shadow-md space-y-6">
        <h2 class="text-2xl font-bold text-gray-800 text-center">
            Histórico de Sessões
        </h2>

        <ul class="space-y-4">
            @forelse($sessions ?? [] as $session)
                <li class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold capitalize">
                                {{ str_replace('_', ' ', $session->session_type ?? 'work') }}
                            </h3>
                            <span class="text-sm text-gray-600">
                                Status: {{ ucfirst($session->status ?? 'unknown') }}
                                @if(isset($session->actual_duration) && $session->actual_duration > 0)
                                    - {{ round($session->actual_duration / 60) }} min
                                @endif
                            </span>
                        </div>
                        <span class="text-sm text-gray-500">
                            {{-- Verifica se started_at é string ou objeto Carbon --}}
                            @if($session->started_at)
                                @if(is_string($session->started_at))
                                    {{ date('H:i', strtotime($session->started_at)) }}
                                @else
                                    {{ $session->started_at->format('H:i') }}
                                @endif
                            @endif
                            
                            @if(isset($session->stopped_at) && $session->stopped_at)
                                - 
                                @if(is_string($session->stopped_at))
                                    {{ date('H:i', strtotime($session->stopped_at)) }}
                                @else
                                    {{ $session->stopped_at->format('H:i') }}
                                @endif
                            @elseif(isset($session->ended_at) && $session->ended_at)
                                - 
                                @if(is_string($session->ended_at))
                                    {{ date('H:i', strtotime($session->ended_at)) }}
                                @else
                                    {{ $session->ended_at->format('H:i') }}
                                @endif
                            @endif
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