<aside class="details panel">
    @if ($mission)
        <div class="details-header">
            <div>
                <span class="details-status {{ $mission['status'] }}">{{ ucfirst($mission['status']) }}</span>
                @if ($mission['list'])
                    <span class="details-list">{{ $mission['list'] }}</span>
                @else
                    <span class="details-list muted">Sem lista</span>
                @endif
            </div>
            <div class="right">
                <button class="icon-btn" title="Editar">
                    <i data-lucide="pencil"></i>
                </button>
                <button class="icon-btn" title="Mais opções">
                    <i data-lucide="more-horizontal"></i>
                </button>
            </div>
        </div>

        <div class="details-body">
            <h2 class="details-title">{{ $mission['title'] }}</h2>

            @if (!empty($missionTags))
                <div class="details-tags">
                    @foreach ($missionTags as $tag)
                        <span class="details-tag">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif

            @if (!empty($mission['description']))
                <p class="details-description">{{ $mission['description'] }}</p>
            @else
                <p class="details-description muted">Sem descrição adicionada.</p>
            @endif

            <div class="details-meta">
                <div>
                    <strong>Criada</strong>
                    <span>{{ optional($mission['created_at'])->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <strong>Atualizada</strong>
                    <span>{{ optional($mission['updated_at'])->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <strong>Prioridade</strong>
                    <span>{{ $mission['priority'] ?? '-' }}</span>
                </div>
            </div>
        </div>
    @else
        <div class="details-empty">
            <h3>Selecione uma tarefa</h3>
            <p>Escolha uma tarefa no painel principal para visualizar detalhes, notas e etiquetas.</p>
        </div>
    @endif
</aside>
