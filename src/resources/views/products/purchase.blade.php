@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
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
    <form action="{{ route('purchase.store', ['item' => $item->id]) }}" method="post">
        @csrf
        <div class="purchase-container">
            <div class="purchase-left">
                <div class="product-info">
                    <div class="product-image">
                        <img src="{{ asset($item->item_image) }}" alt="{{ $item->item_name }}">
                    </div>
                    <div class="product-text">
                        <h2 class="product-name">{{ $item->item_name }}</h2>
                        <p class="product-price">¥{{ number_format($item->price) }}</p>
                    </div>
                </div>

                <div class="section">
                    <h3>支払い方法</h3>
                    <select class="form__select--payment" name="payment" id="payment">
                        <option value="" selected disabled>選択してください</option>
                        @foreach(\App\Models\Purchase::PAYMENT_METHODS as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="form__error">
                        @error('payment')
                        {{ $message }}
                        @enderror
                    </div>
                </div>

                <div class="section">
                    <h3>配送先 <a href="{{ route('purchase.address', ['item' => $item->id]) }}" class="change-link">変更する</a></h3>
                    @if (session('shipping_post_code'))
                        <p class="post-code">〒 {{ session('shipping_post_code') }}</p>
                        <p class="address">{{ session('shipping_address') }} {{ session('shipping_building') ?? '' }}</p>

                        <input type="hidden" name="shipping_post_code" value="{{ session('shipping_post_code') }}">
                        <input type="hidden" name="shipping_address" value="{{ session('shipping_address') }}">
                        <input type="hidden" name="shipping_building" value="{{ session('shipping_building') ?? '' }}">
                    @else
                        <p class="post-code">〒 {{ $profile->post_code }}</p>
                        <p class="address">{{ $profile->address }} {{ $profile->building ?? '' }}</p>

                        <input type="hidden" name="shipping_post_code" value="{{ $profile->post_code }}">
                        <input type="hidden" name="shipping_address" value="{{ $profile->address }}">
                        <input type="hidden" name="shipping_building" value="{{ $profile->building ?? '' }}">
                    @endif
                </div>
                <div class="form__error">
                    @error('shipping_post_code')
                        {{ $message }}
                    @enderror
                    @error('shipping_address')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="purchase-right">
                <div class="summary-box">
                    <div class="summary-row">
                        <span class="item_price">商品代金</span>
                        <span class="price">¥{{ number_format($item->price) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="payment_method">支払い方法</span>
                        <span class="method">選択してください</span>
                    </div>
                </div>

                <button class="purchase-submit-button">購入する</button>
            </div>
        </div>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.getElementById('payment');
            const methodText = document.querySelector('.method');

            const paymentLabels = {
                'convenience': 'コンビニ払い',
                'card': 'カード払い'
            };

            select.addEventListener('change', function () {
                const selected = this.value;
                if (paymentLabels[selected]) {
                    methodText.textContent = paymentLabels[selected];
                } else {
                    methodText.textContent = ''; // 念のため初期化
                }
            });
        });
    </script>
@endsection