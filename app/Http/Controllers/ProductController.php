<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * 商品一覧表示（検索対応）
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        if ($request->filled('maker')) {
            $query->where('maker', $request->maker);
        }

        $products = $query->paginate(10);

        return view('products.index', compact('products'));
    }

    /**
     * 新規登録フォーム表示
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * 商品登録処理（DB保存付き）
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
            'maker'       => 'required|string|max:255',
            'comment'     => 'nullable|string|max:1000',
            'image_path'  => 'nullable|image|max:2048',
        ]);

        // 画像アップロード処理
        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('products.index')
                         ->with('success', '商品を登録しました');
    }

    /**
     * 商品詳細表示
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * 編集フォーム表示
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * 商品更新処理
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
            'maker'       => 'required|string|max:255',
            'comment'     => 'nullable|string|max:1000',
            'image_path'  => 'nullable|image|max:2048',
        ]);

        // 画像がアップロードされた場合のみ処理
        if ($request->hasFile('image_path')) {
            // 古い画像がある場合は削除
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }

            // 新しい画像を保存
            $validated['image_path'] = $request->file('image_path')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.show', $product->id)
                         ->with('success', '商品を更新しました');
    }

    /**
     * 商品削除処理
     */
    public function destroy(Product $product)
    {
        // 画像がある場合は削除
        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('products.index')
                         ->with('success', '商品を削除しました');
    }
}
