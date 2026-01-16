@extends('layouts.app')
@section('title', '住所の変更')
@section('css')
<link rel="stylesheet" href="{{ asset('css/address_edit.css') }}">
@endsection

@section('content')
<div class="address-change-container">
    <h2>住所の変更</h2>
    
    {{-- フォームアクションは PurchaseController@updateAddress へ --}}
    <form action="{{ route('purchase.address.update', ['id' => $item->id]) }}" method="POST">
        @csrf
        @method('PATCH') {{-- 更新処理なのでPATCHを使用 --}}

        <div class="form-group">
            <label for="post_code">郵便番号</label>
            <input type="text" name="post_code" id="post_code" 
                    value="{{ old('post_code', $profile->post_code ?? '') }}" 
                    placeholder="例: 123-4567" required>
            @error('post_code')<p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" 
                    value="{{ old('address', $profile->address ?? '') }}" required>
            @error('address')<p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" 
                    value="{{ old('building', $profile->building ?? '') }}">
            @error('building')<p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="update-button">更新する</button>
    </form>
</div>
@endsection