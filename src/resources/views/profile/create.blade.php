@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endsection

@section('header-contents')
    <div class="header__center">
        <form class="header__form" action="/search" method="GET">
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
    <div class="profile-form__content">
        <div class="profile-form__heading">
            <h2>プロフィール設定</h2>
        </div>
        <form class="form" action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="form__group form__group--image">
                <div class="form__image-preview no-image" id="profileImagePreview"></div>
                <div class="form__image-upload">
                    <label for="profile_image" class="form__image-label">画像を選択する</label>
                    <input type="file" id="profile_image" name="profile_image" accept=".jpeg, .jpg, .png" class="form__image-input">
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">ユーザー名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="username" value="{{ old('username', $user->username) }}">
                    </div>
                    <div class="form__error">
                        @error('username')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">郵便番号</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="post_code" maxlength="8" value="{{ old('post_code') }}">
                    </div>
                    <div class="form__error">
                        @error('post_code')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">住所</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="address" value="{{ old('address') }}">
                    </div>
                    <div class="form__error">
                        @error('address')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">建物名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="building" value="{{ old('building') }}">
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">更新する</button>
            </div>
        </form>
    </div>
    <script>
    document.getElementById('profile_image').addEventListener('change', function(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profileImagePreview');
                preview.style.backgroundImage = `url(${e.target.result})`;
                preview.style.backgroundSize = 'cover';
                preview.style.backgroundPosition = 'center';
                preview.classList.remove('no-image');
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
    </script>
@endsection