<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'index');

Route::get('/board', [\App\Http\Controllers\BoardController::class, 'index'])->name('board.index');
Route::get('/board/{station:code}', [\App\Http\Controllers\BoardController::class, 'show'])->name('board.show');

Route::view('/stats', 'board.stats')->name('stats.index');
