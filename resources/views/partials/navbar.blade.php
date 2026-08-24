<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <span class="brand-mark">L</span>
            Lanchonete Web3
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Início</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('sobre') }}">Sobre</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contato.create') }}">Contato</a></li>

                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login.form') }}">Entrar</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm ms-lg-2" href="{{ route('register.form') }}">Criar conta</a></li>
                @endguest

                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>

                    @if (auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('categorias.index') }}">Categorias</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.usuarios.index') }}">Funcionários</a></li>
                    @endif

                    <li class="nav-item"><a class="nav-link" href="{{ route('produtos.index') }}">Produtos</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('pedidos.index') }}">Pedidos</a></li>

                    <li class="nav-item user-pill">
                        <span>{{ auth()->user()->name }}</span>
                        <small>{{ auth()->user()->role }}</small>
                    </li>

                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-outline-primary btn-sm" type="submit">Sair</button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>