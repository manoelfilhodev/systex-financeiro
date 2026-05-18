<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LancamentoController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified', 'subscription'])
    ->name('dashboard');

Route::middleware(['auth', 'subscription'])->group(function () {
    Route::resource('categorias', CategoriaController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('lancamentos', LancamentoController::class)->except(['show']);
    Route::post('/theme', [ThemeController::class, 'update'])->name('theme.update');
    Route::get('/premium', PremiumController::class)->name('premium.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'subscription', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::post('/payments/{payment}/approve', [AdminController::class, 'approvePayment'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [AdminController::class, 'rejectPayment'])->name('payments.reject');
    Route::post('/users/{user}/extend-trial', [AdminController::class, 'extendTrial'])->name('users.extend-trial');
    Route::patch('/users/{user}/plan', [AdminController::class, 'updateUserPlan'])->name('users.plan');
});

require __DIR__.'/auth.php';
