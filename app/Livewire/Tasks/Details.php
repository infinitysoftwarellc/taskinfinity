<?php

namespace App\Livewire\Tasks;

use App\Models\Mission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Details extends Component
{
    protected $listeners = [
        'task-selected' => 'loadMission',
        'tasks-updated' => 'refreshMission',
    ];

    public ?int $missionId = null;

    public ?array $mission = null;

    public array $missionTags = [];

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
            ->where('user_id', $user->id)
            ->find($missionId);

        if (! $mission) {
            $this->missionId = null;
            $this->mission = null;
            $this->missionTags = [];

            return;
        }

        $this->missionId = $mission->id;
        $this->mission = [
            'title' => $mission->title,
            'description' => $mission->description,
            'status' => $mission->status,
            'list' => $mission->list?->name,
            'created_at' => $mission->created_at,
            'updated_at' => $mission->updated_at,
            'priority' => $mission->priority,
        ];

        $labels = $mission->labels_json ?? [];
        if (is_array($labels)) {
            $this->missionTags = $labels;
        } else {
            $this->missionTags = [];
        }
    }

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
}
