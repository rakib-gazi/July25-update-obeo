<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Obeo Admin</title>
    <!-- For Development Environment -->
    @vite('resources/js/app.js')

    @inertiaHead
    @routes
    <meta name="csrf-token" content="{{ csrf_token() }}">


</head>
<body class="bg-gray-200 font-nunito">
    @inertia
</body>
</html>
