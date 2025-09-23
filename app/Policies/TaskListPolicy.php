<?php

namespace App\Policies;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskListPolicy
{
    // app/Policies/TaskListPolicy.php

public function view(User $user, TaskList $taskList): bool
{

    return $user->id === $taskList->user_id;
}

    public function update(User $user, TaskList $taskList): bool
    {
        return $user->id === $taskList->user_id;
    }

    public function delete(User $user, TaskList $taskList): bool
    {
        return $user->id === $taskList->user_id;
    }
}