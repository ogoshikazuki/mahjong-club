<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>麻雀</title>
        <link rel="icon" href="/favicon.ico">
        <link rel="manifest" href="manifest.json">
        <link rel="apple-touch-icon" sizes="144x144" href="/images/icons/icon-144.png">
    </head>
    <body>
        <div id="app"></div>
        <script type="text/javascript" src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
