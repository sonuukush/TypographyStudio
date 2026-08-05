<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Typography Studio')) — Typography Studio</title>
    <meta name="description" content="@yield('description', 'Typography Studio — Create stunning animated typography videos instantly.')">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&family=Poppins:wght@400;600;700;900&family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,700;0,9..144,900;1,9..144,400&family=Dancing+Script:wght@400;600;700&family=Cinzel:wght@400;600;700&family=Cinzel+Decorative:wght@400;700&family=Archivo+Black&family=Oswald:wght@400;500;700&family=Bebas+Neue&family=Courier+Prime:ital,wght@0,400;0,700;1,400&family=Caveat:wght@400;500;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- GSAP --}}
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="min-h-screen antialiased" style="background:#080810;color:#fff;">
    {{-- Navigation --}}
    <nav class="nav-glass" style="position:fixed;top:0;left:0;right:0;z-index:50;">
        <div class="max-w-7xl mx-auto px-4" style="padding-left:1rem;padding-right:1rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;height:64px;">
                {{-- Logo --}}
                <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                    <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#7c3aed,#ec4899);display:flex;align-items:center;justify-content:center;">
                        <svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h6"/>
                        </svg>
                    </div>
                    <span style="font-family:'Fraunces',serif;font-weight:800;font-size:1.05rem;color:#fff;">Typography<span class="text-gradient">Studio</span></span>
                </a>

                {{-- Nav links --}}
                <div style="display:flex;align-items:center;gap:8px;">
                    @auth
                        <a href="{{ route('app') }}" style="font-size:0.85rem;color:rgba(255,255,255,0.6);text-decoration:none;padding:6px 12px;transition:color 0.15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Studio</a>
                        <a href="{{ route('downloads') }}" style="font-size:0.85rem;color:rgba(255,255,255,0.6);text-decoration:none;padding:6px 12px;transition:color 0.15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Downloads</a>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;margin:0;">
                            @csrf
                            <button type="submit" class="btn-ghost" style="font-size:0.82rem;padding:6px 14px;cursor:pointer;">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" style="font-size:0.85rem;color:rgba(255,255,255,0.6);text-decoration:none;padding:6px 12px;">Login</a>
                        <a href="{{ route('register') }}" class="btn-primary" style="font-size:0.82rem;padding:7px 16px;">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Main content --}}
    <main style="padding-top:64px;">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
