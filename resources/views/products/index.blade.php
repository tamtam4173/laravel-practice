@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 40px auto;">

    <h2 style="font-size: 24px; margin-bottom: 20px;">商品一覧画面</h2>

    {{-- 成功メッセージ --}}
    @if (session('success'))
        <div style="margin-bottom: 16px; padding: 10px 12px; border: 1px solid #b7eb8f; background:#f6ffed; color:#389e0d; border-radius:6px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- 検索フォーム --}}
    <form method="GET" action="{{ route('products.index') }}"
          style="display: flex; gap: 10px; margin-bottom: 16px; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
        <input type="text" name="keyword" placeholder="商品名で検索"
               value="{{ request('keyword') }}" style="flex: 1; padding: 6px;">
        <select name="company_id" style="padding: 6px; min-width: 200px;">
            <option value="">会社名（すべて）</option>
            @foreach($companies as $id => $name)
                <option value="{{ $id }}" {{ (string)$id === (string)request('company_id') ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        <button type="submit"
            style="padding: 6px 12px; border: 1px solid #888; border-radius: 4px; background: #f9f9f9;">
            検索
        </button>
    </form>

    {{-- 新規登録ボタン（右寄せ） --}}
    <div style="margin-bottom: 15px; text-align: right;">
        <a href="{{ route('products.create') }}"
           style="display:inline-block; background-color: orange; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px;">
            新規登録
        </a>
    </div>

    {{-- 一覧テーブル --}}
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ccc;">
        <thead>
            <tr style="background-color: #f7f7f7;">
                <th style="border: 1px solid #ccc; padding: 8px;">ID</th>
                <th style="border: 1px solid #ccc; padding: 8px;">商品名</th>
                <th style="border: 1px solid #ccc; padding: 8px;">会社名</th>
                <th style="border: 1px solid #ccc; padding: 8px;">価格</th>
                <th style="border: 1px solid #ccc; padding: 8px;">在庫数</th>
                <th style="border: 1px solid #ccc; padding: 8px;">操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $p)
                <tr style="text-align: center;">
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $p->id }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $p->product_name }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ optional($p->company)->company_name ?? '（未設定）' }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">¥{{ number_format($p->price) }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $p->stock }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">
                        <a href="{{ route('products.show', $p->id) }}"
                           style="background-color: deepskyblue; color: white; padding: 4px 8px; border-radius: 4px; margin-right: 4px; text-decoration:none;">
                           詳細
                        </a>
                        <form action="{{ route('products.destroy', $p->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('本当に削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                style="background-color: red; color: white; padding: 4px 8px; border-radius: 4px; border: none;">
                                削除
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="border: 1px solid #ccc; padding: 12px; text-align:center; color:#666;">
                        該当する商品は見つかりませんでした。
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 20px;">
        {{ $products->appends(request()->query())->links() }}
    </div>
</div>
@endsection
