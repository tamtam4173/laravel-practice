<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Sale;

class SaleController extends Controller
{
    /**
     * 購入処理API
     * ① salesテーブルにレコードを追加
     * ② productsテーブルの在庫数を減算
     * ③ 在庫不足ならエラー
     */
    public function purchase(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['nullable', 'integer', 'min:1'],
        ], [], [
            'product_id' => '商品ID',
            'quantity'   => '購入数',
        ]);

        $quantity = $validated['quantity'] ?? 1;

        try {
            return DB::transaction(function () use ($validated, $quantity) {
                $product = Product::lockForUpdate()->find($validated['product_id']);

                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => '商品が見つかりません。',
                    ], 404);
                }

                if ($product->stock < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => '在庫不足のため購入できません。',
                    ], 400);
                }

                $unitPrice = $product->price;
                $subtotal = $unitPrice * $quantity;

                Sale::create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal'   => $subtotal,
                ]);

                $product->stock = $product->stock - $quantity;
                $product->save();

                return response()->json([
                    'success' => true,
                    'message' => '購入が完了しました。',
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'remaining_stock' => $product->stock,
                ], 200);
            });
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => '購入処理中にエラーが発生しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}