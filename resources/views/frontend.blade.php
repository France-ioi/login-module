<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Login module</title>
        <script type="text/javascript" src="/build/libs.js"></script>
        <link rel="stylesheet" href="/build/app.css"></script>
    </head>
    <body>
        <script type="text/javascript">
            window.__LOGIN_MODULE_CONFIG={!! json_encode($config) !!}
        </script>
        <div id="app"></div>
        <script type="text/javascript" src="/build/app.js"></script>
    </body>
</html>