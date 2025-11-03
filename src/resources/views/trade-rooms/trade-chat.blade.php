@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/trade-chat.css') }}">
@endsection

@section('content')
<div class="trade-room-layout">
    <div class="trade-sidebar">
        <div class="sidebar-header">その他の取引</div>
        <div class="trade-list">
            @foreach($allTradingRooms as $tradingRoom)
                <a href="{{ route('trade-rooms.show', $tradingRoom->id) }}" class="trade-item {{ $tradingRoom->id === $room->id ? 'active' : '' }}">
                    {{ $tradingRoom->purchase->item->item_name}}
                </a>
            @endforeach
        </div>
    </div>

    <div class="trade-chat-area">
        <div class="trade-room-header">
            <div class="header-user">
                <div class="user-avatar">
                    @if($otherUser->profile && $otherUser->profile->profile_image)
                        <img src="{{ asset('storage/' . $otherUser->profile->profile_image) }}" alt="プロフィール画像">
                    @endif
                </div>
                <h1 class="trade-room-title">「{{ $otherUser->username }}」さんとの取引画面</h1>
            </div>

            @if($purchase->status === 'trading' && $purchase->user_id === auth()->id())
                {{-- 購入者が取引中の場合 --}}
                <button class="complete-button" onclick="openRatingModal()">取引を完了する</button>
            @elseif($purchase->status === 'awaiting_seller_rating' && $purchase->item->user_id === auth()->id())
                {{-- 出品者が評価待ちの場合、自動でモーダル表示 --}}
                <script>
                    window.addEventListener('DOMContentLoaded', function() {
                        openRatingModal();
                    });
                </script>
            @endif
        </div>

        <div class="product-info">
            <div class="product-image">
                <img src="{{ asset($item->item_image) }}" alt="{{ $item->item_name }}">
            </div>
            <div class="product-details">
                <h2 class="product-name">{{ $item->item_name }}</h2>
                <p class="product-price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>

        <div class="chat-area">
            @forelse($messages as $message)
                @if($message->trashed())
                    <div class="message-wrapper message-deleted">
                        <p class="deleted-text">このメッセージは削除されました</p>
                    </div>
                @else
                    <div class="message-wrapper {{ $message->sender_id === auth()->id() ? 'message-right' : 'message-left' }}">
                        @if($message->sender_id !== auth()->id())
                            <div class="message-header">
                                <div class="user-avatar">
                                    @if($message->sender->profile && $message->sender->profile->profile_image)
                                        <img src="{{ asset('storage/' . $message->sender->profile->profile_image) }}" alt="プロフィール画像">
                                    @endif
                                </div>
                                <span class="user-name">{{ $message->sender->username }}</span>
                            </div>
                            <div class="message-bubble">
                                @if($message->body)
                                    <p>{{ $message->body }}</p>
                                @endif
                                @if($message->image_path)
                                    <div class="message-image">
                                        <img src="{{ asset('storage/' . $message->image_path) }}" alt="送信画像">
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="message-header-right">
                                <span class="user-name">{{ $message->sender->username }}</span>
                                <div class="user-avatar">
                                    @if($message->sender->profile && $message->sender->profile->profile_image)
                                        <img src="{{ asset('storage/' . $message->sender->profile->profile_image) }}" alt="プロフィール画像">
                                    @endif
                                </div>
                            </div>
                            <div class="message-bubble message-own">
                                @if($message->body)
                                    <p>{{ $message->body }}</p>
                                @endif
                                @if($message->image_path)
                                    <div class="message-image">
                                        <img src="{{ asset('storage/' . $message->image_path) }}" alt="送信画像">
                                    </div>
                                @endif
                            </div>
                            <div class="message-actions">
                                <button class="edit-button" data-message-id="{{ $message->id }}">編集</button>
                                <button class="delete-button" data-message-id="{{ $message->id }}">削除</button>
                            </div>
                        @endif
                    </div>
                @endif
            @empty
                <p class="no-messages">まだメッセージがありません</p>
            @endforelse
        </div>

        <form action="{{ route('trade-rooms.messages.store', $room) }}"
            method="post" enctype="multipart/form-data" class="message-form">
            @csrf

            @if($errors->any())
                <div class="error-messages">
                    @foreach($errors->all() as $error)
                        <p class="error-message">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="input-container">
                <input type="text" name="body" class="message-input" placeholder="取引メッセージを記入してください" value="{{ old('body') }}">

                <input type="file" name="image" id="image-input"   style="display: none;">
                <button type="button" class="image-button" onclick="document.getElementById('image-input').click()">画像を追加</button>
                <span id="image-name" class="image-name"></span>

                <button type="submit" class="send-button">
                    <img src="{{ asset('images/paper-airplane.jpg') }}" alt="送信">
                    </svg>
                </button>
            </div>
        </form>
        <script>
        // 画像選択時にファイル名を表示
        document.getElementById('image-input').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || '';
            document.getElementById('image-name').textContent = fileName ? `選択: ${fileName}` : '';
        });
        document.addEventListener('DOMContentLoaded', function() {
            const messageInput = document.querySelector('.message-input');
            const roomId = {{ $room->id }};
            const storageKey = `trade-message-draft-${roomId}`;

            const savedDraft = localStorage.getItem(storageKey);
            if (savedDraft) {
                messageInput.value = savedDraft;
            }

            messageInput.addEventListener('input', function() {
                localStorage.setItem(storageKey, this.value);
            });

            document.querySelector('.message-form').addEventListener('submit', function() {
                localStorage.removeItem(storageKey);
            });
            // 編集ボタンのクリック
            document.querySelectorAll('.edit-button').forEach(button => {
                button.addEventListener('click', function() {
                    const messageId = this.dataset.messageId;
                    const messageWrapper = this.closest('.message-wrapper');
                    const messageBubble = messageWrapper.querySelector('.message-bubble');
                    const originalText = messageBubble.querySelector('p').textContent;

                    // テキストボックスに変更
                    messageBubble.innerHTML = `
                        <form action="/trade-rooms/{{ $room->id }}/messages/${messageId}" method="POST" class="edit-form">
                            @csrf
                            @method('PATCH')
                            <textarea name="body" class="edit-textarea" required>${originalText}</textarea>
                            <div class="edit-actions">
                                <button type="submit" class="save-button">保存</button>
                                <button type="button" class="cancel-button">キャンセル</button>
                            </div>
                        </form>
                    `;

                    // キャンセルボタン
                    messageBubble.querySelector('.cancel-button').addEventListener('click', function() {
                        messageBubble.innerHTML = `<p>${originalText}</p>`;
                    });
                });
            });

            //削除ボタン
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    if (!confirm('このメッセージを削除しますか？')) {
                        return;
                    }

                    const messageId = this.dataset.messageId;

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/trade-rooms/{{ $room->id }}/messages/${messageId}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                });
            });
        });
        </script>
    </div>
