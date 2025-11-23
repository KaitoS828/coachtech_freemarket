@extends('layouts.app') 

@section('title', 'トップページ')

{{-- index.cssを読み込む --}}
@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="item-list-wrapper">
    
    {{-- タブの切り替えセクション --}}
    <div class="item-tabs">
        
        {{-- 1. おすすめ（全商品）タブ --}}
        <a href="{{ route('item.index') }}" 
            class="tab-link {{ !request('tab') || request('tab') === 'recommend' ? 'active' : '' }}">
            おすすめ
        </a>
        
        {{-- 2. マイリスト（いいねした商品）タブ - ログイン時のみ表示 --}}
        @auth
            {{-- ItemController の index メソッドで $request->tab === 'mylist' を処理します --}}
            <a href="{{ route('item.index', ['tab' => 'mylist']) }}" 
                class="tab-link {{ request('tab') === 'mylist' ? 'active' : '' }}">
                マイリスト
            </a>
        @endauth
        
        {{-- ログイン前のマイリスト（非アクティブ表示）--}}
        @guest
            <span class="tab-link disabled">マイリスト</span>
        @endguest
    </div>
    <hr class="tabs-divider">
    
    <h2>商品一覧</h2>

    {{-- 商品リストの表示 --}}
    <div class="item-container">
        {{-- 商品がない場合のメッセージ --}}
        @if ($items->isEmpty())
            <p class="no-items">表示できる商品がありません。</p>
        @endif
        
        @foreach ($items as $item)
            {{--商品詳細画面へのリンク --}}
            <a href="{{ route('item.show', ['id' => $item->id]) }}" class="item-card-link">
                <div class="item-card">
                    
                    {{--商品画像表示ロジック (S3/ローカルパス対応) --}}
                    <img src="
                        @if (str_starts_with($item->image_path, 'http'))
                            {{ $item->image_path }}
                        @else
                            {{ asset('storage/' . $item->image_path) }}
                        @endif
                    " alt="{{ $item->name }}" class="item-card__image">
                    
                    <div class="item-card__content">
                        <h4 class="item-card__title">{{ $item->name }}</h4>
                        <p class="item-card__price">¥{{ number_format($item->price) }}</p>

                        {{-- カテゴリをタグとして表示--}}
                        <div class="item-card__categories">
                            @if($item->categories)
                                @foreach ($item->categories as $category)
                                    <span class="item-category-tag">#{{ $category->name }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection