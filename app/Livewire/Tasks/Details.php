<?php

namespace App\Livewire\Tasks;

use App\Models\Mission;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Details extends Component
{
    public ?int $missionId = null;

    public ?array $mission = null;

    public array $missionTags = [];

    #[On('task-selected')]
    public function loadMission(?int $missionId = null): void
    {
        if (! $missionId) {
            $this->missionId = null;
            $this->mission = null;
            $this->missionTags = [];

            return;
        }

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->with('list')
            ->withCount([
                'checkpoints',
                'checkpoints as checkpoints_done_count' => fn ($query) => $query->where('is_done', true),
                'attachments',
            ])
            ->where('user_id', $user->id)
            ->find($missionId);

        if (! $mission) {
            $this->missionId = null;
            $this->mission = null;
            $this->missionTags = [];

            return;
        }

        $timezone = $user->timezone ?? config('app.timezone');

        $this->missionId = $mission->id;
        $this->mission = [
            'title' => $mission->title,
            'description' => $mission->description,
            'status' => $mission->status,
            'list' => $mission->list?->name,
            'created_at' => $mission->created_at?->copy()->setTimezone($timezone),
            'updated_at' => $mission->updated_at?->copy()->setTimezone($timezone),
            'due_at' => $mission->due_at?->copy()->setTimezone($timezone),
            'priority' => $mission->priority,
            'priority_label' => $this->priorityLabel($mission->priority),
            'is_starred' => (bool) $mission->is_starred,
            'xp_reward' => $mission->xp_reward,
            'checkpoints_total' => $mission->checkpoints_count ?? 0,
            'checkpoints_done' => $mission->checkpoints_done_count ?? 0,
            'attachments_count' => $mission->attachments_count ?? 0,
        ];

        $labels = $mission->labels_json ?? [];
        if (is_array($labels)) {
            $this->missionTags = $labels;
        } else {
            $this->missionTags = [];
        }
    }

    #[On('tasks-updated')]
    public function refreshMission(): void
    {
        if ($this->missionId) {
            $this->loadMission($this->missionId);
        }
    }

    public function render()
    {
        return view('livewire.tasks.details', [
            'mission' => $this->mission,
            'missionTags' => $this->missionTags,
        ]);
    }

    private function priorityLabel(?int $priority): string
    {
        return match ($priority) {
            3 => 'Alta',
            2 => 'Média',
            1 => 'Baixa',
            default => 'Nenhuma',
        };
    }
}
