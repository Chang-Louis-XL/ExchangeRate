<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    // 匯率數據
    private $currencies = [
        [
            "base_currency" => "USD",
            "last_updated" => "2025-03-24T10:00:00+08:00",
            "rates" => [
                "USD" => 1.0000,
                "TWD" => 31.5000,
                "JPY" => 148.5000
            ]
        ],
        [
            "base_currency" => "TWD",
            "last_updated" => "2025-03-24T10:00:00+08:00",
            "rates" => [
                "USD" => 0.0317,
                "TWD" => 1.0000,
                "JPY" => 4.7143
            ]
        ],
        [
            "base_currency" => "JPY",
            "last_updated" => "2025-03-24T10:00:00+08:00",
            "rates" => [
                "USD" => 0.00673,
                "TWD" => 0.2121,
                "JPY" => 1.0000
            ]
        ]
    ];

    public function convert(Request $request)
    {
      
        // 獲取請求數據
        $fromCurrency = strtoupper($request->from_currency);
        $toCurrency = strtoupper($request->to_currency);
        $amount = (float) $request->amount;

        // 查找匯率
        $currencyData = null;
        foreach ($this->currencies as $currency) {
            if ($currency['base_currency'] === $fromCurrency) {
                $currencyData = $currency;
                break;
            }
        }

        // 計算轉換金額
        $rates = $currencyData['rates'];
        $rate = $rates[$toCurrency];
        $convertedAmount = $amount * $rate;

        // 返回結果
        return response()->json([
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'amount' => $amount,
            'converted_amount' => round($convertedAmount, 2),
            'rate' => $rate,
            'last_updated' => $currencyData['last_updated']
        ]);
    }

    // 獲取支援的貨幣列表
    public function getSupportedCurrenciesArray()
{
    $currencies = [];
    foreach ($this->currencies as $currency) {
        $currencies[] = $currency['base_currency'];
    }
    return $currencies;
}

}