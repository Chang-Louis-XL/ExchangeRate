<?php

use App\Http\Controllers\Api\ExchangeController;
use App\Http\Controllers\Api\CurrencyManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 貨幣轉換 API
Route::post('/currency/convert', [ExchangeController::class, 'convert']);

// 幣別管理 API
Route::apiResource('currencies', CurrencyManagementController::class);