<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Library API</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>
    @vite(['resources/scss/welcome.scss'])
</head>
<body>
<div class="card">
    <h1>Book Library API</h1>
    <p>REST API for book library. Quick links:</p>
    <div class="links">
        <a href="{{ url('/api/books') }}" target="_blank">Books API</a>
        <a href="{{ url('/docs/api') }}" target="_blank">Swagger UI</a>
    </div>
</div>
</body>
</html>
