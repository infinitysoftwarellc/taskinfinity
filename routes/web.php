<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\Folder\FolderController;



Route::view('/', 'welcome');

Route::view('/profile', 'profile')
        ->name('profile');


Route::middleware(['auth'])->prefix('webapp')->name('webapp.')->group(function () {

    Route::get('/task', [TaskController::class, 'index'])
        ->middleware(['verified'])
        ->name('task.index');
     Route::resource('folders', FolderController::class);   

    
});

require __DIR__.'/auth.php';
