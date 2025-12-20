<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;

// Expense Routes
Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
Route::get('/expenses', [ExpenseController::class, 'index']);
Route::get('/expenses/{id}', [ExpenseController::class, 'show']);
Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
Route::post('/expenses/{id}/mark-paid', [ExpenseController::class, 'markAsPaid']);
