<?php
// app/Console/Commands/CleanupAbandonedPomodoroSessions.php

namespace App\Console\Commands;

use App\Models\PomodoroSession;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupAbandonedPomodoroSessions extends Command
{
    protected $signature = 'pomodoro:cleanup';
    protected $description = 'Cleanup abandoned pomodoro sessions that are running for too long';

    public function handle()
    {
        // Busca sessões que estão "rodando" há mais de 2 horas
        $abandonedSessions = PomodoroSession::where('status', 'running')
            ->where('started_at', '<', now()->subHours(2))
            ->get();

        $this->info("Found {$abandonedSessions->count()} abandoned sessions");

        foreach ($abandonedSessions as $session) {
            $session->abandon('Sessão abandonada automaticamente (mais de 2h inativa)');
            $this->line("Abandoned session ID: {$session->id} for user ID: {$session->user_id}");
        }

        // Busca sessões pausadas há mais de 24 horas
        $oldPausedSessions = PomodoroSession::where('status', 'paused')
            ->where('paused_at', '<', now()->subDay())
            ->get();

        $this->info("Found {$oldPausedSessions->count()} old paused sessions");

        foreach ($oldPausedSessions as $session) {
            $session->abandon('Sessão pausada abandonada automaticamente (mais de 24h pausada)');
            $this->line("Abandoned paused session ID: {$session->id} for user ID: {$session->user_id}");
        }

        $totalCleaned = $abandonedSessions->count() + $oldPausedSessions->count();
        $this->info("✅ Cleanup completed! {$totalCleaned} sessions processed.");

        return Command::SUCCESS;
    }
}