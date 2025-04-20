<?php

namespace App\Http\Controllers\Api;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CurrencyManagementController extends Controller
{
    /**
     * 獲取所有幣別列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $currencies = Currency::all();
        return response()->json(['data' => $currencies]);
    }

    /**
     * 儲存新增的幣別與匯率
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:3|unique:currencies,code',
            'name' => 'required|string|max:255',
            'rates' => 'nullable|array',
            'rates.*.target_currency' => 'required|string|max:3',
            'rates.*.rate' => 'required|numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        // DB::beginTransaction(); 之後，所有的資料庫操作（如新增、更新、刪除）都會暫時保留，不會馬上寫入資料庫
        DB::beginTransaction();
        try {
            // 創建新幣別
            $currency = Currency::create([
                // strtoupper() 轉成大寫字串
                // $request->xxx 可以取得表單欄位
                'code' => strtoupper($request->code),
                'name' => $request->name
            ]);

            // Carbon 是日期時間處理套件，Carbon::now()，會回傳一個代表「此刻」的 Carbon 日期時間物件。
            $now = Carbon::now();
            
            // 新增此幣別對自身的匯率 (永遠為 1.0)
            // 模型名稱（Model）：用「單數」且「大寫駝峰式」命名，例如 ExchangeRate
            // 資料表名稱（Table）：用「複數」且「底線分隔」命名，例如 exchange_rates
            ExchangeRate::create([
                'base_currency' => $currency->code,
                'target_currency' => $currency->code,
                'rate' => 1.0,
                'last_updated' => $now
            ]);
            
            // 添加對其他幣別的匯率
            if ($request->has('rates')) {
                foreach ($request->rates as $rateData) {
                    if (!empty($rateData['target_currency']) && !empty($rateData['rate'])) {
                        ExchangeRate::create([
                            'base_currency' => $currency->code,
                            'target_currency' => $rateData['target_currency'],
                            'rate' => $rateData['rate'],
                            'last_updated' => $now,
                        ]);
                    }
                }
            }
            
            DB::commit();
            return response()->json([
                'message' => '幣別及匯率新增成功！',
                'data' => $currency
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => '新增失敗：' . $e->getMessage()], 500);
        }
    }

    /**
     * 獲取指定幣別
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {   
        // findOrFail($id) 用主鍵去資料表查詢一筆資料
        $currency = Currency::findOrFail($id);
        // where、orWhere、get() 這些方法，都是 ExchangeRate 模型 Laravel Eloquent 提供的查詢功能
        $exchangeRates = ExchangeRate::where('base_currency', $currency->code)
            ->orWhere('target_currency', $currency->code)
            ->get();
        
        return response()->json([
            'currency' => $currency,
            'exchange_rates' => $exchangeRates
        ]);
    }

    /**
     * 更新指定幣別及匯率
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    //  $id 為路由帶入的資料
    public function update(Request $request, $id)
    {
        $currency = Currency::findOrFail($id);
        
        // 建立一個驗證器（Validator），呼叫 Validator 類別的 make 方法，用來檢查 $request 送來的所有資料是否符合規則
        $validator = Validator::make($request->all(), [
            'code' => [
                'required', 
                'string', 
                // Rule::unique(唯一)建立一個驗證規則，要求 code 欄位在 currencies 資料表中必須是唯一
                // ->ignore(忽略) 這樣當使用者更新現有幣別時，可以保持相同的代碼而不會被系統視為重複
                Rule::unique('currencies')->ignore($currency->id)
            ],
            'name' => 'required|string|max:255',
            'rates' => 'nullable|array',
            'rates.*.target_currency' => 'required|string',
            'rates.*.rate' => 'required|numeric|gt:0',
        ], [
            // 自訂錯誤訊息
            'rates.*.rate.required' => 'field is required',
            'rates.*.rate.numeric' => 'field must be a number',
            'rates.*.rate.gt' => 'field must be greater than 0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // 檢查是否有關聯的匯率資料且嘗試修改幣別代碼
        $oldCode = $currency->code;
        // strtoupper　把 $request->code 的內容全部轉成大寫字母。
        $newCode = strtoupper($request->code);
        
        if ($oldCode != $newCode) {
            $relatedRates = ExchangeRate::where('base_currency', $oldCode)
                ->orWhere('target_currency', $oldCode)
                // ->exists() 會回傳 true 或 false，表示有沒有這樣的資料。
                ->exists();
                
            if ($relatedRates) {
                return response()->json([
                    'error' => '無法修改幣別代碼，因為已有相關匯率資料存在，請刪除後新增！'
                ], 422);
            }
        }

        // DB::beginTransaction() 開始準備執行資料庫處理，沒有發生錯誤時DB::commit() 
        // 會提交所有的變更，讓資料庫更新生效；如果發生錯誤則DB::rollBack() 會回復到開始處理的狀態。
        DB::beginTransaction();
        try {
            // 更新幣別基本資料
            $currency->update([
                'code' => $newCode,
                'name' => $request->name
            ]);



            // 使用 Carbon 這個日期時間套件
            $now = Carbon::now();
            
            // 更新匯率資料
            if ($request->has('rates')) {
                foreach ($request->rates as $rateData) {
                    if (!empty($rateData['target_currency']) && !empty($rateData['rate'])) {
                        ExchangeRate::updateOrCreate(
                            [
                                'base_currency' => $newCode,
                                'target_currency' => $rateData['target_currency']
                            ],
                            [
                                'rate' => $rateData['rate'],
                                'last_updated' => $now
                            ]
                        );
                    }
                }
            }
            
            DB::commit();
            return response()->json([
                'message' => '幣別及匯率更新成功！',
                'data' => $currency
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => '更新失敗：' . $e->getMessage()], 500);
        }
    }

    /**
     * 刪除指定幣別
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // findOrFail($id) 用主鍵去資料表查詢一筆資料
        $currency = Currency::findOrFail($id);
    
        // 刪除所有 base_currency 或 target_currency 為該幣別的匯率資料
        ExchangeRate::where('base_currency', $currency->code)
            ->orWhere('target_currency', $currency->code)
            ->delete();
    
        $currency->delete();
    
        return response()->json(['message' => '幣別刪除成功！'], 200);
    }
}