<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Company;

class ProductController extends Controller
{
    /** 商品一覧（検索：商品名/会社） */
    public function index(Request $request)
    {
        $query = Product::with('company');

        if ($request->filled('keyword')) {
            $query->where('product_name', 'like', '%'.$request->keyword.'%');
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $products  = $query->paginate(10);
        // DBに登録済みの会社名をそのまま使う
        $companies = Company::orderBy('company_name')->pluck('company_name', 'id');

        return view('products.index', compact('products','companies'));
    }

    /** 新規登録フォーム（会社名は DB から取得） */
    public function create()
    {
        $companies = Company::orderBy('company_name')->pluck('company_name', 'id');
        return view('products.create', compact('companies'));
    }

    /** 商品登録（画面nameが旧名でもOKに正規化） */
    public function store(Request $request)
    {
        $rawName      = $request->input('product_name', $request->input('name'));
        $validated = $request->validate([
            'company_id' => ['required','exists:companies,id'],
            'price'      => ['required','integer','min:0'],
            'stock'      => ['required','integer','min:0'],
            'comment'    => ['nullable','string'],
        ], [], [
            'company_id' => '会社名',
            'price'      => '価格',
            'stock'      => '在庫数',
            'comment'    => 'コメント',
        ]);

        if (empty($rawName)) {
            return back()->withErrors(['product_name' => '商品名は必須です'])->withInput();
        }

        // maker 列（互換用）がある環境では会社名文字列も保存
        $companyName = Company::where('id', $validated['company_id'])->value('company_name');

        Product::create([
            'company_id'   => (int)$validated['company_id'],
            'product_name' => $rawName,
            'price'        => (int)$validated['price'],
            'stock'        => (int)$validated['stock'],
            'comment'      => $validated['comment'] ?? null,
            'maker'        => $companyName, // maker列が存在しNOT NULLならエラー回避
        ]);

        return redirect()->route('products.index')->with('success', '商品を登録しました');
    }

    /** 詳細 */
    public function show(Product $product)
    {
        $product->load('company');
        return view('products.show', compact('product'));
    }

    /** 編集フォーム（DBの会社名をプルダウン） */
    public function edit(Product $product)
    {
        $companies = Company::orderBy('company_name')->pluck('company_name','id');
        return view('products.edit', compact('product','companies'));
    }

    /** 更新 */
    public function update(Request $request, Product $product)
    {
        $rawName   = $request->input('product_name', $request->input('name'));
        $validated = $request->validate([
            'company_id' => ['required','exists:companies,id'],
            'price'      => ['required','integer','min:0'],
            'stock'      => ['required','integer','min:0'],
            'comment'    => ['nullable','string'],
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
            'company_id'   => (int)$validated['company_id'],
            'product_name' => $rawName,
            'price'        => (int)$validated['price'],
            'stock'        => (int)$validated['stock'],
            'comment'      => $validated['comment'] ?? null,
            'maker'        => $companyName,
        ]);

        return redirect()->route('products.show', $product->id)->with('success', '商品を更新しました');
    }

    /** 削除 */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', '商品を削除しました');
    }
}
