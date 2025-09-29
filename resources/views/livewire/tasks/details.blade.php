<aside class="ti-details">
    @if ($mission)
        <!-- Top bar -->
        <div class="ti-topbar">
            <div class="left">
                <button class="pill" type="button" title="Due Date">
                    <i data-lucide="calendar"></i>
                    <span>
                      @if ($mission['due_at'])
                         {{ $mission['due_at']->format('d/m/Y') }}
                      @else
                         Due Date
                      @endif
                    </span>
                </button>
            </div>
            <div class="right">
                <button class="icon ghost" type="button" title="Flag"><i data-lucide="flag"></i></button>
            </div>
        </div>

        <!-- Title + actions -->
        <div class="ti-header">
            <h2 class="ti-title" title="{{ $mission['title'] }}">{{ $mission['title'] ?: 'No Title' }}</h2>
            <div class="actions">
                <button class="icon ghost" title="Editar"><i data-lucide="pencil"></i></button>
                <button class="icon ghost" title="Mais opções"><i data-lucide="more-horizontal"></i></button>
            </div>
        </div>

        <div class="ti-divider"></div>

        <!-- Subtasks -->
        <section class="ti-subtasks">
            @php
                // $mission['subtasks'] = [
                //   ['id'=>1,'title'=>'s','done'=>false,'children'=>[
                //       ['id'=>2,'title'=>'S','done'=>false,'children'=>[]],
                //       ['id'=>3,'title'=>'No Title','done'=>false,'children'=>[]],
                //   ]],
                // ];
                $subtasks = $mission['subtasks'] ?? [];
            @endphp

            @if (count($subtasks))
                <ul class="ti-list" role="list">
                    @foreach ($subtasks as $st)
                        @include('components.subtask-item', ['item'=>$st, 'depth'=>0])
                    @endforeach
                </ul>
            @else
                <p class="muted" style="margin:8px 0 0;">Sem subtarefas</p>
            @endif

            <button class="add-subtask" type="button"
                {{-- wire:click="openNewSubtask({{ $mission['id'] }})" --}}
            >
                <i data-lucide="plus"></i> Add Subtask
            </button>
        </section>
    @else
        <div class="ti-empty">
            <h3>Selecione uma tarefa</h3>
            <p>Escolha uma tarefa para ver detalhes e subtarefas.</p>
        </div>
    @endif
</aside>
