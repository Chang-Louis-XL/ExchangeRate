<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
{
    $apiController = new \App\Http\Controllers\api\CurrencyController();
    $supportedCurrencies = $apiController->getSupportedCurrenciesArray();
    
    return view('currency.index', ['supportedCurrencies' => $supportedCurrencies]);
}
}