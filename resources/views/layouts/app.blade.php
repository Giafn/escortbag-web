<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Escort Bags</title>

    {{-- icon --}}
    <link rel="icon" href="/iconsquare.jpeg" type="image/x-icon" />
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Scripts -->
    <style>
        .pop-hover {
            transition: transform 0.2s;
        }
        .pop-hover:hover {
            transform: scale(1.05);
        }

        .page-item.active .page-link {
            background-color: #000;
            border-color: #000;
        }

        .dropdown-item.active, .dropdown-item:active {
            background-color: #000 !important;
            color: #fff !important;
        }
    </style>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" type="text/css" href="/css/toastr.min.css">
    <style>
        /* buat success toast menjadi warna hitam bg nya */
        .toast-success {
            background-color: #000 !important;
        }
        .toast-container {
            padding-bottom: 20px;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
</head>
<body style="background-color: #fff;" x-data="cartData()">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="/icon.jpeg" alt="escortbag" height="30" class="d-inline-block align-text-top">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                        <li class="nav-item" style="{{ request()->routeIs('items.index') ? 'font-weight: bolder;' : '' }}">
                            <a class="nav-link" href="{{ route('home') }}">{{ auth()->user()->role == 'admin' ? 'List Items' : 'Home' }}</a>
                        </li>
                        @if (auth()->user()->role == 'admin')
                        <li class="nav-item" style="{{ request()->routeIs('order.index') ? 'font-weight: bolder;' : '' }}">
                            <a class="nav-link" href="/order" style="{{ request()->routeIs('order.index') ? 'font-weight: bolder;' : '' }}">
                                Order
                            </a>
                        </li>
                        @endif
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Account
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    {{-- transaksi --}}
                                    <a class="dropdown-item {{ request()->routeIs('my-transactions') ? 'active' : '' }}" href="/my-transactions">
                                        Transactions
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }} ({{ auth()->user()   ->name }})
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            @if (request()->routeIs('catalog') || request()->routeIs('catalog.show'))
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="cart-trigger">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </a>
                            </li>
                            @endif
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
        <footer class="footer mt-auto py-3 bg-white">
            <div class="container text-center">
                <span class="text-muted">© {{ date('Y') }} Escort Bags</span>
            </div>
        </footer>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
        <script src="/js/toastr.min.js"></script>
        <script>
            $(function() {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-bottom-center",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
        });

        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        </script>
        @include('cart')
        @stack('scripts')
    </div>
</body>
</html>
