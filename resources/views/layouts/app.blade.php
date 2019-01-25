<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link href="/css/app.css" rel="stylesheet">
    <script src="/js/app.js" charset="UTF-8"></script>
</head>
<body {!! Request::is('profile') ? 'data-spy="scroll" data-target="#contextualNav"' : '' !!}>
    @yield('navigation')
    <div class="container">
        @yield('content')
    </div>
</body>
</html>