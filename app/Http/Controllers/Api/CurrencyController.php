<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * 獲取所有支持的貨幣代碼
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $currencies = Currency::getSupportedCurrencies();
            return response()->json($currencies);
        } catch (\Exception $e) {
            \Log::error('獲取貨幣列表失敗: ' . $e->getMessage());
            return response()->json(['error' => '獲取貨幣列表失敗'], 500);
        }
    }
}
