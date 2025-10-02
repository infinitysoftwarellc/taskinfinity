<?php

namespace Tests\Feature\Tasks;

use App\Livewire\Tasks\Details;
use App\Livewire\Tasks\MainPanel;
use App\Models\Mission;
use App\Models\TaskList;
use App\Models\User;
use App\Support\MissionShortcutFilter;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class TasksPageInteractionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_task_inside_shortcut_assigns_expected_due_date(): void
    {
        $user = User::factory()->create([
            'timezone' => 'America/Sao_Paulo',
        ]);

        CarbonImmutable::setTestNow(CarbonImmutable::create(2025, 3, 10, 12, 0, 0, 'America/Sao_Paulo'));

        try {
        $list = TaskList::create([
            'user_id' => $user->id,
            'name' => 'Inbox',
        ]);

        $component = Livewire::actingAs($user)
            ->test(MainPanel::class, ['shortcut' => MissionShortcutFilter::TODAY]);

        $component->set('newTaskTitle', 'Revisar relatório');
        $component->set('newTaskListId', $list->id);
        $component->call('createTask');

        $created = Mission::query()->where('user_id', $user->id)->firstOrFail();

        $expectedDue = CarbonImmutable::now($user->timezone)->startOfDay()
            ->setTimezone(config('app.timezone'));

        $this->assertTrue($created->due_at?->equalTo($expectedDue));
        $this->assertSame($list->id, $created->list_id);
        $component->assertSet('newTaskTitle', '');
        $component->assertSet('selectedMissionIds', [$created->id]);
        } finally {
            CarbonImmutable::setTestNow();
        }
    }

    public function test_creating_mission_after_preserves_due_date_and_order(): void
    {
        $user = User::factory()->create([
            'timezone' => 'America/Sao_Paulo',
        ]);

        $list = TaskList::create([
            'user_id' => $user->id,
            'name' => 'Projetos',
        ]);

        $referenceDue = CarbonImmutable::create(2025, 3, 12, 0, 0, 0, 'America/Sao_Paulo')
            ->setTimezone(config('app.timezone'));

        $reference = Mission::create([
            'user_id' => $user->id,
            'list_id' => $list->id,
            'title' => 'Planejar sprint',
            'status' => 'active',
            'position' => 1,
            'due_at' => $referenceDue,
        ]);

        $component = Livewire::actingAs($user)->test(MainPanel::class);
        $component->call('createMissionAfter', $reference->id);

        $created = Mission::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->firstOrFail();

        $this->assertSame($reference->list_id, $created->list_id);
        $this->assertSame(2, $created->position);
        $this->assertTrue($created->due_at?->equalTo($reference->fresh()->due_at));
        $component->assertSet('selectedMissionIds', [$created->id]);
    }

    public function test_ctrl_or_meta_click_toggles_multi_selection_without_losing_order(): void
    {
        $user = User::factory()->create();

        $list = TaskList::create([
            'user_id' => $user->id,
            'name' => 'Principal',
        ]);

        $missions = collect(['Inbox', 'Hoje', 'Próximos'])->map(fn ($title, $index) => Mission::create([
            'user_id' => $user->id,
            'list_id' => $list->id,
            'title' => $title,
            'status' => 'active',
            'position' => $index + 1,
        ]));

        [$first, $second, $third] = $missions->map->id->all();

        $component = Livewire::actingAs($user)->test(MainPanel::class);

        $component->call('selectMission', $first, 0, 0);
        $component->assertSet('selectedMissionIds', [$first]);

        $component->call('selectMission', $third, 0, 1);
        $component->assertSet('selectedMissionIds', [$first, $third]);

        $component->call('selectMission', $second, 0, 1);
        $component->assertSet('selectedMissionIds', [$first, $second, $third]);

        $component->call('selectMission', $first, 0, 1);
        $component->assertSet('selectedMissionIds', [$second, $third]);
        $component->assertSet('selectedMissionId', $third);

        $component->call('selectMission', $second, 0, 1);
        $component->assertSet('selectedMissionIds', [$third]);
        $component->assertSet('selectedMissionId', $third);
    }

    public function test_collapsed_state_persists_for_same_user_context(): void
    {
        config(['cache.default' => 'array']);
        Cache::flush();

        $user = User::factory()->create();

        $list = TaskList::create([
            'user_id' => $user->id,
            'name' => 'Inbox',
        ]);

        $mission = Mission::create([
            'user_id' => $user->id,
            'list_id' => $list->id,
            'title' => 'Fechar caixa do dia',
            'status' => 'active',
            'position' => 1,
        ]);

        Livewire::actingAs($user)
            ->test(MainPanel::class)
            ->call('toggleMissionCollapse', $mission->id)
            ->assertSet('collapsedMissionIds', [$mission->id]);

        $secondInstance = Livewire::actingAs($user)->test(MainPanel::class);

        $this->assertContains($mission->id, $secondInstance->get('collapsedMissionIds'));
    }

    public function test_details_multi_selection_refreshes_when_tasks_updated_event_is_dispatched(): void
    {
        $user = User::factory()->create();

        $list = TaskList::create([
            'user_id' => $user->id,
            'name' => 'Inbox',
        ]);

        $first = Mission::create([
            'user_id' => $user->id,
            'list_id' => $list->id,
            'title' => 'Ajustar fluxos',
            'status' => 'active',
            'position' => 1,
        ]);

        $second = Mission::create([
            'user_id' => $user->id,
            'list_id' => $list->id,
            'title' => 'Publicar release',
            'status' => 'active',
            'position' => 2,
        ]);

        $component = Livewire::actingAs($user)->test(Details::class);

        $component->call('loadMultiSelection', [$first->id, $second->id]);
        $component->assertSet('multiSelection', [$first->id, $second->id]);

        $initialItems = $component->render()->getData()['multiSelectionItems'] ?? collect();
        $initialItems = $initialItems instanceof \Illuminate\Support\Collection
            ? $initialItems
            : collect($initialItems);

        $this->assertSame('Publicar release', $initialItems->firstWhere('id', $second->id)['title'] ?? null);

        $second->update(['title' => 'Publicar release 1.1']);

        $component->dispatch('tasks-updated');
        $updatedItems = $component->render()->getData()['multiSelectionItems'] ?? collect();
        $updatedItems = $updatedItems instanceof \Illuminate\Support\Collection
            ? $updatedItems
            : collect($updatedItems);

        $this->assertSame('Publicar release 1.1', $updatedItems->firstWhere('id', $second->id)['title'] ?? null);
    }
}
