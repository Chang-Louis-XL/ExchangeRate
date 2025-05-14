<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrencyManagementController extends Controller
{
    /**
     * 獲取所有貨幣
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $currencies = Currency::all();
        return response()->json($currencies);
    }

    /**
     * 儲存新貨幣
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:3|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $currency = new Currency();
            $currency->code = $request->code;
            $currency->name = $request->name;
            $currency->symbol = $request->symbol;
            $currency->save();

            return response()->json($currency, 201);
        } catch (\Exception $e) {
            \Log::error('建立貨幣失敗: ' . $e->getMessage());
            return response()->json(['error' => '建立貨幣過程中發生錯誤'], 500);
        }
    }

    /**
     * 獲取指定貨幣
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $currency = Currency::findOrFail($id);
            return response()->json($currency);
        } catch (\Exception $e) {
            return response()->json(['error' => '找不到指定貨幣'], 404);
        }
    }

    /**
     * 更新指定貨幣
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $currency = Currency::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'code' => 'sometimes|required|string|max:3|unique:currencies,code,' . $currency->id,
                'name' => 'sometimes|required|string|max:255',
                'symbol' => 'sometimes|required|string|max:5'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($request->has('code')) {
                $currency->code = $request->code;
            }

            if ($request->has('name')) {
                $currency->name = $request->name;
            }

            if ($request->has('symbol')) {
                $currency->symbol = $request->symbol;
            }

            $currency->save();

            return response()->json($currency);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json(['error' => '找不到指定貨幣'], 404);
            }

            \Log::error('更新貨幣失敗: ' . $e->getMessage());
            return response()->json(['error' => '更新貨幣過程中發生錯誤'], 500);
        }
    }

    /**
     * 刪除指定貨幣
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $currency = Currency::findOrFail($id);
            $currency->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json(['error' => '找不到指定貨幣'], 404);
            }

            \Log::error('刪除貨幣失敗: ' . $e->getMessage());
            return response()->json(['error' => '刪除貨幣過程中發生錯誤'], 500);
        }
    }
}
