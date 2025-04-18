<?php

namespace App\Http\Controllers;

use App\Models\Currency;

class CurrencyController extends Controller
{
    public function index()
    {
        // 直接靜態調用模型方法
        $supportedCurrencies = Currency::getSupportedCurrencies();
        return view('index', ['supportedCurrencies' => $supportedCurrencies]);
    }
}