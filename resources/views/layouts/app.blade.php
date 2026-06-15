<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', config('app.name'))</title>
<!-- Bootstrap CSS via CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="" crossorigin="anonymous">
<!-- Site custom styles -->
<link href="/css/site.css" rel="stylesheet">
</head>
<body class="bg-light">
@include('partials.navbar')
<main class="container py-4">
@yield('content')
</main>
@include('partials.footer')
{{-- JS opcional para componentes do Bootstrap --}}
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="" crossorigin="anonymous"></script>
</body>
</html>