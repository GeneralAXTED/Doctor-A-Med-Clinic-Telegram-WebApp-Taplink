<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramBotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Telegram Webhook Handler
Route::post('/telegram/webhook', [TelegramBotController::class, 'handleWebhook']);

// WebApp Form Booking Notification Proxy
Route::post('/telegram/send-booking', [TelegramBotController::class, 'sendBookingNotification']);

// One-Click Set Webhook Route
Route::get('/telegram/set-webhook', [TelegramBotController::class, 'setWebhook']);
