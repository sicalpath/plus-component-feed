<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>动态管理 - ThinkSNS+</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ $csrf_token }}">

    <script type="text/javascript">
        window.FEED = {!! json_encode([
            'baseURL' => $base_url,
            'csrfToken' => $csrf_token,
        ]) !!};
    </script>
</head>
<body>
    <div id="app"></div>
    <script src="{{ \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\asset('admin.js') }}"></script>
</body>
</html>