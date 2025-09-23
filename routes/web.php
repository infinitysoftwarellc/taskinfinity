<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\Folder\FolderController;
use App\Http\Controllers\TaskList\TaskListController;
use App\Http\Controllers\PomodoroController;



Route::view('/', 'welcome');

Route::view('/profile', 'profile')
        ->name('profile');


Route::middleware(['auth'])->prefix('webapp')->name('webapp.')->group(function () {

    Route::get('/task', [TaskController::class, 'index'])
        ->middleware(['verified'])
        ->name('task.index');
     Route::resource('folders', FolderController::class);
     
    Route::get('folders/{folder}/tasklists', [TaskListController::class, 'index'])->name('tasklists.index');
    Route::resource('tasklists', TaskListController::class)->except(['index']);
    Route::resource('tasks', TaskController::class)->except(['index']);
    Route::get('/pomodoro', [PomodoroController::class, 'index'])
    ->middleware(['auth', 'verified']) // Garante que apenas usuários logados possam acessar
    ->name('pomodoro');

    
});

require __DIR__.'/auth.php';
 // Adicione no topo

// ...