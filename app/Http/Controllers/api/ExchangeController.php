<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExchangeController extends Controller
{
    /**
     * 貨幣轉換 API
     */
    public function convert(Request $request)
    {
        // 獲取請求數據
        $fromCurrency = strtoupper($request->from_currency);
        $toCurrency = strtoupper($request->to_currency);
        $amount = (float) $request->amount;
        
        // 驗證請求資料
        $validator = Validator::make([
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'amount' => $amount
        ], [
            'from_currency' => 'required|exists:currencies,code',
            'to_currency' => 'required|exists:currencies,code',
            'amount' => 'required|numeric|min:0.01'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 從資料庫查詢匯率資料
        $exchangeRate = ExchangeRate::where('base_currency', $fromCurrency)
                                  ->where('target_currency', $toCurrency)
                                  ->first();
        
        if (!$exchangeRate) {
            return response()->json(['error' => '找不到匯率資料'], 404);
        }
        
        // 計算轉換金額
        $convertedAmount = $amount * $exchangeRate->rate;

        // 返回結果
        return response()->json([
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'amount' => $amount,
            'converted_amount' => round($convertedAmount, 2),
            'rate' => $exchangeRate->rate,
            'last_updated' => $exchangeRate->last_updated
        ]);
    }

   
}