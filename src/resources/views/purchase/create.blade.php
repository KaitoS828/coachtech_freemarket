@extends('layouts.app')
@section('title', '購入手続き')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <h2>購入手続き</h2>

    {{-- ★★★ 修正1: 左上商品情報エリア (見本に合わせた配置) ★★★ --}}
    <div class="item-header-summary">
        {{-- 商品画像 --}}
        <div class="header-image-wrapper">
            <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}" class="header-item-image">
        </div>
        <div class="header-info-wrapper">
            <h3 class="header-item-name">{{ $item->name }}</h3>
            <p class="header-item-price">¥{{ number_format($item->price) }}</p>
        </div>
    </div>
    
    <hr class="header-divider">

    <div class="purchase-main-content">
        
        {{-- 左側: 支払い・配送情報 --}}
        <div class="payment-shipping-area">
            
            {{-- 1. 支払い方法 --}}
            <div class="section-box">
                <h3>支払い方法</h3>
                {{-- 💡 actionは PurchaseController@store へ --}}
                <form id="payment-form" action="{{ route('purchase.store', ['itemId' => $item->id]) }}" method="POST">
                    @csrf
                    
                    <select name="payment_method" required>
                        <option value="">選択してください</option>
                        @foreach ($paymentMethods as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('payment_method')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </form>
            </div>
            
            <hr class="section-divider">
            
            {{-- 2. 配送先情報 --}}
            <div class="section-box">
                <h3>配送先</h3>
                {{-- ユーザーのプロフィールから住所情報を表示 --}}
                <p>〒 {{ $profile->post_code ?? 'XXX-YYYY' }}</p>
                <p>ここに住所と建物が入ります ({{ $profile->address ?? '未設定' }}{{ $profile->building ? ' ' . $profile->building : '' }})</p>
                
                {{-- 配送先変更ボタン (FN024) --}}
                <a href="{{ route('purchase.address.edit', ['itemId' => $item->id]) }}" class="address-change-link">
                    変更する
                </a>
            </div>
            
            <hr class="section-divider">
        </div>

        {{-- 右側: サマリーと購入ボタン --}}
        <div class="summary-area">
            <table class="summary-table">
                <tr><th>商品代金</th><td>¥{{ number_format($item->price) }}</td></tr>
                <tr><th>支払い方法</th><td id="payment-display">選択されていません</td></tr>
                <tr class="total-row"><th>合計金額</th><td>¥{{ number_format($item->price) }}</td></tr>
            </table>

            {{-- 購入を確定するボタン --}}
            <button type="submit" form="payment-form" class="purchase-confirm-button">
                購入する
            </button>
        </div>
    </div>
</div>
@endsection