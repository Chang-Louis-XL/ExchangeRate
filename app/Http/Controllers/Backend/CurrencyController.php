<?php

namespace App\Http\Controllers\Backend;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class CurrencyController extends Controller
{
    /**
     * 顯示所有幣別列表頁面
     */
    public function index()
    {
        $currencies = Currency::all();
        return view('backend.index', compact('currencies'));
    }

    /**
     * 顯示幣別創建表單頁面
     */
    public function create()
    {
        $currencies = Currency::all();
        return view('backend.create', compact('currencies'));
    }

    /**
     * 顯示指定幣別詳情頁面
     */
    public function show($id)
    {
        $currency = Currency::findOrFail($id);
        $exchangeRates = ExchangeRate::where('base_currency', $currency->code)
            ->orWhere('target_currency', $currency->code)
            ->get();
        
        return view('backend.show', compact('currency', 'exchangeRates'));
    }

    /**
     * 顯示編輯幣別表單頁面
     */
    public function edit($id)
    {
        $currency = Currency::findOrFail($id);
        $otherCurrencies = Currency::where('id', '!=', $id)->get();
        
        // 獲取當前幣別的匯率資訊 (排除對自身的匯率)
        $existingRates = ExchangeRate::where('base_currency', $currency->code)
            ->where('target_currency', '!=', $currency->code)
            ->get()
            ->keyBy('target_currency');
        
        return view('backend.edit', compact('currency', 'otherCurrencies', 'existingRates'));
    }
}