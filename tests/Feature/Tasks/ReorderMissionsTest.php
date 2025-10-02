<?php

declare(strict_types=1);

use App\Livewire\Tasks\MainPanel;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

test('drag and drop reordering persists mission positions', function () {
    Config::set('scout.driver', 'database');
    Config::set('scout.queue', false);
    Config::set('scout.after_commit', false);

    $user = User::factory()->create();
    $list = TaskList::create([
        'user_id' => $user->id,
        'name' => 'Inbox',
        'position' => 1,
    ]);

    $first = Task::create([
        'user_id' => $user->id,
        'list_id' => $list->id,
        'title' => 'Primeira tarefa',
        'status' => 'active',
        'position' => 1,
    ]);

    $second = Task::create([
        'user_id' => $user->id,
        'list_id' => $list->id,
        'title' => 'Segunda tarefa',
        'status' => 'active',
        'position' => 2,
    ]);

    $this->actingAs($user);

    Livewire::test(MainPanel::class, ['currentListId' => $list->id])
        ->call('reorderMissions', [
            ['id' => $second->id, 'list_id' => $list->id],
            ['id' => $first->id, 'list_id' => $list->id],
        ]);

    expect($second->refresh()->position)->toBe(1)
        ->and($first->refresh()->position)->toBe(2);
});
