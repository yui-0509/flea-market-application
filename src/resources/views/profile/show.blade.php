@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
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
    <div class="profile-header">
        <div class="profile-left">
            <div class="profile-image">
                @if ($profile->profile_image)
                    <img src="{{ asset('storage/' . $profile->profile_image) }}" alt="プロフィール画像">
                @else
                    <div class="default-image"></div>
                @endif
            </div>
            <div class="profile-info">
                <p class="user-name">{{ Auth::user()->username }}</p>
            </div>
        </div>
        <div class="profile-edit">
            <a href="{{ route('profile.edit') }}" class="edit-button">プロフィールを編集</a>
        </div>
    </div>
    <div class="item-table__header">
        <a href="/mypage?tab=sell" class="item-table__header-a {{ request('tab', 'sell') === 'sell' ? 'active-tab' : '' }}">出品した商品</a>
        <a href="/mypage?tab=buy" class="item-table__header-a {{ request('tab') === 'buy' ? 'active-tab' : '' }}">購入した商品</a>
    </div>
    <div class="items-container">
        @forelse ($items as $item)
            <div class="item-card {{ $item->is_sold ? 'sold' : '' }}">
                <div class="image-wrapper">
                    <a href="{{ route('products.detail', ['item' => $item->id]) }}">
                        <img src="{{ $item->item_image }}" alt="{{ $item->item_name }}">
                    </a>
                    @if($item->is_sold)
                        <div class="sold-label">SOLD</div>
                    @endif
                </div>
                <h3 class="item-name">
                    <a href="{{ route('products.detail', ['item' => $item->id]) }}">
                        {{ $item->item_name }}
                    </a>
                </h3>
            </div>
        @empty
            @if(request('tab') === 'buy')
                <p>購入した商品がありません。</p>
            @else
                <p>出品した商品がありません。</p>
            @endif
        @endforelse
    </div>
@endsection