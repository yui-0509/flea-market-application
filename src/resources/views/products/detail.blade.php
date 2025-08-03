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
    <div class="product-detail-container">
        <div class="left-column">
            <div class="product-image">
                <img src="{{ asset($item->item_image) }}" alt="{{ $item->item_name }}" class="{{ $item->is_sold ? 'sold-image' : '' }}">
                @if($item->is_sold)
                    <div class="sold-label">SOLD</div>
                @endif
            </div>
        </div>

        <div class="right-column">
            <h1 class="product-name">{{ $item->item_name }}</h1>
            @if ($item->brand)
                <p class="brand-name">{{ $item->brand->brand_name }}</p>
            @endif
            <p class="price">
                <span class="yen">¥</span>{{ number_format($item->price) }}
                <span class="tax">(税込)</span>
            </p>

            <div class="icons">
                @auth
                    <span class="icon"
                        id="like-button"
                        data-item-id="{{ $item->id }}"
                        data-liked="{{ $item->isLikedBy(Auth::user()) ? 'true' : 'false' }}">
                        <img src="{{ asset('images/star.png') }}"
                            alt="いいね"
                            class="icon-img {{ $item->isLikedBy(Auth::user()) ? 'liked' : '' }}" id="like-icon">
                        <span class="like-count">{{ $item->likes->count() }}</span>
                    </span>
                @else
                    <a href="{{ route('login') }}" class="icon">
                        <img src="{{ asset('images/star.png') }}" alt="いいね" class="icon-img">
                        <span class="like-count">{{ $item->likes->count() }}</span>
                    </a>
                @endauth
                <span class="icon">
                    <img src="{{ asset('images/bubble.png') }}"
                        alt="コメント"
                        class="icon-img"
                        id="comment-icon">
                    <span id="comment-count">{{$item->comments->count() }}</span>
                </span>
            </div>

            <a href="{{ route('purchase', ['item' => $item->id]) }}" class="purchase-button">
                購入手続きへ
            </a>

            <div class="section description-section">
                <h2>商品説明</h2>
                <p>{!! nl2br(e($item->description)) !!}</p>
            </div>

            <div class="section">
                <h2>商品の情報</h2>
                <p><strong>カテゴリー</strong>
                    @foreach ($item->categories as $category)
                        <span class="category-tag">{{ $category->category_name }}</span>
                    @endforeach
                </p>
                <p><strong>商品の状態</strong> {{ $statusText }}</p>
            </div>

            <div class="section comment-section">
                <h2>コメント(<span id="comment-title-count">{{ $item->comments->count() }}</span>)</h2>

                <div id="comment-list">
                    @foreach($item->comments as $comment)
                        <div class="comment">
                            <div class="comment-avatar">
                                @if($comment->user->profile && $comment->user->profile->profile_image)
                                    <img src="{{ asset('storage/' . $comment->user->profile->profile_image) }}" alt="ユーザーアイコン">
                                @else
                                    <div class="default-avatar"></div>
                                @endif
                            </div>
                            <div class="comment-body">
                                <div class="comment-author">{{ $comment->user->username }}</div>
                                <div class="comment-content">{!! nl2br(e($comment->content)) !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="comment-form">
                <h2>商品へのコメント</h2>
                <form id="comment-form" action="{{ route('item.comment', ['item' => $item->id]) }}" method="POST">
                    @csrf
                    <textarea name="content" rows="4"></textarea>
                    <button type="submit">コメントを送信する</button>
                </form>
                <div class="form__error">
                    @error('content')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

        // -------------------------
        // いいねボタン処理
        // -------------------------
        const likeButton = document.getElementById('like-button');
        const likeIcon = document.getElementById('like-icon');
        const likeCount = document.querySelector('.like-count');

        if (likeButton) {
            let liked = likeButton.dataset.liked === 'true';

            likeButton.addEventListener('click', () => {
                const itemId = likeButton.dataset.itemId;

                if (!itemId) {
                    window.location.href = '/login';
                    return;
                }

                const method = liked ? 'DELETE' : 'POST';
                const url = `/item/like/${itemId}`;

                fetch(url, {
                    method: method,
                    headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    likeCount.textContent = data.count;
                    if (data.message === 'liked') {
                        likeIcon.classList.add('liked');
                        liked = true;
                    } else if (data.message === 'unliked') {
                        likeIcon.classList.remove('liked');
                        liked = false;
                    }
                })
                .catch(error => {
                    console.error('いいねエラー:', error);
                });
            });
        }

        // -------------------------
        // コメント送信処理
        // -------------------------
        const commentForm = document.getElementById('comment-form');
        const commentCount = document.getElementById('comment-count');
        const commentTitleCount = document.getElementById('comment-title-count');
        const commentIcon = document.getElementById('comment-icon');

        if (commentForm) {
            commentForm.addEventListener('submit', function (e) {
                if (!isLoggedIn) {
                    e.preventDefault();
                    window.location.href = '/login';
                    return;
                }

                e.preventDefault();

                const formData = new FormData(this);
                const actionUrl = this.action;

                fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(async response => {
                    const errorContainer = document.querySelector('.form__error');
                    if (errorContainer) errorContainer.textContent = ''; // エラー欄を初期化

                    if (!response.ok) {
                        if (response.status === 422) {
                            const data = await response.json();
                            if (data.errors && data.errors.content) {
                                errorContainer.textContent = data.errors.content[0];
                            }
                        } else {
                            console.error('予期しないエラー:', response.status);
                        }
                        return;
                    }

                    const data = await response.json();
                    if (data.success) {
                        commentCount.textContent = data.comment_count;
                        commentTitleCount.textContent = data.comment_count;
                        commentIcon.classList.add('commented');

                        const commentList = document.getElementById('comment-list');
                        const commentElement = document.createElement('div');
                        commentElement.classList.add('comment');

                        const avatarHTML = data.comment.avatar
                            ? `<img src="${data.comment.avatar}" alt="ユーザーアイコン" class="avatar-img">`
                            : `<div class="default-avatar"></div>`;

                        commentElement.innerHTML = `
                            <div class="comment-avatar">
                                ${avatarHTML}
                            </div>
                            <div class="comment-body">
                                <div class="comment-author">${data.comment.author}</div>
                                <div class="comment-content">${data.comment.content}</div>
                            </div>
                        `;

                        commentList.appendChild(commentElement);
                        commentForm.reset();
                    }
                })
                .catch(error => {
                    console.error('コメント送信エラー:', error);
                });
            });
        }
    });
    </script>
@endsection