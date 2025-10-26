<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hassam Todo - Professional Task Manager')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.98);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .navbar-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #667eea;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #667eea;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                padding: 1rem;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            .nav-links.active {
                display: flex;
            }

            .mobile-menu-btn {
                display: block;
            }

            .container {
                padding: 0 1rem;
            }

            .navbar {
                padding: 1rem;
            }
        }
    </style>
    @yield('styles')
</head>

<body>
    {{-- resources/views/partials/navbar.blade.php --}}

    @php
        use Illuminate\Support\Facades\Auth;

        $user = Auth::user();
        $initial = strtoupper(substr($user?->name ?? 'U', 0, 1));
    @endphp

    <nav class="main-nav">
        <div class="nav-container">

            {{-- LEFT: BRAND --}}
            <a href="{{ route('tasks.index') }}" class="brand">
                <div class="brand-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="brand-text">
                    <span class="brand-title">Hassam Todo</span>
                    <span class="brand-subtitle">Task Manager</span>
                </div>
            </a>

            {{-- MOBILE TOGGLE (visible <768px) --}}
            <button class="mobile-toggle" id="mobileToggleBtn" aria-label="Toggle menu" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>

            {{-- CENTER/LINKS (Desktop only) --}}
            <ul class="nav-links" id="mainNavLinks">
                <li>
                    <a href="{{ route('tasks.index') }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tasks.create') }}">
                        <i class="fas fa-plus"></i>
                        <span>New Task</span>
                    </a>
                </li>
            </ul>

            {{-- RIGHT: USER DROPDOWN (if logged in) --}}
            @auth
                <div class="user-area">
                    <button class="user-toggle" id="userToggleBtn" onclick="toggleUserDropdown()" aria-label="User menu">
                        <div class="avatar-circle">{{ $initial }}</div>
                        <div class="user-meta">
                            <div class="user-name">{{ $user->name }}</div>
                            <div class="user-email">{{ $user->email }}</div>
                        </div>
                        <i class="fas fa-chevron-down caret"></i>
                    </button>

                    <div class="user-dropdown" id="userDropdownMenu">
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>

              
                        <form action="{{ route('logout') }}" method="POST" class="dropdown-item logout-form">
                            @csrf
                            <button type="submit" class="logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endauth

            @guest
                <div class="auth-links-guest">
                    <a href="{{ route('login') }}" class="auth-btn login-btn">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="auth-btn register-btn">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                </div>
            @endguest
        </div>

        {{-- MOBILE PANEL (collapses under nav on small screens) --}}
        <div class="mobile-panel" id="mobilePanel">
            <ul class="mobile-links">
                <li>
                    <a href="{{ route('tasks.index') }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tasks.create') }}">
                        <i class="fas fa-plus"></i>
                        <span>New Task</span>
                    </a>
                </li>

                @auth
                    <li class="mobile-section-label">Account</li>
                    <li>
                        <a href="{{ route('profile.edit') }}">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profile.edit') }}#password">
                            <i class="fas fa-lock"></i>
                            <span>Change Password</span>
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="mobile-logout-form">
                            @csrf
                            <button type="submit">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                @endauth

                @guest
                    <li class="mobile-section-label">Account</li>
                    <li>
                        <a href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i>
                            <span>Register</span>
                        </a>
                    </li>
                @endguest
            </ul>
        </div>
    </nav>

    <style>
        /* ====== NAV WRAPPER ====== */
        .main-nav {
            background: #1e2139;
            color: #fff;
            box-shadow: 0 12px 40px -10px rgba(0, 0, 0, 0.6);
            position: sticky;
            top: 0;
            z-index: 1000;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Inter", Roboto, "Segoe UI", Arial, sans-serif;
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: .75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        /* ====== BRAND / LOGO ====== */
        .brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            text-decoration: none;
            color: #fff;
        }

        .brand-icon {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .brand-text {
            line-height: 1.2;
            display: flex;
            flex-direction: column;
        }

        .brand-title {
            font-size: .95rem;
            font-weight: 600;
            color: #fff;
        }

        .brand-subtitle {
            font-size: .7rem;
            color: #a5a9ff;
            font-weight: 500;
        }

        /* ====== DESKTOP LINKS ====== */
        .nav-links {
            display: none;
            /* hidden on mobile */
            list-style: none;
            align-items: center;
            gap: 1rem;
        }

        .nav-links li a {
            display: flex;
            align-items: center;
            gap: .5rem;
            color: #d7dbff;
            font-size: .9rem;
            font-weight: 500;
            text-decoration: none;
            background: rgba(255, 255, 255, 0);
            padding: .5rem .75rem;
            border-radius: .5rem;
            transition: all .15s;
        }

        .nav-links li a:hover {
            background: rgba(255, 255, 255, 0.07);
            color: #fff;
        }

        /* ====== USER AREA (desktop) ====== */
        .user-area {
            position: relative;
        }

        .user-toggle {
            display: flex;
            align-items: center;
            gap: .6rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: .75rem;
            padding: .5rem .75rem;
            cursor: pointer;
            min-width: 0;
            color: #fff;
        }

        .user-toggle:focus {
            outline: 2px solid #6366f1;
            outline-offset: 2px;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: .6rem;
            background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);
            color: #fff;
            font-size: .8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-meta {
            display: none;
            flex-direction: column;
            min-width: 0;
            text-align: left;
            line-height: 1.2;
        }

        .user-name {
            font-size: .8rem;
            font-weight: 600;
            color: #fff;
            max-width: 140px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .user-email {
            font-size: .7rem;
            font-weight: 400;
            color: #9ca3ff;
            max-width: 140px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .caret {
            font-size: .7rem;
            color: #9ca3ff;
        }

        /* dropdown menu */
        .user-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + .5rem);
            min-width: 180px;
            background: #fff;
            color: #111;
            border-radius: .75rem;
            box-shadow: 0 30px 70px -10px rgba(0, 0, 0, .55);
            padding: .5rem 0;
            display: none;
            z-index: 9999;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: .6rem;
            width: 100%;
            font-size: .9rem;
            font-weight: 500;
            color: #1f2937;
            text-decoration: none;
            background: transparent;
            border: 0;
            padding: .6rem 1rem;
            text-align: left;
            cursor: pointer;
        }

        .dropdown-item i {
            width: 18px;
            text-align: center;
            font-size: .8rem;
            color: #4f46e5;
        }

        .dropdown-item:hover {
            background: #f3f4f6;
        }

        .logout-form {
            margin: 0;
            padding: 0;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: .6rem;
            background: none;
            border: 0;
            width: 100%;
            font-size: .9rem;
            font-weight: 500;
            cursor: pointer;
            color: #b91c1c;
            padding: .6rem 1rem;
            text-align: left;
        }

        .logout-btn i {
            color: #b91c1c;
        }

        /* ====== AUTH LINKS (guest desktop) ====== */
        .auth-links-guest {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .auth-btn {
            font-size: .8rem;
            font-weight: 500;
            border-radius: .6rem;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .6rem .8rem;
            border: 1px solid rgba(255, 255, 255, .15);
            background: rgba(255, 255, 255, .05);
            color: #fff;
            text-decoration: none;
            transition: all .15s;
        }

        .auth-btn:hover {
            background: rgba(255, 255, 255, .12);
        }

        .register-btn {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: 0;
        }

        /* ====== MOBILE PANEL ====== */
        .mobile-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            font-size: 1rem;
            color: #fff;
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: .6rem;
            cursor: pointer;
        }

        .mobile-panel {
            display: none;
            background: #1e2139;
            border-top: 1px solid rgba(255, 255, 255, .07);
        }

        .mobile-links {
            list-style: none;
            margin: 0;
            padding: .75rem 1rem 1rem;
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .mobile-links li a,
        .mobile-links li button {
            display: flex;
            align-items: center;
            gap: .6rem;
            width: 100%;
            background: rgba(255, 255, 255, 0);
            border: 0;
            border-radius: .5rem;
            padding: .75rem .75rem;
            text-align: left;
            color: #fff;
            font-size: .9rem;
            font-weight: 500;
            text-decoration: none;
        }

        .mobile-links li a i,
        .mobile-links li button i {
            width: 18px;
            text-align: center;
            color: #a5a9ff;
            font-size: .8rem;
        }

        .mobile-links li a:hover,
        .mobile-links li button:hover {
            background: rgba(255, 255, 255, .07);
        }

        .mobile-section-label {
            font-size: .7rem;
            font-weight: 600;
            color: #9ca3ff;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: .5rem .25rem 0;
            margin-top: .5rem;
        }

        .mobile-logout-form {
            margin: 0;
            padding: 0;
        }

        .mobile-logout-form button {
            color: #fff;
            cursor: pointer;
            background: none;
        }

        /* ====== BREAKPOINTS ====== */
        @media (min-width: 768px) {
            .nav-links {
                display: flex;
            }

            .mobile-toggle {
                display: none;
            }

            .mobile-panel {
                display: none !important;
            }

            .user-meta {
                display: flex;
            }
        }
    </style>

    <script>
        /**
         * Toggle mobile nav panel
         */
        function toggleMobileMenu() {
            var panel = document.getElementById('mobilePanel');
            if (!panel) return;

            if (panel.style.display === 'block') {
                panel.style.display = 'none';
            } else {
                panel.style.display = 'block';
            }
        }

        /**
         * Toggle user dropdown (desktop)
         */
        function toggleUserDropdown() {
            var menu = document.getElementById('userDropdownMenu');
            if (!menu) return;
            var isOpen = menu.style.display === 'block';
            closeAllDropdowns();
            menu.style.display = isOpen ? 'none' : 'block';
        }

        /**
         * Close dropdowns when clicking outside
         */
        document.addEventListener('click', function(e) {
            var userBtn = document.getElementById('userToggleBtn');
            var userMenu = document.getElementById('userDropdownMenu');

            if (userMenu && userBtn) {
                if (!userBtn.contains(e.target) && !userMenu.contains(e.target)) {
                    userMenu.style.display = 'none';
                }
            }
        });

        /**
         * Helper: close all dropdowns
         */
        function closeAllDropdowns() {
            var menus = document.querySelectorAll('.user-dropdown');
            menus.forEach(function(m) {
                m.style.display = 'none';
            });
        }
    </script>



    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    <script>
        function toggleMenu() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('active');
        }
    </script>
    @yield('scripts')
    {{-- MAIN PAGE CONTENT --}}
    @if (isset($slot))
        {{ $slot }}
 
    @endif


</body>

</html>
