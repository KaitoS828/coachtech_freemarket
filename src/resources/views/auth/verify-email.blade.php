@extends('layouts.app')
@section('title', 'メール認証誘導画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verification.css') }}">
@endsection

@section('content')
<div class="verification-container">
    <div class="verification-box">
        
        <p class="verification-message">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>
        
        {{-- 1. 認証リンクへ遷移するボタン (メールを確認させる動線) --}}
        {{-- 💡 開発環境ではMailHog UIへ誘導 --}}
        <a href="http://localhost:8025" class="btn-verify-guide" target="_blank">
            認証はこちらから
        </a>
        
        {{-- 2. 認証メール再送ボタン (FN013) --}}
        {{-- 💡 Fortifyの 'verification.send' ルートにPOSTリクエストを送る --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-resend-mail">
                認証メールを再送する
            </button>
        </form>

    </div>
</div>
@endsection