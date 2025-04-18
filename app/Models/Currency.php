<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    
    protected $fillable = ['code', 'name'];
    
    // 取得此貨幣作為基準貨幣的所有匯率
    public function rates()
    {
        return $this->hasMany(ExchangeRate::class, 'base_currency', 'code');
    }
    
    /**
     * 獲取所有支援的貨幣代碼
     *
     * @return array
     */
    public static function getSupportedCurrencies(): array
    {
        return self::pluck('code')->toArray();
    }
}