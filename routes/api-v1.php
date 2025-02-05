<?php declare(strict_types=1);

use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

Route::post('telegram/update/' . config('services.telegram-bot.token'), [TelegramController::class, 'update'])->name('telegram.update');
