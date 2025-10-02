<?php

namespace App\Http\Livewire\Spotlight;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightSearchResult;

class ProjectSearchCommand extends SpotlightCommand
{
    protected string $name = 'Buscar projetos';

    protected string $description = 'Abrir listas e projetos usando Scout.';

    public function dependencies(): SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(
                SpotlightCommandDependency::make('project')
                    ->setPlaceholder('Qual projeto você procura?')
            );
    }

    public function searchProject(string $query): array
    {
        return $this->projectResults($query)
            ->map(function (Project $project) {
                $name = Str::of((string) $project->name)->trim();

                if ($name->isEmpty()) {
                    $name = 'Sem nome';
                }

                return new SpotlightSearchResult(
                    (string) $project->getKey(),
                    (string) $name,
                    $this->describeProject($project)
                );
            })
            ->all();
    }

    public function execute(Spotlight $spotlight, ?string $project = null): void
    {
        if (! $project || ! $userId = Auth::id()) {
            return;
        }

        $model = Project::query()
            ->where('user_id', $userId)
            ->find($project);

        if (! $model) {
            return;
        }

        $spotlight->redirectRoute('tasks.index', [
            'taskList' => $model->getKey(),
        ]);
    }

    protected function projectResults(string $query): Collection
    {
        $query = trim($query);

        if ($query === '' || ! Auth::check()) {
            return collect();
        }

        return rescue(function () use ($query) {
            /** @var LengthAwarePaginator $paginator */
            $paginator = Project::search($query)
                ->take(7)
                ->paginate(perPage: 7);

            return collect($paginator->items());
        }, function () use ($query) {
            $userId = Auth::id();

            if (! $userId) {
                return collect();
            }

            return Project::query()
                ->where('user_id', $userId)
                ->where(function ($builder) use ($query) {
                    $builder->where('name', 'like', "%{$query}%")
                        ->orWhere('view_type', 'like', "%{$query}%");
                })
                ->orderBy('name')
                ->limit(7)
                ->get();
        }, report: false);
    }

    protected function describeProject(Project $project): string
    {
        $parts = [];

        if ($project->view_type) {
            $parts[] = Str::title(str_replace('_', ' ', $project->view_type));
        }

        if ($project->is_pinned) {
            $parts[] = 'Fixado';
        }

        if ($project->color) {
            $parts[] = $project->color;
        }

        if (empty($parts)) {
            $parts[] = 'Projeto';
        }

        return implode(' • ', $parts);
    }
}
