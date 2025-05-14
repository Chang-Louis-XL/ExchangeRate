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
     * 將一種貨幣轉換為另一種貨幣
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function convert(Request $request)
    {
        // 驗證輸入
        $validator = Validator::make($request->all(), [
            'from_currency' => 'required|string|exists:currencies,code',
            'to_currency' => 'required|string|exists:currencies,code',
            'amount' => 'required|numeric|min:0.01'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $fromCurrency = $request->input('from_currency');
            $toCurrency = $request->input('to_currency');
            $amount = $request->input('amount');

            // 獲取最新的匯率
            $exchangeRate = ExchangeRate::getLatestRate($fromCurrency, $toCurrency);

            if (!$exchangeRate) {
                return response()->json(['error' => '找不到匯率數據'], 404);
            }

            // 計算轉換後的金額
            $convertedAmount = $amount * $exchangeRate->rate;

            return response()->json([
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'amount' => $amount,
                'rate' => $exchangeRate->rate,
                'converted_amount' => $convertedAmount,
                'last_updated' => $exchangeRate->updated_at
            ]);

        } catch (\Exception $e) {
            \Log::error('貨幣轉換失敗: ' . $e->getMessage());
            return response()->json(['error' => '貨幣轉換過程中發生錯誤'], 500);
        }
    }
}
