{{-- 共通のレイアウト読み込み --}}
@extends('layouts.app')
@section('title', $item->name)

{{-- cssを読み込む --}}
@section('css')
<link rel="stylesheet" href="{{ asset('css/item_detail.css') }}">
@endsection

@section('content')
    <div class="item-detail-container">

        <div class="item-main-area">
            {{-- 左側: 商品画像 (見本通りの大きな枠) --}}
                <div class="item-image-wrapper">
                    <img src="
                    {{-- ★★★ 画像パスの安全な処理 (S3/ローカル混在対応) ★★★ --}}
                    @if (str_starts_with($item->image_path, 'http'))
                        {{ $item->image_path }}
                    @else
                        {{ asset('storage/' . $item->image_path) }}
                    @endif
                    " alt="{{ $item->name }}" class="item-image">
                </div>

            {{-- 右側: 詳細情報 --}}
            <div class="item-info">
                <h1>{{ $item->name }}</h1>
                <p class="brand-name">{{ $item->brand_name }}</p>
                <p class="price">¥{{ number_format($item->price) }} (税込)</p>

                <div class="interaction-stats">
                    
                    @auth
                        {{-- 💡 ログインユーザーがいいね済みか判定 --}}
                        @php
                            // $item->likes は Like コレクション。その中に現在のユーザーのIDが存在するかで判定。
                            $isLiked = Auth::user()->likes->contains('item_id', $item->id); 
                        @endphp
                        
                        {{-- ★いいねボタンのフォーム (POSTでLikeController@toggleへ) ★ --}}
                        <form action="{{ route('like.toggle', ['itemId' => $item->id]) }}" method="POST" class="like-form">
                            @csrf
                            
                            <button type="submit" class="like-button {{ $isLiked ? 'liked' : '' }}">
                                {{-- 💡 いいね済みなら塗りつぶし (CSSで対応) --}}
                                <span class="heart-icon">♥</span>
                                {{-- いいね数 --}}
                                {{ $item->likes->count() }}
                            </button>
                        </form>
                    @else
                        {{-- 非ログイン時の表示 --}}
                        <span class="likes-count">
                            <span class="heart-icon">♥</span>
                            {{ $item->likes->count() }}
                        </span>
                    @endauth

                    {{-- ★★★ コメント数とアイコン ★★★ --}}
                    <span class="comments-count comment-stats">
                        <span class="comment-icon">💬</span>
                        <span class="comment-number">{{ $item->comments->count() }}</span>
                    </span>
                </div>


                {{-- ★★★ 購入ボタンの表示切り替えロジック (SOLD OUT) ★★★ --}}
                {{-- 💡 Itemモデルに purchase() リレーションが定義されていることを前提とする --}}
                @if ($item->purchase)
                    <button class="buy-button sold-out-button" disabled> SOLD OUT (購入済み) </button>
                @else
                    {{-- ★★★ 修正: ルート名を 'item.purchase.create'、キーを 'id' に統一 ★★★ --}}
                    <a href="{{ route('item.purchase.create', ['id' => $item->id]) }}" class="buy-button active-button">
                        購入手続きへ
                    </a>
                @endif
                
                <hr class="info-divider">

                <h3>商品説明</h3>
                <div class="description-body">
                    <p>{{ $item->description }}</p>
                    <p>商品の状態は **{{ $item->condition }}** です。</p>
                </div>
                
                <hr class="info-divider">

                <h3>商品の情報</h3>
                <div class="item-specs">
                    <div class="spec-row">
                        <p class="spec-label">カテゴリ</p>
                        <p class="spec-value">
                            @foreach ($item->categories as $category)
                                <span class="tag tag-category">{{ $category->name }}</span>
                            @endforeach
                        </p>
                    </div>
                    <div class="spec-row">
                        <p class="spec-label">商品の状態</p>
                        {{-- データベースに保存された 'condition' の値を表示 --}}
                        <p class="spec-value tag tag-condition">{{ $item->condition }}</p> 
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="comments-divider">
        
        {{-- コメントセクション --}}
    <div class="comments-section">
        <h3>コメント({{ $item->comments->count() }})</h3>
        
        {{-- 既存のコメント一覧 --}}
        <div class="comment-list">
            @foreach ($item->comments as $comment)
                <div class="comment-item">
                    {{-- コメントしたユーザーのアイコンと名前 (見本) --}}
                    <div class="comment-user-info">
                        <div class="user-icon"></div>
                        <span class="username">{{ $comment->user->name ?? '名無しユーザー' }}</span>
                    </div>
                    <p class="comment-body">{{ $comment->comment }}</p>
                </div>
            @endforeach
        </div>

        {{-- コメント投稿フォーム: ログイン時のみ表示 --}}
        @auth
            <div class="comment-form-area">
                <h4>商品へのコメント</h4>
                {{-- 💡 コメント保存ルートと隠しフィールドを正しく設定 --}}
                <form action="{{ route('comment.store') }}" method="POST">
                    @csrf
                    {{-- ★商品IDを隠しフィールドで送る --}}
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                    
                    <textarea name="comment" rows="5" placeholder="こちらにコメントを入力します">{{ old('comment') }}</textarea>
                    
                    {{-- ★ バリデーションエラー表示 --}}
                    @error('comment')
                        <p class="error-message">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="comment-submit-button">コメントを送信する</button>
                </form>
            </div>
        @else
            <p class="login-prompt">コメントするには<a href="{{ route('login') }}">ログイン</a>してください。</p>
        @endauth
    </div>
    </div>
@endsection