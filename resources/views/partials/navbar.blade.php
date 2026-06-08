@guest
    <li class="nav-item"><a class="nav-link" href="{{ route('login.form') }}">Entrar</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('register.form') }}">Criar conta</a></li>
@endguest

@auth
    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>

    @if (auth()->user()->role === 'gerente')
        <li class="nav-item"><a class="nav-link" href="{{ route('categorias.index') }}">Categorias</a></li>
    @endif

    <li class="nav-item"><a class="nav-link" href="{{ route('produtos.index') }}">Produtos</a></li>
    <li class="nav-item">{{ auth()->user()->name }} ({{ auth()->user()->role }})</li>
    <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-link nav-link p-0" type="submit">Sair</button>
        </form>
    </li>
@endauth