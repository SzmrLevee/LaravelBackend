<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PandaController;

Route::get('/pandas', [PandaController::class, 'index'])
    ->name('pandas.index');

Route::get('/pandas/{panda}', [PandaController::class, 'show'])
    ->name('pandas.show');

Route::post('/pandas', [PandaController::class, 'store'])
    ->name('pandas.store');

Route::put('/pandas/{panda}', [PandaController::class, 'update'])
    ->name('pandas.update');

Route::delete('/pandas/{panda}', [PandaController::class, 'destroy'])
    ->name('pandas.destroy');
