<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>取引完了通知</title>
</head>

<body>
    <h2>取引完了通知</h2>

    <p>{{ $buyer->username }}さんより取引が完了し、評価がありました。</p>

    <p>商品名: {{ $purchase->item->item_name }}</p>

    <p>取引完了のため、評価を送信してください。</p>

    <a href="{{ route('trade-rooms.show', $purchase->room->id) }}">取引画面を開く</a>

    <p>フリマアプリ運営チームより</p>
</body>
</html>