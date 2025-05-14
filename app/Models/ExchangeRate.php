<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = ['base_currency', 'target_currency', 'rate', 'last_updated'];

    protected $casts = [
        // datetime 自動轉換為 Carbon 日期時間物件
        'last_updated' => 'datetime',
        // decimal:8（最多 8 位小數）
        'rate' => 'decimal:8',
    ];

    /**
     * 獲取兩種貨幣之間的最新匯率
     *
     * @param string $fromCurrency 來源貨幣代碼
     * @param string $toCurrency 目標貨幣代碼
     * @return \App\Models\ExchangeRate|null
     */
    public static function getLatestRate(string $fromCurrency, string $toCurrency)
    {
        // 先嘗試直接尋找匹配的匯率記錄
        $rate = self::where('base_currency', $fromCurrency)
                    ->where('target_currency', $toCurrency)
                    ->orderBy('last_updated', 'desc')
                    ->first();

        if ($rate) {
            return $rate;
        }

        // 如果找不到直接匯率，嘗試尋找反向匯率
        $reverseRate = self::where('base_currency', $toCurrency)
                          ->where('target_currency', $fromCurrency)
                          ->orderBy('last_updated', 'desc')
                          ->first();

        if ($reverseRate) {
            // 創建一個新的匯率物件，但不儲存到資料庫，只用於本次轉換
            $rate = new ExchangeRate();
            $rate->base_currency = $fromCurrency;
            $rate->target_currency = $toCurrency;
            $rate->rate = 1 / $reverseRate->rate;
            $rate->last_updated = $reverseRate->last_updated;

            return $rate;
        }

        return null;
    }
}
