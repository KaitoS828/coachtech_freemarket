{{-- 共通のレイアウト読み込み --}}
@extends('layouts.app') 
@section('title', 'ログイン')

{{-- 灰色背景を適用 --}}
@section('body-class', 'bg-gray')

{{-- login.cssを読み込む --}}
@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-container">
    
    <h2 class="login__heading">ログイン</h2>

    {{-- ログイン失敗時のエラーメッセージ表示 --}}
    @if ($errors->any())
        <div class="form-errors" >
            <ul>
                {{-- Fortifyが提供するログイン失敗メッセージ --}}
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="form" action="/login" method="post">
        @csrf 
        
        <div class="form__group">
            <label for="email" class="form__label">メールアドレス</label>
            {{-- name属性は 'email' で固定 --}}
            <input type="email" id="email" name="email" class="form__input" value="{{ old('email') }}" required>
        </div>

        <div class="form__group">
            <label for="password" class="form__label">パスワード</label>
            {{-- name属性は 'password' で固定 --}}
            <input type="password" id="password" name="password" class="form__input" required>
        </div>

        <div class="form__group">
            <button type="submit" class="form__button">ログイン</button>
        </div>
    </form>

    <div class="register-link">
        <a href="/register">会員登録の方はこちら</a>
    </div>

</div>
@endsection
