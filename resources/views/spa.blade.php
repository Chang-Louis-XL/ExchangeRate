<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>匯率轉換系統 - SPA</title>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- 使用 Vite 引入CSS -->
    @vite(['resources/css/app.css'])
</head>
<body>
    <!-- React 應用掛載點 -->
    <div id="app"></div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <!-- 引入 React 應用 (通過 Vite) -->
    @viteReactRefresh
    @vite(['resources/js/app.jsx'])
</body>
</html>
