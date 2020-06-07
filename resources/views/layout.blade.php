<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>麻雀</title>

        <link rel="icon" href="/favicon.ico">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
        <style>
            .table-responsive th,td {
                white-space: nowrap;
            }
            input {
                font-size: 16px !important;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-dark bg-dark sticky-top">
            <a href="{{ route('home') }}" class="navbar-brand">Mahjong</a>
            @if(!resolve(App\Service\GameService::class)->isGameStarted())
                <form method="POST" action="{{ route('game.start') }}">
                    {{ csrf_field() }}
                    <button class="btn btn-outline-primary">ゲームスタート</button>
                </form>
            @endif
        </nav>
        <div class="container mt-3" id="app" style="margin-bottom:4rem;">
            @yield('content')
        </div>
        <nav class="navbar navbar-dark bg-dark fixed-bottom justify-content-end">
            <ul class="navbar-nav">
                @if(!Route::is('average-finish-order'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('average-finish-order') }}">平均着順</a>
                    </li>
                @endif
            </ul>
        </nav>
    </body>
    <script type="text/javascript" src="{{ mix('/js/app.js') }}"></script>
</html>
