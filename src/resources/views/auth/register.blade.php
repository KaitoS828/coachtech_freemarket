@extends('layouts.app') 

@section('title', '会員登録')

{{-- 灰色背景を適用 --}}
@section('body-class', 'bg-gray')

{{-- register.cssを読み込む --}}
@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register-container">
    
    <h2 class="register__heading">会員登録</h2>

    {{-- バリデーションエラー表示 --}}
    @if ($errors->any())
        <div class="form-errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="form" action="/register" method="post">
        @csrf 
        
        <div class="form__group">
            <label for="name" class="form__label">ユーザー名</label>
            <input type="text" id="name" name="name" class="form__input" value="{{ old('name') }}" required>
        </div>

        <div class="form__group">
            <label for="email" class="form__label">メールアドレス</label>
            <input type="email" id="email" name="email" class="form__input" value="{{ old('email') }}" required>
        </div>

        <div class="form__group">
            <label for="password" class="form__label">パスワード</label>
            <input type="password" id="password" name="password" class="form__input" required>
        </div>

        <div class="form__group">
            <label for="password_confirmation" class="form__label">確認用パスワード</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form__input" required>
        </div>

        <div class="form__group">
            <button type="submit" class="form__button">登録する</button>
        </div>
    </form>

    <div class="login-link">
        <a href="/login">ログインはこちら</a>
    </div>

</div>
@endsection