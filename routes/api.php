<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\ExchangeController;
use App\Http\Controllers\Api\CurrencyManagementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| 此處定義 API 路由。這些路由將由 RouteServiceProvider 加載
| 並被分配到 "api" 中間件組。開始構建 API 吧！
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 貨幣相關 API 端點
Route::get('/currencies', [CurrencyController::class, 'index']);
Route::post('/currency/convert', [ExchangeController::class, 'convert']);

// 幣別管理 API - 後台功能
Route::prefix('admin')->group(function() {
    Route::apiResource('currencies', CurrencyManagementController::class);
});
