<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-
scale=1">
<title>@yield('title', config('app.name'))</title>
{{-- CSS via CDN (ex.: Bootstrap 5) para simplificar neste
capítulo --}}
https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min
.css
</head>
<body class="bg-light">
@include('partials.navbar')
<main class="container py-4">
@yield('content')
</main>
@include('partials.footer')
{{-- JS opcional para componentes do Bootstrap --}}
https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bund
le.min.js</script>
</body>
</html>