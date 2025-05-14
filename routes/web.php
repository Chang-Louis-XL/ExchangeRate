<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| 這裡是註冊 web 路由的地方。這些路由會被 RouteServiceProvider 載入
| 並被分配到 "web" 中間件組。建立令人驚奇的事物吧！
|
*/

// 原有的伺服器端渲染路由，可選保留或移除
// Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

// SPA 路由 - 所有不匹配 /api 的請求都指向 SPA 入口點
Route::get('/{path?}', function () {
    return view('spa');
})->where('path', '(?!api).*');


