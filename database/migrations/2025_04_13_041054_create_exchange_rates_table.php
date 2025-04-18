<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 3); // 基準貨幣 (例如: USD)
            $table->string('target_currency', 3); // 目標貨幣 (例如: TWD)
            $table->decimal('rate', 16, 8); // 匯率值 (允許小數點後 8 位)
            $table->timestamp('last_updated')->useCurrent(); // 最後更新時間
            $table->timestamps();
            
            // 建立複合唯一索引確保不會有重複的貨幣對
            $table->unique(['base_currency', 'target_currency']);
            
            // 外鍵關聯 (可選, 如果需要更嚴格的資料完整性)
            // $table->foreign('base_currency')->references('code')->on('currencies');
            // $table->foreign('target_currency')->references('code')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
