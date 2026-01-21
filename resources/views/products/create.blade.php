@extends('layouts.app')

@section('content')
<div style="max-width: 520px; margin: 40px auto;">
  <h2 style="font-size: 22px; margin-bottom: 18px;">商品新規登録画面</h2>

  {{-- エラー表示 --}}
  @if ($errors->any())
    <div style="margin-bottom:12px;color:#b91c1c;">
      <ul style="margin:0;padding-left:18px;">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data"
        style="border:1px solid #999; padding:24px 26px;">
    @csrf

    {{-- 商品名* --}}
    <div style="display:flex; align-items:center; margin-bottom:16px;">
      <label style="width:115px;">商品名 <span style="color:#e11d48;">*</span></label>
      <input type="text" name="name" value="{{ old('name') }}"
             style="flex:1; padding:6px; border:1px solid #cfcfcf; border-radius:2px;" required>
    </div>

    {{-- メーカー名*（companiesのidを値にする） --}}
    <div style="display:flex; align-items:center; margin-bottom:16px;">
      <label style="width:115px;">メーカー名 <span style="color:#e11d48;">*</span></label>
      <select name="company_id"
              style="flex:1; padding:6px; border:1px solid #cfcfcf; border-radius:2px;" required>
        <option value="">選択してください</option>
        @foreach($companies as $id => $name)
          <option value="{{ $id }}" {{ (string)$id === old('company_id') ? 'selected' : '' }}>
            {{ $name }}
          </option>
        @endforeach
      </select>
    </div>

    @if($companies->isEmpty())
      <p style="margin:-8px 0 12px; color:#b45309; font-size:13px;">
        ※ 会社データが未登録です。先に companies テーブルへ会社名を登録してください。
      </p>
    @endif

    {{-- 価格* --}}
    <div style="display:flex; align-items:center; margin-bottom:16px;">
      <label style="width:115px;">価格 <span style="color:#e11d48;">*</span></label>
      <input type="number" name="price" value="{{ old('price') }}"
             style="flex:1; padding:6px; border:1px solid #cfcfcf; border-radius:2px;" required>
    </div>

    {{-- 在庫数* --}}
    <div style="display:flex; align-items:center; margin-bottom:16px;">
      <label style="width:115px;">在庫数 <span style="color:#e11d48;">*</span></label>
      <input type="number" name="stock" value="{{ old('stock') }}"
             style="flex:1; padding:6px; border:1px solid #cfcfcf; border-radius:2px;" required>
    </div>

    {{-- コメント --}}
    <div style="display:flex; align-items:flex-start; margin-bottom:16px;">
      <label style="width:115px; line-height:28px;">コメント</label>
      <textarea name="comment" rows="3"
                style="flex:1; padding:6px; border:1px solid #cfcfcf; border-radius:2px;">{{ old('comment') }}</textarea>
    </div>

    {{-- 商品画像 --}}
    <div style="display:flex; align-items:center; margin-bottom:24px;">
      <label style="width:115px;">商品画像</label>
      <input type="file" name="image">
    </div>

    {{-- ボタン --}}
    <div style="display:flex; gap:12px; padding-left:115px;">
      <button type="submit"
              style="background:#f59e0b; color:#fff; border:none; padding:8px 16px; border-radius:4px;">
        新規登録
      </button>
      <a href="{{ route('products.index') }}"
         style="background:#22c1ee; color:#fff; text-decoration:none; padding:8px 16px; border-radius:4px;">
        戻る
      </a>
    </div>
  </form>
</div>
@endsection
