@extends('layouts.app')

@section('title')
プロフィール設定
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('css/profile.css')}}">
@endsection

@section('content')
<div class="profile-content">
    <form action="/mypage/profile" class="profile-form" method="post" enctype="multipart/form-data">
        @csrf
        <h2 class="profile-form-title">
            プロフィール設定
        </h2>
        <div class="profile-form-image-setting">
            @if(isset($profile) && $profile->image)
            <input type="hidden" name="old_image" value="{{$profile->image}}">
            @endif
            <div class="profile-form-image-wrapper">
                <img src="{{ isset($profile) && $profile->image ? asset('storage/images/' . $profile->image) : '' }}" alt="" class="profile-form-image">
            </div>
            <div class="profile-form-file-image">
                <label for="file-upload" class="profile-form-file-image-label">
                    画像を選択する
                </label>
                <input type="file" class="profile-form-file-image-input" id="file-upload" name="image">
            </div>
        </div>
        @error('image')
        <p class="error">
            {{$message}}
        </p>
        @enderror
        <div class="profile-form-section">
            <p class="profile-form-section-title">
                ユーザー名
            </p>
            <div class="profile-form-input-wrapper">
                <input type="text" class="profile-form-input" name="username" value="{{old('username', $profile->username ?? '')}}">
            </div>
            @error('username')
            <p class="error">
                {{$message}}
            </p>
            @enderror
        </div>
        <div class="profile-form-section">
            <p class="profile-form-section-title">
                郵便番号
            </p>
            <div class="profile-form-input-wrapper">
                <input type="text" class="profile-form-input" name="postal_code" value="{{old('postal_code', $profile->postal_code ?? '')}}">
            </div>
            @error('postal_code')
            <p class="error">
                {{$message}}
            </p>
            @enderror
        </div>
        <div class="profile-form-section">
            <p class="profile-form-section-title">
                住所
            </p>
            <div class="profile-form-input-wrapper">
                <input type="text" class="profile-form-input" name="address" value="{{old('address', $profile->address ?? '')}}">
            </div>
            @error('address')
            <p class="error">
                {{$message}}
            </p>
            @enderror
        </div>
        <div class="profile-form-section">
            <p class="profile-form-section-title">
                建物名
            </p>
            <div class="profile-form-input-wrapper">
                <input type="text" class="profile-form-input" name="building" value="{{old('building', $profile->building ?? '')}}">
            </div>
        </div>
        <div class="profile-form-button-wrapper">
            <button class="profile-form-button" type="submit">
                更新する
            </button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file-upload');
        const previewImage = document.querySelector('.profile-form-image');

        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                };

                reader.readAsDataURL(file);
            } else {
                previewImage.src = '';
            }
        });
    });
</script>
@endsection