@extends('layouts.app')
@section('title', '購入手続き')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <h2>購入手続き</h2>

    {{-- 左上商品情報エリア --}}
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
            
            {{-- 1つのフォームにまとめる --}}
            <form id="payment-form" action="{{ route('purchase.store', $item->id) }}" method="POST">
                @csrf
                
                {{-- 1. 支払い方法 --}}
                <div class="section-box">
                    <h3>支払い方法</h3>
                    
                    <select name="payment_method" id="payment-method-select" required>
                        <option value="">選択してください</option>
                        @foreach ($paymentMethods as $key => $label)
                            <option value="{{ $key }}" {{ old('payment_method') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    
                    @error('payment_method')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                
                <hr class="section-divider">
                
                {{-- 2. 配送先情報 --}}
                <div class="section-box">
                    <h3>配送先</h3>
                    {{-- ユーザーのプロフィールから住所情報を表示 --}}
                    <p>〒 {{ $profile->post_code ?? 'XXX-YYYY' }}</p>
                    <p>{{ $profile->address ?? '未設定' }}{{ $profile->building ? ' ' . $profile->building : '' }}</p>
                    
                    {{-- 配送先変更ボタン --}}
                    <a href="{{ route('purchase.address.edit', ['id' => $item->id]) }}" class="address-change-link">
                        変更する
                    </a>
                </div>
                
                <hr class="section-divider">
            </form>
        </div>

        {{-- 右側: サマリーと購入ボタン --}}
        <div class="summary-area">
            <table class="summary-table">
                {{-- 商品代金 --}}
                <tr>
                    <th>商品代金</th>
                    <td>¥{{ number_format($item->price) }}</td>
                </tr>
                
                {{-- 支払い手数料 --}}
                <tr>
                    <th>支払い手数料</th>
                    <td>¥0</td>
                </tr>
                
                {{-- 支払い方法の表示 --}}
                <tr>
                    <th>支払い方法</th>
                    <td id="payment-display">選択してください</td>
                </tr>
                
                {{-- 合計金額 --}}
                <tr class="total-row">
                    <th>合計金額</th>
                    <td>¥{{ number_format($item->price) }}</td>
                </tr>
            </table>

            {{-- 購入を確定するボタン --}}
            {{-- form属性でpayment-formを指定 --}}
            <button type="submit" form="payment-form" class="purchase-button">
                購入を確定する
            </button>
        </div>
    </div>
</div>

{{-- JavaScript: 支払い方法の選択を右側に反映 --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentSelect = document.getElementById('payment-method-select');
    const paymentDisplay = document.getElementById('payment-display');
    
    if (paymentSelect && paymentDisplay) {
        paymentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            paymentDisplay.textContent = selectedOption.text || '選択してください';
        });
        
        // 初期値の設定（old値がある場合）
        if (paymentSelect.value) {
            const selectedOption = paymentSelect.options[paymentSelect.selectedIndex];
            paymentDisplay.textContent = selectedOption.text;
        }
    }
});
</script>
@endsection