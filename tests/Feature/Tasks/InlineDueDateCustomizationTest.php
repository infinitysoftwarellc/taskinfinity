<?php

namespace Tests\Feature\Tasks;

use App\Livewire\Tasks\MainPanel;
use App\Models\Mission;
use App\Models\TaskList;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InlineDueDateCustomizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_set_and_clear_custom_due_date_from_inline_menu(): void
    {
        $user = User::factory()->create([
            'timezone' => 'America/Sao_Paulo',
        ]);

        $list = TaskList::create([
            'user_id' => $user->id,
            'name' => 'Inbox',
        ]);

        $mission = Mission::create([
            'user_id' => $user->id,
            'list_id' => $list->id,
            'title' => 'Missão de teste',
            'status' => 'active',
            'position' => 1,
        ]);

        $component = Livewire::actingAs($user)->test(MainPanel::class);

        $component->call('runInlineAction', $mission->id, 'set-date', '2025-03-15');

        $expectedCustom = CarbonImmutable::createFromFormat('Y-m-d', '2025-03-15', 'America/Sao_Paulo')
            ->setTimezone(config('app.timezone'));

        $this->assertTrue($mission->fresh()->due_at?->equalTo($expectedCustom));

        CarbonImmutable::setTestNow(CarbonImmutable::create(2025, 3, 10, 12, 0, 0, 'America/Sao_Paulo'));

        try {
            $component->call('runInlineAction', $mission->id, 'due-shortcut', 'today');

            $expectedToday = CarbonImmutable::now('America/Sao_Paulo')->startOfDay()
                ->setTimezone(config('app.timezone'));

            $this->assertTrue($mission->fresh()->due_at?->equalTo($expectedToday));

            $component->call('runInlineAction', $mission->id, 'due-shortcut', 'clear');

            $this->assertNull($mission->fresh()->due_at);
        } finally {
            CarbonImmutable::setTestNow();
        }
    }
}
