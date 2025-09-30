<?php

namespace App\Livewire\Missions;

use App\Models\Mission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class InlineDescription extends Component
{
    public int $missionId;

    public string $description = '';

    public bool $isEditing = false;

    protected ?string $lastPersistedHash = null;

    protected const MAX_LENGTH = 50000;

    public function mount(int $missionId): void
    {
        $this->missionId = $missionId;

        $mission = Mission::query()->findOrFail($this->missionId);

        $this->description = $mission->description ?? '';
        $this->lastPersistedHash = $this->hashFor($this->description);
    }

    public function render()
    {
        return view('livewire.missions.inline-description');
    }

    public function saveDescription(string $value): void
    {
        $this->dispatch('description-saving', missionId: $this->missionId);

        $normalized = $this->normalizeContent($value);

        validator(
            ['description' => $normalized],
            ['description' => ['nullable', 'string', 'max:' . static::MAX_LENGTH]],
            [],
            ['description' => __('Descrição')]
        )->validate();

        if ($this->hashFor($normalized) === $this->lastPersistedHash) {
            $this->dispatch('description-saved', missionId: $this->missionId, skipped: true);

            return;
        }

        $mission = Mission::query()->find($this->missionId);

        if (! $mission) {
            $this->dispatch('description-error', missionId: $this->missionId);

            return;
        }

        try {
            $mission->description = $normalized;
            $mission->save();

            $this->description = $normalized;
            $this->lastPersistedHash = $this->hashFor($normalized);

            $this->dispatch('description-saved', missionId: $this->missionId, skipped: false, savedAt: now()->toIso8601String());
            $this->dispatch('tasks-updated');
        } catch (\Throwable $exception) {
            Log::error('Failed to save mission description.', [
                'mission_id' => $this->missionId,
                'exception' => $exception,
            ]);

            $this->dispatch('description-error', missionId: $this->missionId);
        }
    }

    public function startEditing(): void
    {
        $this->isEditing = true;
    }

    public function stopEditing(): void
    {
        $this->isEditing = false;
    }

    protected function normalizeContent(string $raw): string
    {
        $clean = Str::of($raw)
            ->replace(["\r\n", "\r"], "\n")
            ->replace("\u{00A0}", ' ');

        $cleanString = (string) $clean;
        $cleanString = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $cleanString) ?? '';

        return trim($cleanString);
    }

    protected function hashFor(?string $value): string
    {
        return hash('sha256', (string) $value);
    }
}
