<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;
    
    protected $fillable = ['base_currency', 'target_currency', 'rate', 'last_updated'];
    
    protected $casts = [
        'last_updated' => 'datetime',
        'rate' => 'decimal:8',
    ];
    
    // 取得基準貨幣
    public function baseCurrency()
    {
        return $this->belongsTo(Currency::class, 'base_currency', 'code');
    }
    
    // 取得目標貨幣
    public function targetCurrency()
    {
        return $this->belongsTo(Currency::class, 'target_currency', 'code');
    }
}
