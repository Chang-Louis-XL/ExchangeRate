<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        
        // 使用原始控制器中的匯率資料
        $rates = [
            // USD 基準
            ['base_currency' => 'USD', 'target_currency' => 'USD', 'rate' => 1.0000, 'last_updated' => $now],
            ['base_currency' => 'USD', 'target_currency' => 'TWD', 'rate' => 31.5000, 'last_updated' => $now],
            ['base_currency' => 'USD', 'target_currency' => 'JPY', 'rate' => 148.5000, 'last_updated' => $now],
            
            // TWD 基準
            ['base_currency' => 'TWD', 'target_currency' => 'USD', 'rate' => 0.0317, 'last_updated' => $now],
            ['base_currency' => 'TWD', 'target_currency' => 'TWD', 'rate' => 1.0000, 'last_updated' => $now],
            ['base_currency' => 'TWD', 'target_currency' => 'JPY', 'rate' => 4.7143, 'last_updated' => $now],
            
            // JPY 基準
            ['base_currency' => 'JPY', 'target_currency' => 'USD', 'rate' => 0.00673, 'last_updated' => $now],
            ['base_currency' => 'JPY', 'target_currency' => 'TWD', 'rate' => 0.2121, 'last_updated' => $now],
            ['base_currency' => 'JPY', 'target_currency' => 'JPY', 'rate' => 1.0000, 'last_updated' => $now],
        ];
        
        foreach ($rates as $rate) {
            ExchangeRate::create($rate);
        }
    }
}