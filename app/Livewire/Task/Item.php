<?php

namespace App\Livewire\Task;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Item extends Component
{
    public Task $task;

    public int $depth = 0;

    public string $title = '';

    public ?string $description = null;

    public string $status = 'todo';

    public string $priority = 'none';

    public ?string $dueAt = null;

    public ?int $estimatePomodoros = null;

    public ?int $pomodorosDone = null;

    public bool $showSubtaskForm = false;

    public string $subtaskTitle = '';

    public array $statusOptions = [
        ['value' => 'todo', 'label' => 'A fazer'],
        ['value' => 'doing', 'label' => 'Em progresso'],
        ['value' => 'done', 'label' => 'Concluída'],
        ['value' => 'archived', 'label' => 'Arquivada'],
    ];

    public array $priorityOptions = [
        ['value' => 'none', 'label' => 'Sem prioridade'],
        ['value' => 'low', 'label' => 'Baixa'],
        ['value' => 'med', 'label' => 'Média'],
        ['value' => 'high', 'label' => 'Alta'],
    ];

    protected $listeners = [
        'task-updated' => 'refreshTask',
    ];

    public function mount(Task $task, int $depth = 0): void
    {
        abort_unless($task->user_id === Auth::id(), 403);

        $this->task = $task->load(['childrenRecursive' => function ($query) {
            $query->orderBy('position')->orderBy('created_at');
        }]);

        $this->depth = $depth > 0 ? $depth : ($task->depth ?? 0);
        $this->fillFromTask();
    }

    public function updatedTitle(): void
    {
        $this->saveTitle();
    }

    public function updatedStatus(): void
    {
        $this->saveStatus();
    }

    public function updatedPriority(): void
    {
        $this->savePriority();
    }

    public function updatedDueAt(): void
    {
        $this->saveDueAt();
    }

    public function updatedEstimatePomodoros(): void
    {
        $this->saveEstimatePomodoros();
    }

    public function updatedPomodorosDone(): void
    {
        $this->savePomodorosDone();
    }

    public function saveTitle(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $this->task->update([
            'title' => trim($validated['title']),
        ]);

        $this->refreshTask();
    }

    public function saveDescription(): void
    {
        $validated = $this->validate([
            'description' => ['nullable', 'string'],
        ]);

        $this->task->update([
            'description' => $validated['description'] ?? null,
        ]);

        $this->refreshTask();
    }

    public function saveStatus(): void
    {
        $validated = $this->validate([
            'status' => ['required', Rule::in(array_column($this->statusOptions, 'value'))],
        ]);

        $payload = ['status' => $validated['status']];

        if ($payload['status'] === 'done') {
            $payload['completed_at'] = $this->task->completed_at ?? now();
        } else {
            $payload['completed_at'] = null;
        }

        $this->task->update($payload);

        $this->refreshTask();
        $this->dispatch('task-updated');
    }

    public function savePriority(): void
    {
        $validated = $this->validate([
            'priority' => ['required', Rule::in(array_column($this->priorityOptions, 'value'))],
        ]);

        $this->task->update([
            'priority' => $validated['priority'],
        ]);

        $this->refreshTask();
    }

    public function saveDueAt(): void
    {
        $validated = $this->validate([
            'dueAt' => ['nullable', 'date'],
        ]);

        $timezone = config('app.timezone') ?? 'UTC';

        $this->task->update([
            'due_at' => $validated['dueAt']
                ? Carbon::parse($validated['dueAt'], $timezone)
                : null,
        ]);

        $this->refreshTask();
    }

    public function saveEstimatePomodoros(): void
    {
        $validated = $this->validate([
            'estimatePomodoros' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]);

        $this->task->update([
            'estimate_pomodoros' => $validated['estimatePomodoros'] ?? 0,
        ]);

        $this->refreshTask();
    }

    public function savePomodorosDone(): void
    {
        $validated = $this->validate([
            'pomodorosDone' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]);

        $this->task->update([
            'pomodoros_done' => $validated['pomodorosDone'] ?? 0,
        ]);

        $this->refreshTask();
    }

    public function toggleSubtaskForm(): void
    {
        if ($this->task->depth >= Task::MAX_DEPTH) {
            $this->addError('subtaskTitle', 'Limite de 7 níveis de subtarefas atingido.');

            return;
        }

        $this->showSubtaskForm = ! $this->showSubtaskForm;

        if (! $this->showSubtaskForm) {
            $this->subtaskTitle = '';
            $this->resetErrorBag('subtaskTitle');
        }
    }

    public function createSubtask(): void
    {
        if ($this->task->depth >= Task::MAX_DEPTH) {
            $this->addError('subtaskTitle', 'Limite de 7 níveis de subtarefas atingido.');

            return;
        }

        $validated = $this->validate([
            'subtaskTitle' => ['required', 'string', 'max:255'],
        ]);

        $title = trim($validated['subtaskTitle']);

        if ($title === '') {
            $this->addError('subtaskTitle', 'Informe um nome para a subtarefa.');

            return;
        }

        $position = Task::query()
            ->where('parent_id', $this->task->id)
            ->max('position');

        Task::create([
            'user_id' => $this->task->user_id,
            'list_id' => $this->task->list_id,
            'parent_id' => $this->task->id,
            'depth' => $this->task->depth + 1,
            'title' => $title,
            'status' => 'todo',
            'priority' => $this->task->priority,
            'position' => (int) $position + 1,
        ]);

        $this->subtaskTitle = '';
        $this->showSubtaskForm = false;
        $this->resetErrorBag('subtaskTitle');

        $this->refreshTask();
        $this->dispatch('task-updated');
    }

    public function quickSubtask(): void
    {
        if ($this->task->depth >= Task::MAX_DEPTH) {
            $this->addError('subtaskTitle', 'Limite de 7 níveis de subtarefas atingido.');

            return;
        }

        $defaultTitle = 'Nova subtarefa';

        if ($this->task->childrenRecursive->isNotEmpty()) {
            $count = $this->task->childrenRecursive->count() + 1;
            $defaultTitle = 'Nova subtarefa ' . $count;
        }

        $this->subtaskTitle = $defaultTitle;
        $this->createSubtask();
    }

    public function deleteTask(): void
    {
        $this->task->delete();
        $this->dispatch('task-updated');
    }

    public function openEditor(): void
    {
        $this->dispatch('open-task-editor', taskId: $this->task->id);
    }

    protected function fillFromTask(): void
    {
        $this->title = $this->task->title;
        $this->description = $this->task->description;
        $this->status = $this->task->status;
        $this->priority = $this->task->priority;
        $this->dueAt = $this->task->due_at?->format('Y-m-d\TH:i');
        $this->estimatePomodoros = $this->task->estimate_pomodoros;
        $this->pomodorosDone = $this->task->pomodoros_done;
    }

    public function refreshTask(): void
    {
        $this->task->refresh()->load(['childrenRecursive' => function ($query) {
            $query->orderBy('position')->orderBy('created_at');
        }]);

        $this->fillFromTask();
    }

    public function render()
    {
        return view('livewire.task.item');
    }
}
