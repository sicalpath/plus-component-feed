<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>动态管理 - ThinkSNS+</title>
</head>
<body>
    <div id="app"></div>
    @foreach ($scripts as $script)
        <script src="{{ $script }}"></script>
    @endforeach
</body>
</html>