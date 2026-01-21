@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 40px auto;">
    <h2 style="font-size: 20px; margin-bottom: 20px;">商品情報編集画面</h2>

    {{-- バリデーションエラー表示（任意） --}}
    @if ($errors->any())
        <div style="margin-bottom:12px;color:#b91c1c;">
            <ul style="margin:0;padding-left:18px;">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" style="border: 1px solid #333; padding: 30px;">
        @csrf
        @method('PATCH')

        <table style="width: 100%;">
            <tr>
                <th style="text-align: left; width: 150px;"><i>ID.</i></th>
                <td>{{ $product->id }}</td>
            </tr>

            {{-- 商品名（DBは product_name） --}}
            <tr>
                <th style="text-align: left;">商品名 <span style="color:red">*</span></th>
                <td>
                    {{-- 画面のnameは従来どおり name のままでOK（コントローラで正規化） --}}
                    <input type="text" name="name" value="{{ old('name', $product->product_name) }}" required style="width: 100%; padding: 5px;">
                </td>
            </tr>

            {{-- メーカー名＝会社選択（DBは company_id）。編集ではDBの会社一覧から選択 --}}
            <tr>
                <th style="text-align: left;">メーカー名 <span style="color:red">*</span></th>
                <td>
                    <select name="company_id" required style="width: 100%; padding: 5px;">
                        <option value="">選択してください</option>
                        @foreach ($companies as $id => $name)
                            <option value="{{ $id }}" {{ (string)old('company_id', $product->company_id) === (string)$id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>

            <tr>
                <th style="text-align: left;">価格 <span style="color:red">*</span></th>
                <td>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" required style="width: 100%; padding: 5px;">
                </td>
            </tr>

            <tr>
                <th style="text-align: left;">在庫数 <span style="color:red">*</span></th>
                <td>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required style="width: 100%; padding: 5px;">
                </td>
            </tr>

            <tr>
                <th style="text-align: left;">コメント</th>
                <td>
                    <textarea name="comment" rows="2" style="width: 100%; padding: 5px;">{{ old('comment', $product->comment) }}</textarea>
                </td>
            </tr>

            {{-- 画像（DBは img_path を想定） --}}
            <tr>
                <th style="text-align: left;">商品画像</th>
                <td>
                    <input type="file" name="image">
                    @if ($product->img_path)
                        <div style="margin-top: 8px;">
                            <img src="{{ asset('storage/' . $product->img_path) }}" width="80">
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <div style="margin-top: 20px; display: flex; justify-content: flex-start; gap: 10px;">
            <button type="submit" style="background-color: orange; color: white; padding: 6px 16px; border: none; border-radius: 4px;">更新</button>
            <a href="{{ route('products.index') }}" style="background-color: deepskyblue; color: white; padding: 6px 16px; border-radius: 4px; text-decoration: none;">戻る</a>
        </div>
    </form>
</div>
@endsection
