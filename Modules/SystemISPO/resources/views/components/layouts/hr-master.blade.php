<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'HR Dashboard' }} - HR & ISPO System</title>

    {{-- Google Fonts - Premium Combo --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|outfit:600,700,800" rel="stylesheet" />

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            /* Enhanced Color Palette */
            --hr-primary: #6366f1;
            --hr-primary-light: #818cf8;
            --hr-primary-dark: #4f46e5;
            --hr-accent: #10b981;
            --hr-sidebar-bg-start: #0f172a;
            --hr-sidebar-bg-end: #020617;
            --hr-body-bg: #f8fafc;
            --hr-card-bg: #ffffff;
            --hr-text-base: #1e293b;
            --hr-text-muted: #64748b;
            --hr-border: #e2e8f0;
            --hr-border-light: rgba(226, 232, 240, 0.6);
            
            /* Sizing & Transitions */
            --radius-xl: 24px;
            --radius-lg: 16px;
            --radius-md: 12px;
            --sidebar-width: 280px;
            --header-height: 84px;
            --transition-fast: all 0.2s ease;
            --transition-bounce: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; outline: none !important; }

        body {
            font-family: 'Manrope', sans-serif;
            background-color: var(--hr-body-bg);
            background-image: radial-gradient(at 100% 0%, hsla(240,100%,98%,1) 0, transparent 50%);
            color: var(--hr-text-base);
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
        }

        /* Hide Module Hub FAB */
        .module-hub-fab { display: none !important; }

        /* Elegant Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; border: 2px solid var(--hr-body-bg); }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* --- SIDEBAR --- */
        .hr-sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--hr-sidebar-bg-start) 0%, var(--hr-sidebar-bg-end) 100%);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            color: #fff;
            padding: 28px 20px;
            box-shadow: 4px 0 24px rgba(2, 6, 23, 0.1);
            transition: var(--transition-bounce);
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 0 8px 40px;
        }

        .brand-icon {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, var(--hr-primary), #a855f7);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 20px;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4), inset 0 2px 4px rgba(255,255,255,0.3);
            transition: var(--transition-bounce);
        }
        .sidebar-brand:hover .brand-icon { transform: rotate(-10deg) scale(1.05); }

        .brand-text {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #ffffff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-section { margin-bottom: 32px; }
        .nav-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: #475569;
            padding: 0 12px 14px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 14px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: var(--transition-fast);
            margin-bottom: 6px;
            position: relative;
            overflow: hidden;
        }

        .nav-item i { font-size: 18px; width: 24px; text-align: center; transition: var(--transition-fast); }
        
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.04); transform: translateX(4px); }
        
        .nav-item.active {
            background: rgba(99, 102, 241, 0.15);
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(99, 102, 241, 0.2);
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--hr-primary-light);
            box-shadow: 0 0 12px var(--hr-primary);
        }
        .nav-item.active i { color: var(--hr-primary-light); }

        .sidebar-user {
            margin-top: auto;
            padding-top: 24px;
            border-top: 1px dashed rgba(255,255,255,0.1);
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 20px;
            transition: var(--transition-fast);
            backdrop-filter: blur(10px);
        }
        .user-pill:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.1); }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 16px;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.1);
        }

        /* --- MAIN CONTENT --- */
        .hr-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }

        .hr-header {
            height: var(--header-height);
            padding: 0 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .hr-breadcrumb { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: var(--hr-text-muted); }
        .hr-breadcrumb a { text-decoration: none; color: inherit; padding: 6px 10px; border-radius: 8px; transition: var(--transition-fast); }
        .hr-breadcrumb a:hover { background: var(--hr-border); color: var(--hr-primary-dark); }
        .hr-breadcrumb .separator { font-size: 10px; color: #cbd5e1; }
        .hr-breadcrumb .current { color: var(--hr-text-base); font-weight: 800; padding: 6px 10px; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid var(--hr-border); }

        .header-actions { display: flex; align-items: center; gap: 20px; }

        /* Dynamic Search Bar */
        .search-wrapper { position: relative; }
        .search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); font-size: 14px; color: #94a3b8; pointer-events: none; transition: var(--transition-fast); }
        .search-input {
            width: 240px;
            height: 44px;
            border-radius: 100px;
            border: 1px solid var(--hr-border);
            background: #fff;
            padding: 0 20px 0 44px;
            font-size: 13px;
            font-weight: 500;
            color: var(--hr-text-base);
            transition: var(--transition-bounce);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);
        }
        .search-input:focus {
            width: 340px;
            border-color: var(--hr-primary-light);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            background: #fff;
        }
        .search-input:focus + .search-icon { color: var(--hr-primary); }

        .action-btn {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            border: 1px solid var(--hr-border);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--hr-text-muted);
            cursor: pointer;
            transition: var(--transition-bounce);
            position: relative;
        }
        .action-btn:hover {
            border-color: var(--hr-primary-light);
            color: var(--hr-primary);
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.12);
            transform: translateY(-2px);
        }
        .action-btn .badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .module-switcher {
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--hr-primary), #8b5cf6);
            color: #fff;
            text-decoration: none;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition-bounce);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .module-switcher:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4); filter: brightness(1.1); }

        .hr-content { padding: 40px; max-width: 1440px; margin: 0 auto; width: 100%; animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- REFINED COMPONENTS --- */
        .hr-card {
            background: var(--hr-card-bg);
            border-radius: var(--radius-xl);
            border: 1px solid rgba(255,255,255,0.8);
            box-shadow: 0 10px 40px rgba(0,0,0,0.03), inset 0 0 0 1px var(--hr-border-light);
            overflow: hidden;
            transition: var(--transition-fast);
        }

        .card-inner-header {
            padding: 24px 32px;
            border-bottom: 1px solid var(--hr-border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(to right, #ffffff, #f8fafc);
        }

        .card-inner-title { font-family: 'Outfit', sans-serif; font-size: 17px; font-weight: 800; color: var(--hr-text-base); display: flex; align-items: center; gap: 12px; }
        .card-inner-body { padding: 32px; }

        .premium-input {
            width: 100%;
            padding: 14px 18px;
            background: #f8fafc;
            border: 1.5px solid var(--hr-border);
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 14px;
            color: var(--hr-text-base);
            transition: var(--transition-fast);
        }
        .premium-input:focus {
            background: #fff;
            border-color: var(--hr-primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 1024px) {
            .hr-sidebar { left: -var(--sidebar-width); }
            .hr-sidebar.mobile-open { left: 0; box-shadow: 20px 0 50px rgba(0,0,0,0.4); }
            .hr-main { margin-left: 0; }
            .hr-header { padding: 0 24px; }
            .hr-content { padding: 24px; }
            .search-input { width: 44px; padding: 0; color: transparent; cursor: pointer; }
            .search-input:focus { width: 200px; padding: 0 16px 0 44px; color: var(--hr-text-base); cursor: text; }
            .module-switcher span { display: none; } /* Hide text on mobile, show icon only */
            .module-switcher { padding: 10px; border-radius: 12px; }
        }

    </style>
    @stack('styles')
</head>
<body>
    @include('components.impersonation-banner')

    <div class="hr-main">
        <header class="hr-header">
            <div class="hr-breadcrumb hidden md:flex">
                <a href="{{ route('modules.index') }}"><i class="fas fa-home opacity-70"></i></a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <a href="{{ route('hr.dashboard') }}">HR Unit</a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <span class="current">{{ $title ?? 'Dashboard' }}</span>
            </div>

            <div class="header-actions">
                {{-- Dynamic Search --}}
                <div class="search-wrapper hidden md:block">
                    <input type="text" class="search-input" placeholder="Cari menu, data pegawai...">
                    <i class="fas fa-search search-icon"></i>
                </div>

                {{-- Premium Action Button --}}
                <div class="action-btn" title="Notifikasi">
                    <i class="fas fa-bell"></i>
                    <span class="badge"></span>
                </div>

                {{-- Floating Module Switcher --}}
                <a href="{{ route('modules.index') }}" class="module-switcher">
                    <i class="fas fa-th-large"></i> <span>Modul Lain</span>
                </a>
            </div>
        </header>

        <main class="hr-content">
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')
</body>
</html>