<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('css/sanitize.css')}}">
    <link rel="stylesheet" href="{{asset('css/common.css')}}">
    @yield('css')
    <title>@yield('title')</title>
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <a href="/" class="header-logo-wrapper">
                <img src="{{asset('images/logo.png')}}" alt="" class="header-logo">
            </a>
            <div class="header-button">
                <div class="header-button-wrapper">
                    <a href="/" class="header-button-item">
                        勤怠
                    </a>
                </div>
                <div class="header-button-wrapper">
                    <a href="/" class="header-button-item">
                        勤怠一覧
                    </a>
                </div>
                <div class="header-button-wrapper">
                    <a href="/" class="header-button-item">
                        申請
                    </a>
                </div>
                <div class="header-button-wrapper">
                    {{-- @authの予定 --}}
                    <form action="/logout" class="header-form-logout" method="post">
                        @csrf
                        <button class="header-button-item" type="submit">ログアウト</button>
                    </form>
                </div>
            </div>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
    @yield('js')
</body>

</html>