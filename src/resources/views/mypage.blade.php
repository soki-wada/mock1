@extends('layouts.app')

@section('title')
マイページ
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('css/mypage.css')}}">
@endsection

@section('content')
<div class="mypage-content">
    <div class="mypage-profile">
        <div class="mypage-profile-image-wrapper">
            <img src="{{asset('storage/images/' . $profile->image)}}" alt="" class="mypage-profile-image">
        </div>
        <h2 class="mypage-profile-username">
            {{$profile->username}}
        </h2>
        <div class="mypage-profile-button-wrapper">
            <a href="/mypage/profile" class="mypage-profile-button">
                プロフィールを編集
            </a>
        </div>
    </div>
    <div class="mypage-section-tab">
        <a href="/mypage?tab=sell" class="mypage-section-tab-item {{ $tab === 'sell' ? 'is-red' : 'is-black' }}">
            出品した商品
        </a>
        <a href="/mypage?tab=buy" class="mypage-section-tab-item {{ $tab === 'buy' ? 'is-red' : 'is-black' }}">
            購入した商品
        </a>
    </div>
    <div class="mypage-products sold {{ $tab === 'sell' ? 'is-visible' : 'is-hidden' }}">
        @foreach($soldProducts as $soldProduct)
        <div class="mypage-product-card-wrapper">
            <a href="/item/{{$soldProduct->id}}" class="mypage-product-card">
                <div class="mypage-product-card-image-wrapper">
                    <img src="{{asset('storage/images/' . $soldProduct->image)}}" alt="" class="mypage-product-card-image">
                </div>
                <div class="mypage-product-card-label">
                    <p class="mypage-product-card-label-name">
                        {{$soldProduct->name}}
                    </p>
                    @if($soldProduct->is_purchased == 1)
                    <p class="mypage-product-card-label-sold">
                        sold
                    </p>
                    @endif
                </div>
            </a>
        </div>
        @endforeach
    </div>
    <div class="mypage-products purchased {{ $tab === 'buy' ? 'is-visible' : 'is-hidden' }}">
        @foreach($purchasedProducts as $purchasedProduct)
        <div class="mypage-product-card-wrapper">
            <a href="/item/{{$purchasedProduct->product->id}}" class="mypage-product-card">
                <div class="mypage-product-card-image-wrapper">
                    <img src="{{asset('storage/images/' . $purchasedProduct->product->image)}}" alt="" class="mypage-product-card-image">
                </div>
                <div class="mypage-product-card-label">
                    <p class="mypage-product-card-label-name">
                        {{$purchasedProduct->product->name}}
                    </p>
                    @if($purchasedProduct->product->is_purchased == 1)
                    <p class="mypage-product-card-label-sold">
                        sold
                    </p>
                    @endif
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection