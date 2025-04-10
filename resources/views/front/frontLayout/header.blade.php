<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="#">Front</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav" style="margin-left: 40rem;">
            <a class="nav-item nav-link active" href="{{ url('/listing') }}"><b>Home</b> <span class="sr-only">(current)</span></a>
            <a class="nav-item nav-link"><b>Features</b></a>
            <a class="nav-item nav-link" data-bs-toggle="modal" href="#myModal" role="button"><b>Pricing</b></a>
            @auth()
            <a class="nav-item nav-link" href="{{ route('get-discussion') }}" role="button"><b>Community</b></a>
            @endauth
            @auth
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <b>{{ Auth::user()->name }}</b>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#">Welcome <b>{{ Auth::user()->name }}</b></a></li>
                    <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('invite') }}">Invite</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="nav-item nav-link" href="{{ route('cart') }}"><b>Cart</b></a></li>
                    <li><a class="nav-item nav-link" href="{{ route('order-list') }}"><b>Orders</b></a></li>
                    <li><a class="nav-item nav-link" href="{{ route('log-out') }}"><b>Logout</b></a></li>
                </ul>
            </li>

            @else
            <a class="nav-item nav-link" data-bs-toggle="modal" href="#exampleModalToggle" role="button"><b>Login</b></a>
            @endauth

        </div>
    </div>
</nav>

@section('scripts')

@endsection
