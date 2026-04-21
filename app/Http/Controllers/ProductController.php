<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Company;
use App\Models\Sale;

class ProductController extends Controller
{
    /**
     * 商品一覧（検索：商品名 / 会社 / 価格 / 在庫数 / ソート）
     */
    public function index(Request $request)
    {
        $query = Product::with('company');

        // 商品名検索
        if ($request->filled('keyword')) {
            $query->where('product_name', 'like', '%' . $request->keyword . '%');
        }

        // 会社名検索
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // 価格（下限）
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        // 価格（上限）
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // 在庫数（下限）
        if ($request->filled('stock_min')) {
            $query->where('stock', '>=', $request->stock_min);
        }

        // 在庫数（上限）
        if ($request->filled('stock_max')) {
            $query->where('stock', '<=', $request->stock_max);
        }

        // ソート対象の許可カラム
        $sortableColumns = ['id', 'product_name', 'price', 'stock'];

        // 初期表示は id 降順
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        if (!in_array($sort, $sortableColumns, true)) {
            $sort = 'id';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $query->orderBy($sort, $direction);

        $products = $query->paginate(10)->appends($request->query());
        $companies = Company::orderBy('company_name')->pluck('company_name', 'id');

        // 非同期通信（AJAX）の場合はJSONを返す
        if ($request->ajax()) {
            return response()->json([
                'html' => view('products.partials.table', compact('products', 'sort', 'direction'))->render(),
            ]);
        }

        return view('products.index', compact('products', 'companies', 'sort', 'direction'));
    }

    /**
     * 新規登録画面
     */
    public function create()
    {
        $companies = Company::orderBy('company_name')->pluck('company_name', 'id');
        return view('products.create', compact('companies'));
    }

    /**
     * 商品登録
     */
    public function store(Request $request)
    {
        $rawName = $request->input('product_name', $request->input('name'));

        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'price'      => ['required', 'integer', 'min:0'],
            'stock'      => ['required', 'integer', 'min:0'],
            'comment'    => ['nullable', 'string'],
        ], [], [
            'company_id' => '会社名',
            'price'      => '価格',
            'stock'      => '在庫数',
            'comment'    => 'コメント',
        ]);

        if (empty($rawName)) {
            return back()->withErrors(['product_name' => '商品名は必須です'])->withInput();
        }

        $companyName = Company::where('id', $validated['company_id'])->value('company_name');

        Product::create([
            'company_id'   => (int) $validated['company_id'],
            'product_name' => $rawName,
            'price'        => (int) $validated['price'],
            'stock'        => (int) $validated['stock'],
            'comment'      => $validated['comment'] ?? null,
            'maker'        => $companyName,
        ]);

        return redirect()->route('products.index')->with('success', '商品を登録しました');
    }

    /**
     * 詳細画面
     */
    public function show(Product $product)
    {
        $product->load('company');
        return view('products.show', compact('product'));
    }

    /**
     * 編集画面
     */
    public function edit(Product $product)
    {
        $companies = Company::orderBy('company_name')->pluck('company_name', 'id');
        return view('products.edit', compact('product', 'companies'));
    }

    /**
     * 更新処理
     */
    public function update(Request $request, Product $product)
    {
        $rawName = $request->input('product_name', $request->input('name'));

        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'price'      => ['required', 'integer', 'min:0'],
            'stock'      => ['required', 'integer', 'min:0'],
            'comment'    => ['nullable', 'string'],
        ], [], [
            'company_id' => '会社名',
            'price'      => '価格',
            'stock'      => '在庫数',
            'comment'    => 'コメント',
        ]);

        if (empty($rawName)) {
            return back()->withErrors(['product_name' => '商品名は必須です'])->withInput();
        }

        $companyName = Company::where('id', $validated['company_id'])->value('company_name');

        $product->update([
            'company_id'   => (int) $validated['company_id'],
            'product_name' => $rawName,
            'price'        => (int) $validated['price'],
            'stock'        => (int) $validated['stock'],
            'comment'      => $validated['comment'] ?? null,
            'maker'        => $companyName,
        ]);

        return redirect()->route('products.show', $product->id)->with('success', '商品を更新しました');
    }

    /**
     * 削除処理
     */
    public function destroy(Request $request, Product $product)
    {
        $deletedId = $product->id;
        $product->delete();

        // 非同期削除の場合はJSONを返す
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '商品を削除しました',
                'id' => $deletedId,
            ]);
        }

        return redirect()->route('products.index')->with('success', '商品を削除しました');
    }

    /**
     * 購入処理API
     * ① salesテーブルにレコードを追加
     * ② productsテーブルの在庫数を減算
     * ③ 在庫が0ならエラー
     */
    public function purchase(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ], [], [
            'product_id' => '商品ID',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                // 排他制御
                $product = Product::lockForUpdate()->find($validated['product_id']);

                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => '商品が見つかりません。',
                    ], 404);
                }

                if ($product->stock <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => '在庫切れのため購入できません。',
                    ], 400);
                }

                // 在庫を1減らす
                $product->stock = $product->stock - 1;
                $product->save();

                // salesテーブルに記録
                Sale::create([
                    'product_id' => $product->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => '購入が完了しました。',
                    'product_id' => $product->id,
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