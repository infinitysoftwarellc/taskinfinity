<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PomodoroSession;
use Carbon\Carbon;

class ProcessPomodoroTimers extends Command
{
    protected $signature = 'pomodoro:process';
    protected $description = 'Processa os timers pomodoro finalizados';

    public function handle()
    {
        // Encontra usuários cujo timer terminou e não está pausado
        $usersWithFinishedTimers = User::whereNotNull('pomodoro_ends_at')
            ->where('pomodoro_ends_at', '<=', now())
            ->whereNull('pomodoro_paused_at') // Importante para não processar timers pausados
            ->get();

        foreach ($usersWithFinishedTimers as $user) {
            $lastSession = PomodoroSession::where('user_id', $user->id)
                ->where('status', 'running')
                ->latest('started_at')
                ->first();

            if ($lastSession) {
                // Atualiza a sessão como 'completed'
                $lastSession->update([
                    'status' => 'completed',
                    'stopped_at' => $user->pomodoro_ends_at,
                    'actual_duration' => $lastSession->configured_duration,
                ]);
            }

            // Limpa o estado do timer do usuário
            $user->update([
                'pomodoro_ends_at' => null,
                'pomodoro_session_type' => null,
                'pomodoro_paused_at' => null,
            ]);

            // Aqui você pode adicionar lógica para iniciar a próxima sessão
            // ou enviar uma notificação para o usuário.
            $this->info("Pomodoro para o usuário {$user->email} finalizado.");
        }
    }
}