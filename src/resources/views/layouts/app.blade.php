{{-- ヘッダー --}}

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'coachtechフリマ')</title>

    {{-- フォント読み込み (Noto Sans JPに変更) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">

    {{-- CSS読み込み --}}
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    
    {{-- ページ専用のCSS読み込み --}}
    @yield('css')
    
</head>
{{-- ページごとにbodyのクラスを変更できるようにします --}}
<body class="@yield('body-class', 'bg-white')">
    <header class="header">
        <div class="header__logo">
            <a href="/"><img class="logo-img" src="{{ asset('img/logo_coachtech.png') }}" alt="COACHTECH"></a>
        </div>

        @auth
            {{-- ログイン済 --}}
            {{-- 検索窓の表示 --}}
            <div class="header__search-form">
                <form action="{{ route('item.index') }}" method="GET">
                    <input type="text" name="keyword" class="header__search-input" placeholder="なにをお探しですか？">
                </form>
            </div>
            <nav class="header__nav">
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="header__nav-button">ログアウト</button>
                </form>
                <a href="/mypage">マイページ</a>
                <a href="/sell" class="header__nav-button">出品</a>
            </nav>
        @endauth


        @guest
            {{-- ゲスト（未ログイン） --}}
            {{-- 検索窓の表示 --}}
            <div class="header__search-form">
                <form action="{{ route('item.index') }}" method="GET">
                    <input type="text" name="keyword" ... value="{{ request('keyword') }}" class="header__search-input">
                </form>
            </div>
            </form>
            <nav class="header__nav">
                <a href="/login">ログイン</a>
                <a href="/mypage">マイページ</a>
                <a href="/sell" class="header__nav-button">出品</a>
            </nav>
        @endguest

        
    </header>

    <main class="main">
        @yield('content')
    </main>
</body>
</html>