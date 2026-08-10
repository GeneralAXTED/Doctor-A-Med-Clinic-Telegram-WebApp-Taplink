<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctoraController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Main Doctor-A Taplink WebApp Route
Route::get('/', [DoctoraController::class, 'index'])->name('doctora.home');
Route::get('/doctora', [DoctoraController::class, 'index'])->name('doctora.app');
