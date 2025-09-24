<?php
// app/Jobs/ProcessPomodoroTimer.php

namespace App\Jobs;

use App\Models\PomodoroSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPomodoroTimer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $sessionId
    ) {}

    public function handle(): void
    {
        $session = PomodoroSession::find($this->sessionId);
        
        if (!$session) {
            Log::warning("Pomodoro session {$this->sessionId} not found");
            return;
        }

        // Se não está mais rodando, não faz nada
        if ($session->status !== 'running') {
            return;
        }

        // Verifica se expirou
        if ($session->isExpired()) {
            $session->complete();
            
            // Aqui você pode disparar eventos, notificações, etc.
            // event(new PomodoroCompleted($session));
            
            Log::info("Pomodoro session {$session->id} completed automatically");
            return;
        }

        // Se ainda não expirou, agenda próxima verificação em 10 segundos
        ProcessPomodoroTimer::dispatch($this->sessionId)
            ->delay(now()->addSeconds(10));
    }

    // Se o job falhar, tenta novamente
    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessPomodoroTimer failed for session {$this->sessionId}", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}