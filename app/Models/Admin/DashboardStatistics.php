<?php

namespace App\Models\Admin;

use App\Models\Mission;
use App\Models\TaskList;
use App\Models\User;

final class DashboardStatistics
{
    public function __construct(
        public readonly int $usersCount,
        public readonly int $missionsCount,
        public readonly int $listsCount,
    ) {
    }

    public static function collect(): self
    {
        return new self(
            usersCount: User::count(),
            missionsCount: Mission::count(),
            listsCount: TaskList::count(),
        );
    }

    /**
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return [
            'usersCount' => $this->usersCount,
            'missionsCount' => $this->missionsCount,
            'listsCount' => $this->listsCount,
        ];
    }
}
