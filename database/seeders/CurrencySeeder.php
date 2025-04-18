<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => '美元'],
            ['code' => 'TWD', 'name' => '新台幣'],
            ['code' => 'JPY', 'name' => '日圓'],
        ];
        
        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}