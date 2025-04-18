<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\CurrencyController;

  // 幣別管理路由
Route::get('/currencies', [CurrencyController::class, 'index'])->name('index');
Route::get('/currencies/create', [CurrencyController::class, 'create'])->name('create');
Route::get('/currencies/{id}', [CurrencyController::class, 'show'])->name('show');
Route::get('/currencies/{id}/edit', [CurrencyController::class, 'edit'])->name('edit');
