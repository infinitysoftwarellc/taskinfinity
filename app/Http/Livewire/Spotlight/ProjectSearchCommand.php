<?php

namespace App\Http\Livewire\Spotlight;

use App\Models\Project;
use App\Support\Spotlight\SearchResult;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProjectSearchCommand
{
    public function searchProject(string $query): array
    {
        return $this->projectResults($query)
            ->map(function (Project $project) {
                $name = Str::of((string) $project->name)->trim();

                if ($name->isEmpty()) {
                    $name = 'Sem nome';
                }

                return new SearchResult(
                    (string) $project->getKey(),
                    (string) $name,
                    $this->describeProject($project)
                );
            })
            ->all();
    }

    public function execute(?string $project = null): ?array
    {
        if (! $project || ! $userId = Auth::id()) {
            return null;
        }

        $model = Project::query()
            ->where('user_id', $userId)
            ->find($project);

        if (! $model) {
            return null;
        }

        return [
            'route' => 'tasks.index',
            'parameters' => [
                'taskList' => $model->getKey(),
            ],
        ];
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
