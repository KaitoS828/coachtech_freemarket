@extends('layouts.app')
@section('title', 'メッセージ')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/message.css') }}">
@endsection

@section('content')
    <div class="message-container">

        {{-- 左サイドバー --}}
        <aside class="sidebar">
            <h3>その他の取引</h3>
            @forelse($ongoingpurchase as $otherPurchase)
                <a href="{{ route('message.show', ['purchaseId' => $otherPurchase->id]) }}" class="sidebar-item">
                    {{ $otherPurchase->item->name }}
                </a>
            @empty
                <p style="color: #999; font-size: 12px;">他の取引はありません</p>
            @endforelse
        </aside>

        {{-- 右メインエリア --}}
        <main class="message-main">

            {{-- ヘッダー --}}
            <div class="message-header">
                <div class="message-header-left">
                    {{-- 取引相手のプロフィール画像 --}}
                    @if ($partner->profile && $partner->profile->image_path)
                        <img src="{{ asset('storage/' . $partner->profile->image_path) }}" alt="{{ $partner->name }}"
                            class="partner-avatar">
                    @else
                        <div class="partner-avatar"></div>
                    @endif
                    <h2>「{{ $partner->name }}」さんとの取引画面</h2>
                </div>

                {{-- 購入者 かつ ステータスが進行中(0)の場合のみ完了ボタンを表示 --}}
                @if ($purchase->user_id === $user->id && $purchase->status === 0)
                    <button type="button" class="complete-button" onclick="openModal()">取引を終了する</button>
                @endif

            </div>

            {{-- 商品情報 --}}
            <div class="item-card">
                @if (str_starts_with($item->image_path, 'http'))
                    <img src="{{ $item->image_path }}" alt="{{ $item->name }}">
                @else
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                @endif
                <div class="item-info">
                    <h3>{{ $item->name }}</h3>
                    <p>¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            {{-- メッセージ一覧 --}}
            <div class="messages-area">
                @forelse ($messages as $message)
                    @if ($message->user_id === $user->id)
                        {{-- 自分のメッセージ項目 --}}
                        <div class="message-item me">
                            {{-- ユーザー名とアイコンの行 --}}
                            <div class="message-header-row">
                                <div class="message-user-name">{{ $message->user->name }}</div>
                                @if ($message->user->profile && $message->user->profile->image_path)
                                    <img src="{{ asset('storage/' . $message->user->profile->image_path) }}"
                                        alt="{{ $message->user->name }}" class="message-avatar">
                                @else
                                    <div class="message-avatar"></div>
                                @endif
                            </div>
                            {{-- メッセージコンテンツ --}}
                            <div class="message-content">
                                <div class="message-bubble">{{ $message->content }}</div>

                                {{-- 画像アップロード機能 --}}
                                @if ($message->image_path)
                                    <img src="{{ asset('storage/' . $message->image_path) }}"
                                        alt="{{ $message->content }}" class="message-image">
                                @endif

                                <div class="message-meta">
                                    <div class="message-actions">
                                        <a
                                            href="{{ route('message.edit', ['purchaseId' => $message->purchase_id, 'messageId' => $message->id]) }}">編集</a>
                                        <form
                                            action="{{ route('message.destroy', ['purchaseId' => $message->purchase_id, 'messageId' => $message->id]) }}"
                                            method="POST" style="display: inline;"
                                            onsubmit="return confirm('このメッセージを削除しますか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-link">削除</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- 相手のメッセージ項目 --}}
                        <div class="message-item other">
                            {{-- ユーザー名とアイコンの行 --}}
                            <div class="message-header-row">
                                @if ($message->user->profile && $message->user->profile->image_path)
                                    <img src="{{ asset('storage/' . $message->user->profile->image_path) }}"
                                        alt="{{ $message->user->name }}" class="message-avatar">
                                @else
                                    <div class="message-avatar"></div>
                                @endif
                                <div class="message-user-name">{{ $message->user->name }}</div>
                            </div>
                            {{-- メッセージコンテンツ --}}
                            <div class="message-content">
                                <div class="message-bubble">{{ $message->content }}</div>

                                {{-- 画像アップロード機能 --}}
                                @if ($message->image_path)
                                    <img src="{{ asset('storage/' . $message->image_path) }}"
                                        alt="{{ $message->content }}" class="message-image">
                                @endif
                            </div>
                        </div>
                    @endif
                @empty
                    <p style="text-align: center; color: #999;">メッセージはまだありません</p>
                @endforelse
            </div>

            {{-- メッセージ送信フォーム --}}
            <div class="message-form">
                {{-- バリデーションエラー表示（フォームの外側） --}}
                @if ($errors->any())
                    <div class="validation-errors" style="margin-bottom: 10px;">
                        @foreach ($errors->all() as $error)
                            <p style="color: #FF5555; font-size: 14px; margin: 5px 0;">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('message.store', ['purchaseId' => $purchase->id]) }}" method="POST"
                    enctype="multipart/form-data" novalidate>
                    @csrf
                    {{-- FN009: 入力情報保持機能 --}}
                    <textarea id="message-content" name="content" placeholder="取引メッセージを記入してください">{{ old('content') }}</textarea>
                    <label for="image-upload" class="image-upload-button">画像を追加</label>
                    <input type="file" id="image-upload" name="image" accept="image/*" style="display: none;">
                    <button type="submit" class="send-button"></button>
                </form>
            </div>

        </main>
    </div>

    {{-- 評価モーダル --}}
    <div id="review-modal" class="modal">
        <div class="modal-content">
            <h3>取引が完了しました。</h3>
            <p>今回の取引相手はどうでしたか？</p>

            <form action="{{ route('review.store', ['purchaseId' => $purchase->id]) }}" method="POST">
                @csrf
                {{-- 星評価 --}}
                <div class="rating-group">
                    <input type="radio" id="star5" name="rate" value="5" required /><label
                        for="star5">★</label>
                    <input type="radio" id="star4" name="rate" value="4" /><label for="star4">★</label>
                    <input type="radio" id="star3" name="rate" value="3" /><label for="star3">★</label>
                    <input type="radio" id="star2" name="rate" value="2" /><label for="star2">★</label>
                    <input type="radio" id="star1" name="rate" value="1" /><label
                        for="star1">★</label>
                </div>



                <div class="modal-actions">
                    <button type="submit" class="btn-primary">送信する</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('review-modal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('review-modal').style.display = 'none';
        }

        // 外部クリックで閉じる
        window.onclick = function(event) {
            if (event.target == document.getElementById('review-modal')) {
                closeModal();
            }
        }

        // 出品者 かつ ステータスが1(購入者が評価済み)の場合、ページ読み込み時にモーダルを開く
        @if ($purchase->user_id !== $user->id && $purchase->status === 1)
            window.onload = function() {
                openModal();
            };
        @endif

        // FN009: 入力情報保持機能（localStorage使用）
        document.addEventListener('DOMContentLoaded', function() {
            const purchaseId = '{{ $purchase->id }}';
            const storageKey = `message_draft_${purchaseId}`;
            const textarea = document.getElementById('message-content');
            const form = document.querySelector('.message-form form');

            // ページ読み込み時に復元（old()の値がない場合のみ）
            const savedContent = localStorage.getItem(storageKey);
            if (savedContent && !textarea.value.trim()) {
                textarea.value = savedContent;
            }

            // 入力中に保存
            textarea.addEventListener('input', function() {
                localStorage.setItem(storageKey, this.value);
            });

            // フォーム送信時にクリア
            form.addEventListener('submit', function() {
                localStorage.removeItem(storageKey);
            });
        });
    </script>
@endsection
