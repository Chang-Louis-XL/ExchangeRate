<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurrencyController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [CurrencyController::class, 'index']);


