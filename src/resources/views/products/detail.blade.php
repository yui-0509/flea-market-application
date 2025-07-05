@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
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

@endsection