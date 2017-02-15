<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <title>{{ config('app.name') }}</title>
    <link href="/css/app.css" rel="stylesheet">
</head>
<body>
    @yield('navigation')
    <div class="container">
        @yield('content')
    </div>
    <script src="/js/app.js"></script>
</body>
</html>
