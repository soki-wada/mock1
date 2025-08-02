<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('css/sanitize.css')}}">
    <link rel="stylesheet" href="{{asset('css/common.css')}}">
    @yield('css')
    <title>@yield('title')</title>
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <a href="/" class="header-logo-wrapper">
                <img src="{{asset('images/logo.png')}}" alt="" class="header-logo">
            </a>
            <div class="header-search-form-wrapper">
                <form action="/search" class="header-search-form" method="get">
                    <input type="text" class="header-search-form-input" placeholder="なにをお探しですか？" name="keyword" value="{{old('keyword', $keyword ?? '')}}">
                    <input type="hidden" name="tab" value="{{ request('tab', 'false') }}">
                </form>
            </div>
            <div class="header-button">
                <div class="header-button-wrapper">
                    @auth
                    <form action="/logout" class="header-form-logout" method="post">
                        @csrf
                        <button class="header-button-item" type="submit">ログアウト</button>
                    </form>
                    @else
                    <div class="header-button-wrapper">
                        <a href="/login" class="header-button-item">
                            ログイン
                        </a>
                    </div>
                    @endauth
                </div>
                <div class="header-button-wrapper">
                    <a href="/mypage" class="header-button-item">
                        マイページ
                    </a>
                </div>
                <div class="header-button-wrapper">
                    <a href="/sell" class="header-button-item white">
                        出品
                    </a>
                </div>
            </div>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
    @yield('js')
</body>

</html>