<?php

declare(strict_types=1);

use App\Http\Livewire\Spotlight\ProjectSearchCommand;
use App\Http\Livewire\Spotlight\TaskSearchCommand;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use LivewireUI\Spotlight\SpotlightSearchResult;

beforeEach(function (): void {
    Config::set('scout.driver', 'database');
    Config::set('scout.queue', false);
    Config::set('scout.after_commit', false);
});

test('task spotlight search only returns missions owned by the authenticated user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $taskList = TaskList::create([
        'user_id' => $user->id,
        'name' => 'Inbox',
    ]);

    $ownedTask = Task::create([
        'user_id' => $user->id,
        'list_id' => $taskList->id,
        'title' => 'Revisar contrato importante',
        'status' => 'active',
        'position' => 1,
    ]);

    Task::create([
        'user_id' => $other->id,
        'list_id' => $taskList->id,
        'title' => 'Revisar contrato importante',
        'status' => 'active',
        'position' => 1,
    ]);

    $this->actingAs($user);

    $results = (new TaskSearchCommand())->searchTask('contrato');

    expect($results)
        ->toHaveCount(1)
        ->and($results[0])->toBeInstanceOf(SpotlightSearchResult::class)
        ->and($results[0]->getId())->toBe((string) $ownedTask->getKey());
});

test('project spotlight search respects user ownership', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $ownedProject = Project::create([
        'user_id' => $user->id,
        'name' => 'Projeto Aurora',
        'view_type' => 'list',
        'position' => 1,
    ]);

    Project::create([
        'user_id' => $other->id,
        'name' => 'Projeto Aurora',
        'view_type' => 'list',
        'position' => 1,
    ]);

    $this->actingAs($user);

    $results = (new ProjectSearchCommand())->searchProject('Aurora');

    expect($results)
        ->toHaveCount(1)
        ->and($results[0])->toBeInstanceOf(SpotlightSearchResult::class)
        ->and($results[0]->getId())->toBe((string) $ownedProject->getKey());
});
