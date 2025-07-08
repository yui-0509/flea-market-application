<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>coachtechフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    @yield('css')
</head>

<body>
    <div class="wrapper">
        <header class="header">
            <div class="header__inner">
                <a href="/">
                    <img class="header__logo" src="{{ asset('images/logo.svg') }}" alt="COACHTECH">
                </a>
                <div class="header__contents">
                @yield('header-contents')
                </div>
            </div>
        </header>

        <main class="main">
            @yield('content')
        </main>
    </div>
</body>

</html>