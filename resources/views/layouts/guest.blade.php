<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kaca Abadi')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])

</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    @yield('content')
</body>
</html>
