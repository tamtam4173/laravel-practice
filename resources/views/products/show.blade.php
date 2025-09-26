@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 40px auto; border: 1px solid #ccc; padding: 30px; border-radius: 10px; background-color: #fff;">

    <h2 style="font-size: 18px; margin-bottom: 25px;">商品情報詳細画面</h2>

    <table style="width: 100%; font-size: 14px;">
        <tr>
            <td style="width: 120px;"><strong>ID</strong></td>
            <td>{{ $product->id }}</td>
        </tr>

        <tr>
            <td><strong>商品画像</strong></td>
            <td>
                @if ($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="商品画像" width="100">
                @else
                    <span style="color: #999;">画像なし</span>
                @endif
            </td>
        </tr>

        <tr>
            <td><strong>商品名</strong></td>
            <td>{{ $product->name }}</td>
        </tr>

        <tr>
            <td><strong>メーカー</strong></td>
            <td>{{ $product->maker }}</td>
        </tr>

        <tr>
            <td><strong>価格</strong></td>
            <td>¥{{ number_format($product->price) }}</td>
        </tr>

        <tr>
            <td><strong>在庫数</strong></td>
            <td>{{ $product->stock }}</td>
        </tr>

        <tr>
            <td><strong>コメント</strong></td>
            <td>
                <textarea readonly style="width: 100%; resize: none; border: 1px solid #ccc; padding: 6px; background-color: #f9f9f9;">{{ $product->comment }}</textarea>
            </td>
        </tr>
    </table>

    <div style="margin-top: 25px; display: flex; gap: 10px;">
        <a href="{{ route('products.edit', $product->id) }}" style="background-color: orange; color: white; padding: 6px 16px; border-radius: 4px; text-decoration: none;">編集</a>
        <a href="{{ route('products.index') }}" style="background-color: deepskyblue; color: white; padding: 6px 16px; border-radius: 4px; text-decoration: none;">戻る</a>
    </div>

</div>
@endsection
