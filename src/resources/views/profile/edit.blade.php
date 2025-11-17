{{-- 共通のレイアウト読み込み --}}
@extends('layouts.app') 
@section('title', 'プロフィール設定')

{{-- edit.cssを読み込む --}}
@section('css')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')
    <div class="profile-form__container">
        
        <h2>プロフィール設定</h2>
        
        {{-- 💡 アクションは ProfileController@update、PATCHメソッド、画像アップロード設定済み --}}
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="profile-image-area">
                {{-- 既存の画像表示 --}}
                @php
                    // $profile は ProfileController@edit から渡されている前提
                    $imagePath = $profile->image_path ?? null; 
                    $placeholderClass = $imagePath ? '' : 'profile-placeholder';
                @endphp
                
                {{-- ★★★ 画像のプレビューは行わず、既存の画像またはプレースホルダーを表示 ★★★ --}}
                <img 
                    id="profile-preview" 
                    src="{{ $imagePath ? asset('storage/' . $imagePath) : '' }}" 
                    class="profile-image-frame {{ $placeholderClass }}"
                >
                
                {{-- 画像選択ボタン（input type="file"） --}}
                <label for="image_upload" class="image-select-button">
                    画像を選択する
                </label>
                <input type="file" id="image_upload" name="image" style="display: none;" accept="image/jpeg, image/png">
                
                @error('image')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>


            <div class="form__group">
                <label class="form__label">ユーザー名</label>
                {{-- ユーザー名：$userから既存値を取得し、old()で保持 --}}
                <input type="text" name="name" class="form__input" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="form__label">郵便番号</label>
                {{-- 郵便番号：$profileから既存値を取得し、old()で保持 --}}
                <input type="text" name="post_code" class="form__input" 
                    value="{{ old('post_code', $profile->post_code ?? '') }}" 
                    placeholder="例: 123-4567" required>
                @error('post_code')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="form__label">住所</label>
                {{-- 住所：$profileから既存値を取得し、old()で保持 --}}
                <input type="text" name="address" class="form__input" 
                    value="{{ old('address', $profile->address ?? '') }}" required>
                @error('address')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="form__label">建物名</label>
                {{-- 建物名：$profileから既存値を取得し、old()で保持 --}}
                <input type="text" name="building" class="form__input" 
                    value="{{ old('building', $profile->building ?? '') }}">
                @error('building')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <button type="submit" class="form__button" >更新する</button>
            </div>
        </form>
    </div>
@endsection