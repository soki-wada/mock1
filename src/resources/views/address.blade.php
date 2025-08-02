@extends('layouts.app')

@section('title')
送付先住所変更
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('css/address.css')}}">
@endsection

@section('content')
<div class="address-content">
    <div class="address-form-wrapper">
        <form action="/purchase/address/{{$item_id}}" method="post" class="address-form">
            @csrf
            <h2 class="address-form-title">
                住所の変更
            </h2>
            <div class="address-form-item">
                <p class="address-form-item-title">
                    郵便番号
                </p>
                <input type="text" class="address-form-item-input" name="postal_code" value="{{old('postal_code', $profile->postal_code)}}">
            </div>
            @error('postal_code')
            <p class="error">
                {{$message}}
            </p>
            @enderror
            <div class="address-form-item">
                <p class="address-form-item-title">住所</p>
                <input type="text" class="address-form-item-input" name="address" value="{{ old('address', $profile->address) }}">
            </div>
            @error('address')
            <p class="error">
                {{$message}}
            </p>
            @enderror
            <div class="address-form-item">
                <p class="address-form-item-title">建物名</p>
                <input type="text" class="address-form-item-input" name="building" value="{{ old('building', $profile->building) }}">
            </div>
            <div class="address-form-button-wrapper">
                <button class="address-form-button" type="submit">
                    更新する
                </button>
            </div>
        </form>
    </div>
</div>
@endsection