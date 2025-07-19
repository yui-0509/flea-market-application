@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('header-contents')
    <div class="header__center">
        <form class="form" action="/search" method="GET">
            <input class="header_content-input" type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
        </form>
    </div>

    @auth
    <div class="header__buttons">
        <form class="form_button" action="/logout" method="POST">
            @csrf
            <button  class="header__button--black" type="submit">ログアウト</button>
        </form>
        <a href="/mypage" class="header__button--black">マイページ</a>
        <a href="/sell" class="header__button--white">出品</a>
    </div>
    @endauth
@endsection

@section('content')
    <div class="sell__wrapper">

        <div class="sell__header">
            <h2 class="sell__header-title">商品の出品</h2>
        </div>

        <form class="sell__form" action="/sell/store" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="sell__section">
                <label class="sell__label">商品画像</label>
                <div class="item_image-frame">
                    <label for="item_image" class="sell__upload-button">画像を選択する</label>
                    <input class="sell__input" type="file" name="item_image" id="item_image" accept=".jpeg, .jpg, .png">
                    <img id="preview" class="preview-image">
                    <div class="form__error">
                        @error('item_image')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <h3 class="section__title">商品の詳細</h3>
            <div class="sell__section">
                <label class="sell__label">カテゴリー</label>
                <div class="sell__categories">
                    @foreach ($categories as $category)
                        <input
                            type="checkbox"
                            name="categories[]"
                            value="{{ $category->id }}"
                            id="category_{{ $category->id }}" class="sell__category-checkbox"
                            hidden
                            {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                        >
                        <label for="category_{{ $category->id }}" class="sell__category-button">
                            {{ $category->category_name }}
                        </label>
                    @endforeach
                    <div class="form__error">
                        @error('categories')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <div class="sell__section">
                <label class="sell__label">商品の状態</label>
                <select class="sell__input" name="status" id="status">
                    <option value="" selected disabled>選択してください</option>
                    @foreach (\App\Models\Item::STATUS_LIST as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <div class="form__error">
                    @error('status')
                    {{ $message }}
                    @enderror
                </div>
            </div>

            <h3 class="section__title">商品名と説明</h3>
            <div class="sell__section">
                <label class="sell__label">商品名</label>
                <input class="sell__input" type="text" name="item_name" id="item_name" value="{{ old('item_name') }}">
                <div class="form__error">
                    @error('item_name')
                    {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="sell__section">
                <label class="sell__label" for="brand_name">ブランド名</label>
                <input class="sell__input" type="text" name="brand_name" id="brand_name" value="{{ old('brand_name') }}">
            </div>

            <div class="sell__section">
                <label class="sell__label">商品の説明</label>
                <textarea class="sell__textarea" name="description" id="description">{{ old('description') }}</textarea>
                <div class="form__error">
                    @error('description')
                    {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="sell__section">
                <label class="sell__label">販売価格</label>
                <div class="sell__price-input">
                    <span class="price-yen">¥</span>
                    <input class="sell__input" type="number" name="price" id="price" value="{{ old('price') }}">
                    <div class="form__error">
                        @error('price')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <div class="sell__section">
                <button type="submit" class="sell__submit-button">出品する</button>
            </div>
        </form>
    </div>
    <script>
    document.getElementById('item_image').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const preview = document.getElementById('preview');

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
    </script>
@endsection