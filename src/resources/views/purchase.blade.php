@extends('layouts.app')

@section('title')
商品購入
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('css/purchase.css')}}">
@endsection

@section('content')
<div class="purchase-content">
    <form action="/purchase/checkout" class="purchase-form" method="post">
        @csrf
        <input type="hidden" name="product_id" value="{{$product->id}}">
        <div class="purchase-form-wide">
            <div class="purchase-form-section-product">
                <div class="purchase-form-product-image-wrapper">
                    <img src="{{asset('storage/images/' . $product->image)}}" alt="" class="purchase-form-product-image">
                </div>
                <div class="purchase-form-product-label">
                    <p class="purchase-form-product-label-name">
                        {{$product->name}}
                    </p>
                    <p class="purchase-form-product-label-price">
                        ¥ {{number_format($product->price)}}
                    </p>
                    <input type="hidden" name="price" value="{{$product->price}}">
                </div>
            </div>
            <div class="purchase-form-section">
                <p class="purchase-form-section-title">
                    支払い方法
                </p>
                <div class="purchase-form-select-wrapper">
                    <select name="payment" id="" class="purchase-form-select">
                        <option class="purchase-form-select-option-first" value="">選択してください</option>
                        <option class="purchase-form-select-option" value="0" {{old('payment') === '0' ? 'selected' : '' }}>
                            コンビニ払い
                        </option>
                        <option class="purchase-form-select-option" value="1" {{old('payment') === '1' ? 'selected' : '' }}>
                            カード支払い
                        </option>
                    </select>
                </div>
                @error('payment')
                <p class="error">
                    {{$message}}
                </p>
                @enderror
            </div>
            <div class="purchase-form-section">
                <div class="purchase-form-header">
                    <p class="purchase-form-section-title">
                        配送先
                    </p>
                    <a href="/purchase/address/{{$product->id}}" class="purchase-form-address-update">
                        変更する
                    </a>
                </div>
                @if(session()->has('purchase_address'))
                <p class="purchase-form-address">
                    〒 {{$postal_code}}
                </p>
                <p class="purchase-form-address">
                    {{$address}} {{$building}}
                </p>
                <input type="hidden" name="postal_code" value="{{$postal_code}}">
                <input type="hidden" name="address" value="{{$address}} ">
                <input type="hidden" name="building" value="{{$building}}">
                @else
                <p class="purchase-form-address">
                    〒 {{$profile->postal_code}}
                </p>
                <p class="purchase-form-address">
                    {{$profile->address}} {{$profile->building}}
                </p>
                <input type="hidden" name="postal_code" value="{{$profile->postal_code}}">
                <input type="hidden" name="address" value="{{$profile->address}} ">
                <input type="hidden" name="building" value="{{$profile->building}}">
                @endif
            </div>
        </div>
        <div class="purchase-form-narrow">
            <div class="purchase-form-table-wrapper">
                <table class="purchase-form-table">
                    <tr class="purchase-form-table-row">
                        <th class="purchase-form-table-header">
                            商品代金
                        </th>
                        <td class="purchase-form-table-description">
                            ¥ {{number_format($product->price)}}
                        </td>
                    </tr>
                    <tr class="purchase-form-table-row">
                        <th class="purchase-form-table-header">
                            支払方法
                        </th>
                        <td id="selected-payment-method" class="purchase-form-table-description">

                        </td>
                    </tr>
                </table>
            </div>
            <div class="purchase-form-button-wrapper">
                <button class="purchase-form-button" type="submit">
                    購入する
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    const select = document.querySelector('select[name="payment"]');
    const paymentTd = document.getElementById('selected-payment-method');

    const labels = {
        '0': 'コンビニ払い',
        '1': 'カード支払い'
    };

    // 初期状態の反映（old値あり）
    const initial = select.value;
    if (initial && labels[initial]) {
        paymentTd.textContent = labels[initial];
    }

    // 選択変更時の反映
    select.addEventListener('change', function() {
        const selected = this.value;
        paymentTd.textContent = labels[selected] ?? '';
    });
</script>
@endsection