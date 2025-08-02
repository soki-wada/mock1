@extends('layouts.app')

@section('title')
商品の出品
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('css/sell.css')}}">
@endsection

@section('content')
<div class="sell-content">
    <form action="/sell" class="sell-form" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="is_purchased" value="0">
        <h2 class="sell-form-title">
            商品の出品
        </h2>
        <div class="sell-form-section">
            <p class="sell-form-section-title">
                商品画像
            </p>
            <div class="sell-form-image-wrapper">
                <img src="" alt="a" class="sell-form-image">
                <div class="sell-form-file-image">
                    <label for="file-upload" class="sell-form-file-image-label">
                        画像を選択する
                    </label>
                    <input type="file" class="sell-form-file-image-input" id="file-upload" name="image">
                </div>
            </div>
            @error('image')
            <p class="error">
                {{$message}}
            </p>
            @enderror
        </div>
        <h3 class="sell-form-section-title-gray">
            商品の詳細
        </h3>
        <div class="sell-form-section">
            <p class="sell-form-section-title">
                カテゴリー
            </p>
            <div class="sell-form-category">
                @foreach($categories as $category)
                <input type="checkbox" class="sell-form-category-input" id="{{$category->id}}" name="categories[]" value="{{$category->id}}" {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                <label for="{{$category->id}}" class="sell-form-category-label">
                    {{$category->content}}
                </label>
                @endforeach
            </div>
            @error('categories')
            <p class="error">
                {{$message}}
            </p>
            @enderror
        </div>
        <div class="sell-form-section">
            <p class="sell-form-section-title">
                商品の状態
            </p>
            <div class="sell-form-select-wrapper">
                <select name="condition_id" id="" class="sell-form-select">
                    <option class="sell-form-select-option-first" value="">選択してください</option>
                    @foreach($conditions as $condition)
                    <option class="sell-form-select-option" value="{{$condition->id}}" {{old('condition') == $condition->id ? 'selected' : '' }}>
                        {{$condition->content}}
                    </option>
                    @endforeach
                </select>
            </div>
            @error('condition_id')
            <p class="error">
                {{$message}}
            </p>
            @enderror
        </div>
        <h3 class="sell-form-section-title-gray">
            商品名と説明
        </h3>
        <div class="sell-form-section">
            <p class="sell-form-section-title">
                商品名
            </p>
            <div class="sell-form-section-input-wrapper">
                <input type="text" class="sell-form-section-input" name="name" value="{{old('name')}}">
            </div>
            @error('name')
            <p class="error">
                {{$message}}
            </p>
            @enderror
        </div>
        <div class="sell-form-section">
            <p class="sell-form-section-title">
                ブランド名
            </p>
            <div class="sell-form-section-input-wrapper">
                <input type="text" class="sell-form-section-input" name="brand" value="{{old('brand')}}">
            </div>
        </div>
        <div class="sell-form-section">
            <p class="sell-form-section-title">
                商品の説明
            </p>
            <div class="sell-form-section-textarea-wrapper">
                <textarea name="description" id="" class="sell-form-section-textarea">{{old('description')}}</textarea>
            </div>
            @error('description')
            <p class="error">
                {{$message}}
            </p>
            @enderror
        </div>
        <div class="sell-form-section">
            <p class="sell-form-section-title">
                販売価格
            </p>
            <div class="sell-form-section-input-wrapper">
                <input type="text" class="sell-form-section-input" name="price" value="{{old('price')}}">
            </div>
            @error('price')
            <p class="error">
                {{$message}}
            </p>
            @enderror
        </div>
        <div class="sell-form-button-wrapper">
            <button class="sell-form-button" type="submit">
                出品する
            </button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file-upload');
        const previewImage = document.querySelector('.sell-form-image');

        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                };

                reader.readAsDataURL(file);
            } else {
                previewImage.src = '';
                previewImage.style.display = 'none';
            }
        });
    });
</script>
@endsection