<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;

Route::get('/', [ExpenseController::class, 'index'])->name('expenses.index');
Route::get('/expenses/create', function () {
    return view('expenses.create');
})->name('expenses.create');
