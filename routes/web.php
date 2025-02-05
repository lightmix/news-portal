<?php declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\ProtectGuestUser;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('news');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->middleware(ProtectGuestUser::class)->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->middleware(ProtectGuestUser::class)->name('profile.destroy');
});
