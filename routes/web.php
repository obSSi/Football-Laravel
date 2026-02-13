<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChampionnatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\MatchController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/championnats', [ChampionnatController::class, 'index'])->name('championnats.index');
    Route::post('/championnats', [ChampionnatController::class, 'store'])
        ->name('championnats.store')
        ->middleware('admin');
    Route::patch('/championnats/{championnat}', [ChampionnatController::class, 'update'])
        ->name('championnats.update')
        ->middleware('admin');
    Route::delete('/championnats/{championnat}', [ChampionnatController::class, 'destroy'])
        ->name('championnats.destroy')
        ->middleware('admin');

    Route::get('/equipes', [EquipeController::class, 'index'])->name('equipes.index');
    Route::post('/equipes', [EquipeController::class, 'store'])
        ->name('equipes.store')
        ->middleware('admin');
    Route::patch('/equipes/{equipe}', [EquipeController::class, 'update'])
        ->name('equipes.update')
        ->middleware('admin');
    Route::delete('/equipes/{equipe}', [EquipeController::class, 'destroy'])
        ->name('equipes.destroy')
        ->middleware('admin');

    Route::get('/matchs', [MatchController::class, 'index'])->name('matchs.index');
    Route::get('/classement', [MatchController::class, 'classement'])->name('matchs.classement');
    Route::post('/matchs/generer', [MatchController::class, 'generer'])
        ->name('matchs.generer')
        ->middleware('admin');
    Route::post('/matchs/simuler', [MatchController::class, 'simuler'])
        ->name('matchs.simuler')
        ->middleware('admin');
    Route::patch('/matchs/{fixture}/score', [MatchController::class, 'updateScore'])
        ->name('matchs.score')
        ->middleware('admin');
    Route::post('/matchs/reset', [MatchController::class, 'reset'])
        ->name('matchs.reset')
        ->middleware('admin');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
