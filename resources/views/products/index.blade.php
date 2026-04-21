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
    <form id="search-form" method="GET" action="{{ route('products.index') }}"
          style="margin-bottom: 16px; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">

        <input type="hidden" name="sort" id="sort" value="{{ $sort ?? 'id' }}">
        <input type="hidden" name="direction" id="direction" value="{{ $direction ?? 'desc' }}">

        <div style="display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap;">
            <input type="text" name="keyword" placeholder="商品名で検索"
                   value="{{ request('keyword') }}" style="flex: 1; min-width: 180px; padding: 6px;">

            <select name="company_id" style="padding: 6px; min-width: 200px;">
                <option value="">会社名（すべて）</option>
                @foreach($companies as $id => $name)
                    <option value="{{ $id }}" {{ (string)$id === (string)request('company_id') ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap;">
            <input type="number" name="price_min" placeholder="価格（下限）"
                   value="{{ request('price_min') }}" style="width: 140px; padding: 6px;">

            <input type="number" name="price_max" placeholder="価格（上限）"
                   value="{{ request('price_max') }}" style="width: 140px; padding: 6px;">

            <input type="number" name="stock_min" placeholder="在庫数（下限）"
                   value="{{ request('stock_min') }}" style="width: 140px; padding: 6px;">

            <input type="number" name="stock_max" placeholder="在庫数（上限）"
                   value="{{ request('stock_max') }}" style="width: 140px; padding: 6px;">

            <button type="submit"
                    style="padding: 6px 12px; border: 1px solid #888; border-radius: 4px; background: #f9f9f9;">
                検索
            </button>
        </div>
    </form>

    {{-- 新規登録ボタン --}}
    <div style="margin-bottom: 15px; text-align: right;">
        <a href="{{ route('products.create') }}"
           style="display:inline-block; background-color: orange; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px;">
            新規登録
        </a>
    </div>

    {{-- 一覧テーブル差し込み --}}
    <div id="product-table-area">
        @include('products.partials.table', [
            'products' => $products,
            'sort' => $sort ?? 'id',
            'direction' => $direction ?? 'desc'
        ])
    </div>
</div>

<script>
async function submitSearchForm() {
    const form = document.getElementById('search-form');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);

    try {
        const response = await fetch(form.action + '?' + params.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        document.getElementById('product-table-area').innerHTML = data.html;

        bindSortEvents();
        bindDeleteEvents();
    } catch (error) {
        alert('検索中にエラーが発生しました。');
        console.error(error);
    }
}

document.getElementById('search-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    await submitSearchForm();
});

function bindSortEvents() {
    document.querySelectorAll('.sortable-header').forEach(header => {
        header.addEventListener('click', async function () {
            const clickedSort = this.dataset.sort;
            const sortInput = document.getElementById('sort');
            const directionInput = document.getElementById('direction');

            if (sortInput.value === clickedSort) {
                directionInput.value = directionInput.value === 'asc' ? 'desc' : 'asc';
            } else {
                sortInput.value = clickedSort;
                directionInput.value = 'asc';
            }

            await submitSearchForm();
        });
    });
}

function bindDeleteEvents() {
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (!confirm('本当に削除しますか？')) {
                return;
            }

            const productId = this.dataset.id;
            const formData = new FormData(this);

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    const row = document.getElementById('product-row-' + productId);
                    if (row) {
                        row.remove();
                    }

                    // 商品が全部消えたらメッセージ表示
                    const tbody = document.querySelector('#product-table-area tbody');
                    if (tbody && tbody.querySelectorAll('tr').length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="6" style="border: 1px solid #ccc; padding: 12px; text-align:center; color:#666;">
                                    該当する商品は見つかりませんでした。
                                </td>
                            </tr>
                        `;
                    }
                } else {
                    alert('削除に失敗しました。');
                }
            } catch (error) {
                alert('削除中にエラーが発生しました。');
                console.error(error);
            }
        });
    });
}

bindSortEvents();
bindDeleteEvents();
</script>
@endsection