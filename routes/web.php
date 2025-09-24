<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rotas existentes...
Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    // --- NOVAS ROTAS PARA O INFINITYTASK ---

    // Rota raiz redireciona para a caixa de entrada (inbox)
    Route::get('/', function () {
        return redirect()->route('tasks.filter', ['filter' => 'inbox']);
    })->name('home');

    // Rota para filtros estáticos (ex: /tasks/today, /tasks/inbox)
    Route::get('/tasks/{filter}', [TaskController::class, 'index'])
        ->where('filter', 'inbox|today|upcoming') // Defina aqui seus filtros válidos
        ->name('tasks.filter');

    // Rota para projetos dinâmicos (ex: /projects/1-meu-projeto)
    // Utiliza Route Model Binding para injetar o objeto Project diretamente
    Route::get('/projects/{project}', [TaskController::class, 'index'])
        ->name('tasks.project');
});


require __DIR__.'/auth.php';