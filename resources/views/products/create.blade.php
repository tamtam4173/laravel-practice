@extends('layouts.app')

@section('content')
<div style="max-width: 500px; margin: 40px auto;">

    <h2 style="font-size: 24px; margin-bottom: 20px;">商品新規登録画面</h2>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data"
          style="border: 1px solid #999; padding: 20px; border-radius: 4px;">
        @csrf

        <div style="margin-bottom: 15px;">
            <label for="name" style="display: inline-block; width: 100px;">商品名<span style="color: red;">*</span></label>
            <input type="text" name="name" id="name" required style="width: 250px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="maker" style="display: inline-block; width: 100px;">メーカー名<span style="color: red;">*</span></label>
            <select name="maker" id="maker" required style="width: 254px;">
                <option value="">選択してください</option>
                <option value="Coca-Cola">Coca-Cola</option>
                <option value="サントリー">サントリー</option>
                <option value="キリン">キリン</option>
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="price" style="display: inline-block; width: 100px;">価格<span style="color: red;">*</span></label>
            <input type="number" name="price" id="price" required style="width: 250px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="stock" style="display: inline-block; width: 100px;">在庫数<span style="color: red;">*</span></label>
            <input type="number" name="stock" id="stock" required style="width: 250px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="comment" style="display: inline-block; width: 100px; vertical-align: top;">コメント</label>
            <textarea name="comment" id="comment" rows="3" style="width: 250px;"></textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="image" style="display: inline-block; width: 100px;">商品画像</label>
            <input type="file" name="image" id="image">
        </div>

        <div style="display: flex; justify-content: center; gap: 20px;">
            <button type="submit" style="background-color: orange; color: white; padding: 6px 16px; border: none; border-radius: 4px;">
                新規登録
            </button>
            <a href="{{ route('products.index') }}" style="background-color: deepskyblue; color: white; padding: 6px 16px; border-radius: 4px; text-decoration: none;">
                戻る
            </a>
        </div>
    </form>
</div>
@endsection
