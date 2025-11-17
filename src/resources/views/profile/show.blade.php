{{-- 共通のレイアウト読み込み --}}
@extends('layouts.app')

@section('title', 'マイページ')

{{-- cssを読み込む --}}
@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')
    <div class="profile-page">
        <h2>プロフィール画面</h2>

            <div class="user-info">
                {{-- プロフィール画像 (ProfileControllerから$profileが渡されていることを前提) --}}
                @php
                    // $profile は ProfileControllerから渡されている前提
                    $imagePath = $profile->image_path ?? null; 
                    $placeholderClass = $imagePath ? '' : 'profile-placeholder';
                @endphp

                <img 
                    id="profile-preview" 
                    {{-- ★★★ 修正箇所：パスがない場合は必ず src を空("")にする ★★★ --}}
                    src="{{ $imagePath ? asset('storage/' . $imagePath) : '' }}" 
                    class="profile-image-frame {{ $placeholderClass }}"
                >
                {{-- ユーザー名 --}}
                <h1>{{ $user->name }}</h1>
                
                <a href="{{ route('profile.edit') }}" class="button-edit">プロフィールを編集</a>
            </div>

    {{-- ★★★ タブ部分：URLクエリで切り替えを制御 ★★★ --}}
        @php
            // URLクエリから 'tab' の値を取得。デフォルトは 'sell' (出品した商品)
            $tab = request('tab') ?? 'sell'; 
        @endphp

        <div class="tabs">
            {{-- 1. 出品した商品タブ --}}
            <a href="{{ route('mypage.show', ['tab' => 'sell']) }}" 
                class="tab-link {{ $tab === 'sell' ? 'active' : '' }}">
                出品した商品
            </a>
            {{-- 2. 購入した商品タブ --}}
            <a href="{{ route('mypage.show', ['tab' => 'buy']) }}" 
                class="tab-link {{ $tab === 'buy' ? 'active' : '' }}">
                購入した商品
            </a>
        </div>
        
        <div class="items-display-wrapper">

            {{-- 1. 出品した商品一覧 (ProfileControllerから $soldItems が渡される前提) --}}
            <div class="items-list items-list--sell" 
                style="{{ $tab === 'sell' ? 'display: grid;' : 'display: none;' }}"> {{-- CSSでグリッドまたは非表示を切り替え --}}
                
                @foreach ($soldItems as $item)
                        <a href="{{ route('item.show', ['id' => $item->id]) }}" class="item-card-link">
                            <div class="item-card">
                                {{-- 商品画像 (S3/ローカルパス混在対応) --}}
                                <div class="item-image-wrapper">
                                    <img src="
                                        @if (str_starts_with($item->image_path, 'http'))
                                            {{ $item->image_path }}
                                        @else
                                            {{ asset('storage/' . $item->image_path) }}
                                        @endif
                                    " alt="{{ $item->name }}" class="item-image">
                                </div>
                                {{-- 商品名 --}}
                                <p class="item-name">{{ $item->name }}</p>
                            </div>
                        </a>
                @endforeach
                
                @if ($soldItems->isEmpty())
                    <p class="no-items-message">出品した商品はありません。</p>
                @endif
            </div>
            
            
            {{-- 2. 購入した商品一覧 (ProfileControllerから $purchasedItems が渡される前提) --}}
            <div class="items-list items-list--buy" 
                    style="{{ $tab === 'buy' ? 'display: grid;' : 'display: none;' }}"> {{-- CSSでグリッドまたは非表示を切り替え --}}
                
                @foreach ($purchasedItems as $item)
                    <a href="{{ route('item.show', ['id' => $item->id]) }}" class="item-card-link">
                        <div class="item-card">
                            {{-- 商品画像 (S3/ローカルパス混在対応) --}}
                            <div class="item-image-wrapper">
                                <img src="
                                    @if (str_starts_with($item->image_path, 'http'))
                                        {{ $item->image_path }}
                                    @else
                                        {{ asset('storage/' . $item->image_path) }}
                                    @endif
                                " alt="{{ $item->name }}" class="item-image">
                            </div>
                            {{-- 商品名 --}}
                            <p class="item-name">{{ $item->name }}</p>
                        </div>
                    </a>
                @endforeach
                
                @if ($purchasedItems->isEmpty())
                    <p class="no-items-message">購入した商品はありません。</p>
                @endif
            </div>
        </div>
    </div>
@endsection