</div>

{{-- 取引完了モーダル --}}
<div id="rating-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>取引が完了しました。</h2>
        </div>
        <div class="modal-body">
            <p class="rating-question">今回の取引相手はどうでしたか？</p>

            <form action="{{ route('purchases.rating.store', $purchase) }}" method="POST" id="rating-form">
                @csrf
                <div class="star-rating">
                    <input type="radio" name="stars" value="5" id="star5" required>
                    <label for="star5"><img src="{{ asset('images/black-star.jpg') }}" alt="星"></label>

                    <input type="radio" name="stars" value="4" id="star4">
                    <label for="star4"><img src="{{ asset('images/black-star.jpg') }}" alt="星"></label>

                    <input type="radio" name="stars" value="3" id="star3">
                    <label for="star3"><img src="{{ asset('images/black-star.jpg') }}" alt="星"></label>

                    <input type="radio" name="stars" value="2" id="star2">
                    <label for="star2"><img src="{{ asset('images/black-star.jpg') }}" alt="星"></label>

                    <input type="radio" name="stars" value="1" id="star1">
                    <label for="star1"><img src="{{ asset('images/black-star.jpg') }}" alt="星"></label>
                </div>

                <button type="submit" class="submit-rating-button">送信する</button>
            </form>
        </div>
    </div>
</div>
<script>
// モーダルを開く
function openRatingModal() {
    document.getElementById('rating-modal').style.display = 'flex';
}
</script>

@endsection