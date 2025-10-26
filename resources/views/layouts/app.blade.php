{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Hassam Todo') }}</title>

    {{-- Fonts / Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          referrerpolicy="no-referrer" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #0f172a;
            color: #fff;
        }

        /* outer gradient card like your screenshot */
        .page-shell {
            min-height: 100vh;
            padding: 12px;
            background: url('') no-repeat center/cover; /* optional bg behind card */
        }

        .page-card {
            min-height: calc(100vh - 24px);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.6);
            display: flex;
            flex-direction: column;
        }

        /* ===== NAVBAR (DARK TOP BAR) ===== */
        .main-nav {
            background: #1e2139;
            color: #fff;
            box-shadow: 0 12px 40px -10px rgba(0,0,0,0.6);
            border-bottom: 1px solid rgba(255,255,255,.07);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            font-family: inherit;
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

        /* BRAND */
        .brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            text-decoration: none;
            color: #fff;
        }
        .brand-icon {
            background: linear-gradient(135deg,#6366f1 0%,#8b5cf6 100%);
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

        /* DESKTOP LINKS */
        .nav-links {
            display: none; /* mobile hidden */
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
            background: rgba(255,255,255,0);
            padding: .5rem .75rem;
            border-radius: .5rem;
            transition: all .15s;
        }
        .nav-links li a:hover {
            background: rgba(255,255,255,0.07);
            color: #fff;
        }

        /* USER AREA */
        .user-area {
            position: relative;
        }
        .user-toggle {
            display: flex;
            align-items: center;
            gap: .6rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
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
            background: linear-gradient(135deg,#4f46e5 0%,#312e81 100%);
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

        /* dropdown */
        .user-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + .5rem);
            min-width: 180px;
            background: #fff;
            color: #111;
            border-radius: .75rem;
            box-shadow: 0 30px 70px -10px rgba(0,0,0,.55);
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

        /* GUEST BUTTONS */
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
            border: 1px solid rgba(255,255,255,.15);
            background: rgba(255,255,255,.05);
            color: #fff;
            text-decoration: none;
            transition: all .15s;
        }
        .auth-btn:hover {
            background: rgba(255,255,255,.12);
        }
        .register-btn {
            background: linear-gradient(135deg,#6366f1 0%,#8b5cf6 100%);
            border: 0;
        }

        /* MOBILE MENU BUTTON */
        .mobile-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            font-size: 1rem;
            color: #fff;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: .6rem;
            cursor: pointer;
        }

        /* MOBILE PANEL */
        .mobile-panel {
            display: none;
            background: #1e2139;
            border-top: 1px solid rgba(255,255,255,.07);
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
            background: rgba(255,255,255,0);
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
            background: rgba(255,255,255,.07);
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

        /* PAGE INNER CONTENT AREA */
        .page-body {
            flex: 1;
            width: 100%;
            max-width: 1280px;
            margin: 2rem auto;
            padding: 0 1.25rem 3rem;
            color: #1e1e2f;
        }

        /* header slot styling */
        .page-header {
            background: rgba(255,255,255,0.2);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: .75rem;
            padding: 1rem 1.25rem;
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.4;
            margin-bottom: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            backdrop-filter: blur(6px);
        }

        /* alert styles (success / error flash) */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            font-size: .9rem;
            font-weight: 500;
            color: #111;
            box-shadow: 0 20px 40px rgba(0,0,0,.15);
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

        /* card style for forms (white boxes from Breeze) */
        .panel-card {
            background: #fff;
            color: #1e1e2f;
            border-radius: .75rem;
            box-shadow: 0 30px 60px rgba(0,0,0,.25);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.25rem;
            max-width: 40rem;
        }

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
</head>
<body>

    {{-- NAVBAR --}}
    @php
        $user = Auth::user();
        $initial = strtoupper(substr($user?->name ?? 'U', 0, 1));
    @endphp

    <nav class="main-nav">
        <div class="nav-container">

            {{-- LEFT BRAND --}}
            <a href="{{ route('tasks.index') }}" class="brand">
                <div class="brand-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="brand-text">
                    <span class="brand-title">Hassam Todo</span>
                    <span class="brand-subtitle">Task Manager</span>
                </div>
            </a>

            {{-- MOBILE BURGER --}}
            <button class="mobile-toggle" id="mobileToggleBtn" aria-label="Toggle menu" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>

            {{-- CENTER LINKS --}}
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

            {{-- RIGHT USER / AUTH --}}
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

                    <a href="{{ route('profile.edit') }}#password" class="dropdown-item">
                        <i class="fas fa-lock"></i>
                        <span>Change Password</span>
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

        {{-- MOBILE PANEL --}}
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

    {{-- PAGE BODY (gradient background card content) --}}
    <div class="page-shell">
        <div class="page-card">

            <div class="page-body">
                {{-- global flash messages --}}
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

                {{-- header slot from <x-slot name="header"> --}}
                @isset($header)
                    <div class="page-header">
                        {{ $header }}
                    </div>
                @endisset

                {{-- MAIN PAGE CONTENT from the component slot --}}
                {{ $slot }}

            </div>
        </div>
    </div>

    <script>
        // mobile nav open/close
        function toggleMobileMenu() {
            var panel = document.getElementById('mobilePanel');
            if (!panel) return;
            panel.style.display = (panel.style.display === 'block') ? 'none' : 'block';
        }

        // user dropdown open/close
        function toggleUserDropdown() {
            var menu = document.getElementById('userDropdownMenu');
            if (!menu) return;
            var isOpen = menu.style.display === 'block';
            closeAllDropdowns();
            menu.style.display = isOpen ? 'none' : 'block';
        }

        function closeAllDropdowns() {
            var menus = document.querySelectorAll('.user-dropdown');
            menus.forEach(function(m){ m.style.display = 'none'; });
        }

        document.addEventListener('click', function (e) {
            var userBtn = document.getElementById('userToggleBtn');
            var userMenu = document.getElementById('userDropdownMenu');

            if (userMenu && userBtn) {
                if (!userBtn.contains(e.target) && !userMenu.contains(e.target)) {
                    userMenu.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>
