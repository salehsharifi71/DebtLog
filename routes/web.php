<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\Auth\LoginController;

// صفحه خانه (بدون نیاز به ورود)
Route::get('/', [HomeController::class, 'index'])->name('home');

// مسیرهای Authentication (فقط مهمانان)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// مسیرهای محافظت‌شده (نیاز به ورود)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', function () {
        return view('expenses.create');
    })->name('expenses.create');
});
