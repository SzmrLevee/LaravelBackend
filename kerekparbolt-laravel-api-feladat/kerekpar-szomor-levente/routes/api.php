<?php

use App\Http\Controllers\BicycleController;
use App\Http\Controllers\ManufacturerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('manufacturers', ManufacturerController::class);
Route::apiResource('bicycles', BicycleController::class)->whereNumber('bicycle');
