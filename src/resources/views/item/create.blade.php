{{-- 共通のレイアウト読み込み --}}
@extends('layouts.app')
@section('title', '商品出品')

{{-- create.cssを読み込む --}}
@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="item-form__container">
    
    <h2>商品の出品</h2>
    
    {{-- 画像ファイルを含むため、enctype="multipart/form-data" を設定 --}}
    <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 商品画像エリア --}}
        <div class="form__section">
            <h3 class="section__title">商品画像</h3>
            
            <div class="image-upload-area">
                {{-- プレビュー表示用のタグ --}}
                <img id="image-preview" src="" class="item-image-preview" style="display: none; max-width: 100%;">
                
                {{-- 画像選択ボタン --}}
                <label for="image_upload" class="image-select-button">
                    画像を選択する
                </label>
                <input type="file" id="image_upload" name="image" style="display: none;" accept="image/*">
            </div>
            
            {{-- バリデーションエラーメッセージの表示 --}}
            @error('image')
                <div class="form__error" style="color: red; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>
        
        <hr class="form__divider">

        {{-- 商品の詳細セクション --}}
        <div class="form__section">
            <h3 class="section__title">商品の詳細</h3>

            {{-- カテゴリ選択 (複数選択を想定) --}}
            <div class="form__group">
                <label class="form__label">カテゴリ</label>
                <div class="category-list">
                    {{-- ItemControllerのcreate()から渡された $categories をループで表示 --}}
                    @foreach ($categories as $category)
                        <label class="category-tag-label">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                            <span>{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                {{-- カテゴリのエラーメッセージの表示 --}}
                @error('categories')
                    <div class="form__error" style="color: red; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- 商品の状態選択 --}}
            <div class="form__group">
                <label class="form__label">商品の状態</label>
                <select name="condition_id" class="form__select">
                    <option value="">選択してください</option>
                    {{-- ItemControllerのcreate()から渡された $conditions をループで表示 --}}
                    @foreach ($conditions as $id => $name)
                        <option value="{{ $id }}"
                            {{-- バリデーションエラーで戻ったときに選択状態を保持 --}}
                            {{ old('condition_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                {{-- 状態のエラーメッセージの表示 --}}
                @error('condition_id')
                    <div class="form__error" style="color: red; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <hr class="form__divider">

        {{-- 商品名と説明セクション --}}
        <div class="form__section">
            <h3 class="section__title">商品名と説明</h3>

            <div class="form__group">
                <label class="form__label">商品名</label>
                <input type="text" name="name" class="form__input" placeholder="商品名" value="{{ old('name') }}">
                @error('name')
                    <div class="form__error" style="color: red; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="form__label">ブランド名</label>
                <input type="text" name="brand_name" class="form__input" placeholder="ブランド名（任意）" value="{{ old('brand_name') }}">
                @error('brand_name')
                    <div class="form__error" style="color: red; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="form__label">商品の説明</label>
                <textarea name="description" class="form__textarea" placeholder="商品の説明">{{ old('description') }}</textarea>
                @error('description')
                    <div class="form__error" style="color: red; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <hr class="form__divider">

        {{-- 販売価格セクション --}}
        <div class="form__section">
            <h3 class="section__title">販売価格</h3>

            <div class="form__group price-group">
                <span class="price__yen-mark">¥</span>
                <input type="number" name="price" class="form__input form__input--price" placeholder="販売価格" value="{{ old('price') }}">
            </div>
            @error('price')
                <div class="form__error" style="color: red; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- 出品ボタン --}}
        <div class="form__group">
            <button type="submit" class="form__button form__button--submit">出品する</button>
        </div>
    </form>
</div>

{{-- プレビュー用のJavaScript --}}
<script>
    document.getElementById('image_upload').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('image-preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    });
</script>
@endsection
