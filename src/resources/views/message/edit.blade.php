{{-- 共通のレイアウト --}}
@extends('layouts.app')

{{-- cssを読み込み --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('css/message.css') }}">
@endsection


@section('content')
    <div class="edit-message-container">
        <h2>メッセージ編集</h2>

        {{-- Messageモデルのpurchase_idを取得、Messageモデルのidを取得 --}}
        <form action="{{ route('message.update', ['purchaseId' => $message->purchase_id, 'messageId' => $message->id]) }}"
            method="POST" class="edit-form">
            @csrf
            @method('PATCH')

            <textarea name="content">{{ $message->content }}</textarea>

            <div class="edit-actions">
                <a href="{{ route('message.show', ['purchaseId' => $message->purchase_id]) }}" class="btn-cancel">キャンセル</a>
                <button type="submit" class="btn-update">更新する</button>
            </div>
        </form>
    </div>
@endsection
