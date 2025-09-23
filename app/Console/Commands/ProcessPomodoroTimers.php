<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PomodoroSession;
use Carbon\Carbon;

class ProcessPomodoroTimers extends Command
{
    protected $signature = 'pomodoro:process';
    protected $description = 'Process finished pomodoro timers and start the next session';

    public function handle()
    {
        $usersWithFinishedTimers = User::where('pomodoro_ends_at', '<=', now())->get();

        foreach ($usersWithFinishedTimers as $user) {
            // Encontra a última sessão "em andamento" para garantir
            $lastSession = PomodoroSession::where('user_id', $user->id)
                ->where('status', 'running')
                ->latest('started_at')
                ->first();

            if ($lastSession) {
                $lastSession->update([
                    'status' => 'completed',
                    'stopped_at' => $user->pomodoro_ends_at,
                ]);
            }
            
            // Lógica para iniciar o próximo ciclo (simplificada)
            // Aqui você pode adicionar a lógica completa de ciclos e pausas longas
            $nextSessionType = $user->pomodoro_session_type === 'work' ? 'short_break' : 'work';
            
            // Por simplicidade, vamos pausar o pomodoro após uma sessão
            // Para continuar automaticamente, você precisaria recriar a lógica de `timerFinished` aqui
            $user->update([
                'pomodoro_ends_at' => null,
                'pomodoro_session_type' => null,
            ]);

            // Opcional: Enviar uma notificação para o usuário
            // event(new PomodoroCycleFinished($user, $nextSessionType));
        }
    }
}