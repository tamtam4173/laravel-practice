@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 40px auto;">

    <h2 style="font-size: 24px; margin-bottom: 20px;">商品一覧画面</h2>

    {{-- 検索フォーム --}}
<form method="GET" action="{{ route('products.index') }}" style="display: flex; gap: 10px; margin-bottom: 20px;">
    <input type="text" name="keyword" placeholder="商品名で検索" value="{{ request('keyword') }}" style="flex: 1; padding: 6px;">
        <select name="maker" style="padding: 6px;">
            <option value="">メーカー名</option>
            <option value="Coca-Cola" {{ request('maker') == 'Coca-Cola' ? 'selected' : '' }}>Coca-Cola</option>
            <option value="サントリー" {{ request('maker') == 'サントリー' ? 'selected' : '' }}>サントリー</option>
        <option value="キリン" {{ request('maker') == 'キリン' ? 'selected' : '' }}>キリン</option>
        </select>
    <button type="submit" style="padding: 6px 16px; border: 1px solid #333; background-color: #f2f2f2; border-radius: 4px;">検索</button>
</form>
    </div>

    {{-- 新規登録ボタン --}}
    <div style="margin-bottom: 15px; text-align: right;">
        <a href="{{ route('products.create') }}" style="background-color: orange; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px;">新規登録</a>
    </div>

    {{-- 一覧テーブル --}}
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ccc;">
        <thead>
            <tr style="background-color: #f7f7f7;">
                <th style="border: 1px solid #ccc; padding: 8px;">ID</th>
                <th style="border: 1px solid #ccc; padding: 8px;">商品画像</th>
                <th style="border: 1px solid #ccc; padding: 8px;">商品名</th>
                <th style="border: 1px solid #ccc; padding: 8px;">価格</th>
                <th style="border: 1px solid #ccc; padding: 8px;">在庫数</th>
                <th style="border: 1px solid #ccc; padding: 8px;">メーカー名</th>
                <th style="border: 1px solid #ccc; padding: 8px;">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $p)
                <tr style="text-align: center;">
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $p->id }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">
                        <img src="https://via.placeholder.com/60x60?text=No+Image" alt="商品画像" width="60">
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $p->name }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">¥{{ number_format($p->price) }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $p->stock }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $p->maker }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">
                        <a href="{{ route('products.show', $p->id) }}" style="background-color: deepskyblue; color: white; padding: 4px 8px; border-radius: 4px; margin-right: 4px;">詳細</a>
                        <form action="{{ route('products.destroy', $p->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('本当に削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background-color: red; color: white; padding: 4px 8px; border-radius: 4px; border: none;">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ページネーション --}}
    <div style="text-align: center; margin-top: 20px;">
        {{ $products->appends(request()->query())->links() }}
    </div>
</div>
@endsection
