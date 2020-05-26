<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>麻雀</title>

        <link rel="icon" href="/favicon.ico">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <link href="/css/app.css" rel="stylesheet">
        <style>
            .table-responsive th,td {
                white-space: nowrap;
            }
            input[type="number"].point {
                width: 40px;
            }
        </style>
    </head>
    <body>
        <div class="container" id="app">
            @yield('content')
        </div>
    </body>
    <script type="text/javascript" src="{{ mix('/js/app.js') }}"></script>
</html>
