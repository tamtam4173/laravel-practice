@php
    $sort = $sort ?? 'id';
    $direction = $direction ?? 'desc';

    function sortIcon($column, $sort, $direction) {
        if ($sort !== $column) return '';
        return $direction === 'asc' ? ' ▲' : ' ▼';
    }
@endphp

<table style="width: 100%; border-collapse: collapse; border: 1px solid #ccc;">
    <thead>
        <tr style="background-color: #f7f7f7;">
            <th class="sortable-header" data-sort="id" style="border: 1px solid #ccc; padding: 8px; cursor:pointer;">
                ID{!! sortIcon('id', $sort, $direction) !!}
            </th>
            <th class="sortable-header" data-sort="product_name" style="border: 1px solid #ccc; padding: 8px; cursor:pointer;">
                商品名{!! sortIcon('product_name', $sort, $direction) !!}
            </th>
            <th style="border: 1px solid #ccc; padding: 8px;">会社名</th>
            <th class="sortable-header" data-sort="price" style="border: 1px solid #ccc; padding: 8px; cursor:pointer;">
                価格{!! sortIcon('price', $sort, $direction) !!}
            </th>
            <th class="sortable-header" data-sort="stock" style="border: 1px solid #ccc; padding: 8px; cursor:pointer;">
                在庫数{!! sortIcon('stock', $sort, $direction) !!}
            </th>
            <th style="border: 1px solid #ccc; padding: 8px;">操作</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($products as $p)
            <tr id="product-row-{{ $p->id }}" style="text-align: center;">
                <td style="border: 1px solid #ccc; padding: 8px;">{{ $p->id }}</td>
                <td style="border: 1px solid #ccc; padding: 8px;">{{ $p->product_name }}</td>
                <td style="border: 1px solid #ccc; padding: 8px;">
                    {{ optional($p->company)->company_name ?? '（未設定）' }}
                </td>
                <td style="border: 1px solid #ccc; padding: 8px;">¥{{ number_format($p->price) }}</td>
                <td style="border: 1px solid #ccc; padding: 8px;">{{ $p->stock }}</td>
                <td style="border: 1px solid #ccc; padding: 8px;">
                    <a href="{{ route('products.show', $p->id) }}"
                       style="background-color: deepskyblue; color: white; padding: 4px 8px; border-radius: 4px; margin-right: 4px; text-decoration:none;">
                        詳細
                    </a>

                    <form action="{{ route('products.destroy', $p->id) }}"
                          method="POST"
                          class="delete-form"
                          data-id="{{ $p->id }}"
                          style="display:inline;">
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