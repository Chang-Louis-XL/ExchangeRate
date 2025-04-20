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
    
}
