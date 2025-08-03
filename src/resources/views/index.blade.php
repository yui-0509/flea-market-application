@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
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
    <div class="item-table__header">
        <a href="/?keyword={{ request('keyword') }}"
        class="item-table__header-a {{ request('tab') !== 'mylist' ? 'active-tab' : '' }}">おすすめ</a>
        <a href="/?tab=mylist&keyword={{ request('keyword') }}"
        class="item-table__header-a {{ request('tab') === 'mylist' ? 'active-tab' : '' }}">マイリスト</a>
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
            @if(request('tab') === 'mylist')
                @auth
                    <p>いいねした商品がありません。</p>
                @else
                    <p></p>
                @endauth
            @else
                <p>おすすめの商品がありません。</p>
            @endif
        @endforelse
    </div>
@endsection