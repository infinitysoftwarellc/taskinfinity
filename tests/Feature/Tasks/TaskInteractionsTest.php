<?php

declare(strict_types=1);

use App\Livewire\Tasks\MainPanel;
use App\Livewire\Tasks\Modals\SubtaskEditor;
use App\Livewire\Tasks\Sidebar;
use App\Models\Mission;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

test('user can create a list from the sidebar modal', function () {
    Config::set('scout.driver', 'database');
    Config::set('scout.queue', false);
    Config::set('scout.after_commit', false);

    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Sidebar::class)
        ->call('openCreateModal', 'list')
        ->set('newListName', 'Planejamento')
        ->call('saveList');

    $this->assertDatabaseHas('lists', [
        'user_id' => $user->id,
        'name' => 'Planejamento',
    ]);
});

test('user can create a tag from the sidebar', function () {
    Config::set('scout.driver', 'database');
    Config::set('scout.queue', false);
    Config::set('scout.after_commit', false);

    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Sidebar::class)
        ->call('openTagModal')
        ->set('newTagName', 'Urgente')
        ->call('createTag');

    $this->assertDatabaseHas('tags', [
        'user_id' => $user->id,
        'name' => 'Urgente',
    ]);
});

test('user can add a task inside a list', function () {
    Config::set('scout.driver', 'database');
    Config::set('scout.queue', false);
    Config::set('scout.after_commit', false);

    $user = User::factory()->create();
    $list = TaskList::create([
        'user_id' => $user->id,
        'name' => 'Inbox',
        'position' => 1,
    ]);

    $this->actingAs($user);

    Livewire::test(MainPanel::class, ['currentListId' => $list->id])
        ->set('newTaskTitle', 'Comprar café')
        ->call('createTask');

    $this->assertDatabaseHas('missions', [
        'user_id' => $user->id,
        'list_id' => $list->id,
        'title' => 'Comprar café',
    ]);
});

test('subtask editor saves checkpoints and closes the modal', function () {
    Config::set('scout.driver', 'database');
    Config::set('scout.queue', false);
    Config::set('scout.after_commit', false);

    $user = User::factory()->create();
    $list = TaskList::create([
        'user_id' => $user->id,
        'name' => 'Trabalho',
        'position' => 1,
    ]);

    $mission = Mission::create([
        'user_id' => $user->id,
        'list_id' => $list->id,
        'title' => 'Preparar apresentação',
        'status' => 'active',
        'position' => 1,
    ]);

    $this->actingAs($user);

    Livewire::test(SubtaskEditor::class, [
        'missionId' => $mission->id,
        'parentId' => null,
        'modalKey' => 'modal-key-test',
    ])
        ->set('title', 'Definir roteiro')
        ->call('save')
        ->assertSet('open', false)
        ->assertDispatched('closeModal', 'modal-key-test');

    $this->assertDatabaseHas('checkpoints', [
        'mission_id' => $mission->id,
        'title' => 'Definir roteiro',
    ]);
});
