<?php

namespace App\Http\Livewire\Spotlight;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightSearchResult;

class TaskSearchCommand extends SpotlightCommand
{
    protected string $name = 'Buscar tarefas';

    protected string $description = 'Pesquisar e abrir tarefas rapidamente.';

    public function dependencies(): SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(
                SpotlightCommandDependency::make('task')
                    ->setPlaceholder('Digite o nome da tarefa')
            );
    }

    public function searchTask(string $query): array
    {
        return $this->taskResults($query)
            ->map(function (Task $task) {
                $title = Str::of((string) $task->title)->trim();

                if ($title->isEmpty()) {
                    $title = 'Sem título';
                }

                return new SpotlightSearchResult(
                    (string) $task->getKey(),
                    (string) $title,
                    $this->describeTask($task)
                );
            })
            ->all();
    }

    public function execute(Spotlight $spotlight, ?string $task = null): void
    {
        if (! $task || ! $userId = Auth::id()) {
            return;
        }

        $model = Task::query()
            ->where('user_id', $userId)
            ->find($task);

        if (! $model) {
            return;
        }

        $params = ['mission' => $model->getKey()];

        if ($model->list_id) {
            $params['taskList'] = $model->list_id;
        }

        $spotlight->redirectRoute('tasks.index', $params);
    }

    protected function taskResults(string $query): Collection
    {
        $query = trim($query);

        if ($query === '' || ! Auth::check()) {
            return collect();
        }

        return rescue(function () use ($query) {
            /** @var LengthAwarePaginator $paginator */
            $paginator = Task::search($query)
                ->take(7)
                ->paginate(perPage: 7);

            return collect($paginator->items());
        }, function () use ($query) {
            $userId = Auth::id();

            if (! $userId) {
                return collect();
            }

            return Task::query()
                ->with('list')
                ->where('user_id', $userId)
                ->where(function ($builder) use ($query) {
                    $builder->where('title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->orderByDesc('updated_at')
                ->limit(7)
                ->get();
        }, report: false);
    }

    protected function describeTask(Task $task): string
    {
        $parts = [];

        if ($task->list) {
            $parts[] = $task->list->name;
        }

        if ($task->due_at) {
            $parts[] = $task->due_at->diffForHumans();
        }

        if (empty($parts)) {
            $parts[] = 'Tarefa';
        }

        return implode(' • ', $parts);
    }
}
