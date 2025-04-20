<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    
    protected $fillable = ['code', 'name'];
    
    
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