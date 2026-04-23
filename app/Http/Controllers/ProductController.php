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

        if ($request->filled('keyword')) {
            $query->where('product_name', 'like', '%' . $request->keyword . '%');
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('stock_min')) {
            $query->where('stock', '>=', $request->stock_min);
        }

        if ($request->filled('stock_max')) {
            $query->where('stock', '<=', $request->stock_max);
        }

        $sortableColumns = ['id', 'product_name', 'price', 'stock'];

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

        if ($request->ajax()) {
            return response()->json([
                'html' => view('products.partials.table', compact('products', 'sort', 'direction'))->render(),
            ]);
        }

        return view('products.index', compact('products', 'companies', 'sort', 'direction'));
    }

    public function create()
    {
        $companies = Company::orderBy('company_name')->pluck('company_name', 'id');
        return view('products.create', compact('companies'));
    }

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

    public function show(Product $product)
    {
        $product->load('company');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $companies = Company::orderBy('company_name')->pluck('company_name', 'id');
        return view('products.edit', compact('product', 'companies'));
    }

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

    public function destroy(Request $request, Product $product)
    {
        $deletedId = $product->id;
        $product->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '商品を削除しました',
                'id' => $deletedId,
            ]);
        }

        return redirect()->route('products.index')->with('success', '商品を削除しました');
    }

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