<?php

use App\Livewire\Task\Item;
use App\Livewire\Task\Workspace;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('permite criar tarefas raiz rapidamente com Enter', function () {
    $user = User::factory()->create();

    $list = TaskList::create([
        'user_id' => $user->id,
        'name' => 'Lista principal',
        'view_mode' => 'list',
        'position' => 1,
    ]);

    $this->actingAs($user);

    Livewire::test(Workspace::class, ['listId' => $list->id])
        ->set('newTaskTitle', 'Planejar lançamento')
        ->call('createRootTask')
        ->assertSet('newTaskTitle', '')
        ->assertHasNoErrors();

    $task = Task::where('list_id', $list->id)->first();

    expect($task)
        ->not()->toBeNull()
        ->and($task->title)->toBe('Planejar lançamento')
        ->and($task->depth)->toBe(0)
        ->and($task->parent_id)->toBeNull();
});

it('impede criar subtarefas além do limite de sete níveis', function () {
    $user = User::factory()->create();

    $list = TaskList::create([
        'user_id' => $user->id,
        'name' => 'Lista principal',
        'view_mode' => 'list',
        'position' => 1,
    ]);

    $task = Task::create([
        'user_id' => $user->id,
        'list_id' => $list->id,
        'parent_id' => null,
        'depth' => Task::MAX_DEPTH,
        'title' => 'Deep task',
        'status' => 'todo',
        'priority' => 'none',
        'position' => 1,
    ]);

    $this->actingAs($user);

    Livewire::test(Item::class, ['task' => $task])
        ->set('subtaskTitle', 'Outra subtarefa')
        ->call('createSubtask')
        ->assertHasErrors(['subtaskTitle']);
});
