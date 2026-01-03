<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PandaController;

Route::get('/pandas', [PandaController::class, 'index'])
    ->name('pandas.index');

Route::get('/pandas/{panda}', [PandaController::class, 'show'])
    ->name('pandas.show');

Route::delete('/pandas/{panda}', [PandaController::class, 'destroy'])
    ->name('pandas.destroy');