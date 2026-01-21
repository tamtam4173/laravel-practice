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
            $query->where('product_name', 'like', '%' . $request->keyword . '%');
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $products  = $query->paginate(10);
        // 検索用プルダウン（DBから）
        $companies = Company::orderBy('company_name')->pluck('company_name', 'id');

        return view('products.index', compact('products', 'companies'));
    }

    /** 新規登録フォーム（3社を固定表示。未登録なら自動作成） */
    public function create()
    {
        $fixed = ['Coca-Cola', 'サントリー', 'キリン'];
        foreach ($fixed as $nm) {
            Company::firstOrCreate(['company_name' => $nm]);
        }

        $companies = Company::whereIn('company_name', $fixed)
            ->orderByRaw("FIELD(company_name, 'Coca-Cola','サントリー','キリン')")
            ->pluck('company_name', 'id');

        return view('products.create', compact('companies'));
    }

    /** 商品登録 */
    public function store(Request $request)
    {
        // 画面の name（name / product_name 両対応）
        $rawName      = $request->input('product_name', $request->input('name'));
        $rawCompanyId = $request->input('company_id');
        $rawMakerText = $request->input('maker'); // 旧画面から会社名文字列が来た場合の保険

        if (!$rawCompanyId && $rawMakerText) {
            $rawCompanyId = Company::where('company_name', $rawMakerText)->value('id');
        }

        $validated = $request->validate([
            'price'   => ['required','integer','min:0'],
            'stock'   => ['required','integer','min:0'],
            'comment' => ['nullable','string'],
        ], [], [
            'price'   => '価格',
            'stock'   => '在庫数',
            'comment' => 'コメント',
        ]);

        if (empty($rawCompanyId)) {
            return back()->withErrors(['company_id' => '会社名が不正です（DBに存在する会社名を選択してください）'])->withInput();
        }
        if (empty($rawName)) {
            return back()->withErrors(['product_name' => '商品名は必須です'])->withInput();
        }

        // maker カラムが NOT NULL のため会社名を同時保存
        $companyName = Company::where('id', $rawCompanyId)->value('company_name');

        Product::create([
            'company_id'   => (int)$rawCompanyId,
            'product_name' => $rawName,
            'price'        => (int)$validated['price'],
            'stock'        => (int)$validated['stock'],
            'comment'      => $validated['comment'] ?? null,
            'maker'        => $companyName,
        ]);

        return redirect()->route('products.index')->with('success', '商品を登録しました');
    }

    /** 詳細 */
    public function show(Product $product)
    {
        $product->load('company');
        return view('products.show', compact('product'));
    }

    /** 編集フォーム（会社名はDBの全件） */
    public function edit(Product $product)
    {
        $companies = Company::orderBy('company_name')->pluck('company_name', 'id');
        return view('products.edit', compact('product', 'companies'));
    }

    /** 更新 */
    public function update(Request $request, Product $product)
    {
        $rawName      = $request->input('product_name', $request->input('name'));
        $rawCompanyId = $request->input('company_id');
        $rawMakerText = $request->input('maker');

        if (!$rawCompanyId && $rawMakerText) {
            $rawCompanyId = Company::where('company_name', $rawMakerText)->value('id');
        }

        $validated = $request->validate([
            'price'   => ['required','integer','min:0'],
            'stock'   => ['required','integer','min:0'],
            'comment' => ['nullable','string'],
        ]);

        if (empty($rawCompanyId)) {
            return back()->withErrors(['company_id' => '会社名が不正です（DBに存在する会社名を選択してください）'])->withInput();
        }
        if (empty($rawName)) {
            return back()->withErrors(['product_name' => '商品名は必須です'])->withInput();
        }

        $companyName = Company::where('id', $rawCompanyId)->value('company_name');

        $product->update([
            'company_id'   => (int)$rawCompanyId,
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
