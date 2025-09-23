<?php

namespace App\Policies;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FolderPolicy
{
    public function view(User $user, Folder $folder): bool
    {
        return $user->id === $folder->user_id;
    }

    public function update(User $user, Folder $folder): bool
    {
        return $user->id === $folder->user_id;
    }

    public function delete(User $user, Folder $folder): bool
    {
        return $user->id === $folder->user_id;
    }
}