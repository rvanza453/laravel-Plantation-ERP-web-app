<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - ERP Plantation Saraswanti</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <style>
        /* Elegant Reset */
        .main-content { margin-left: 0 !important; }
        .sidebar, .top-bar { display: none !important; }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, hsla(210,100%,98%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(215,25%,90%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(210,100%,98%,1) 0, transparent 50%);
            overflow: hidden;
            position: relative;
            font-family: 'Manrope', system-ui, sans-serif;
        }

        .login-container {
            position: relative;
            margin-top: 50px;
        }

        /* PROFESSIONAL CARD */
        .login-card {
            width: 440px;
            background: #ffffff;
            border-radius: 24px;
            padding: 45px 50px;
            box-shadow: 
                0 10px 25px -3px rgba(15, 23, 42, 0.05),
                0 20px 35px -5px rgba(15, 23, 42, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
            z-index: 20; 
        }

        /* BRANDING AREA */
        .brand-container {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #1f7a45, #145c32);
            color: #ffffff;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(31, 122, 69, 0.25);
            flex-shrink: 0;
        }
        .brand-text {
            font-size: 19px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }
        .brand-text span {
            color: #1f7a45;
            font-weight: 600;
            margin-left: 4px;
        }
        .brand-subtitle {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-top: 4px;
            display: block;
        }

        /* =========================================
           MINION CORE STYLES 
           ========================================= */
        @keyframes breathe {
            0%, 100% { transform: scaleY(1) translateY(0); }
            50% { transform: scaleY(1.02) translateY(-1px); }
        }

        .minion-body {
            width: 85px;
            height: 125px;
            background: #ffdb00;
            border-radius: 45px 45px 20px 20px;
            position: relative;
            box-shadow: inset -8px -8px 15px rgba(0,0,0,0.1), 0 10px 15px rgba(0,0,0,0.15);
            transform-style: preserve-3d;
            transition: transform 0.2s cubic-bezier(0.25, 1.5, 0.5, 1);
            animation: breathe 3s infinite ease-in-out;
            transform-origin: bottom center;
        }

        .hair-tuft {
            position: absolute;
            top: -12px;
            left: 50%;
            width: 2px;
            height: 16px;
            background: #333;
            transform: translateX(-50%);
            border-radius: 2px;
        }
        .hair-tuft::before, .hair-tuft::after {
            content: '';
            position: absolute;
            width: 2px;
            height: 12px;
            background: #333;
            top: 4px;
            border-radius: 2px;
        }
        .hair-tuft::before { transform: rotate(-30deg); left: -5px; }
        .hair-tuft::after { transform: rotate(30deg); right: -5px; }
        
        .goggles {
            position: absolute;
            top: 25px;
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 0px;
            transition: transform 0.3s ease;
            z-index: 3;
        }
        .goggle-strap {
            position: absolute;
            top: 20px;
            width: 104%;
            left: -2%;
            height: 10px;
            background: #222;
            z-index: 1;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .goggle-frame {
            width: 44px;
            height: 44px;
            background: #cbd5e1;
            border: 4px solid #64748b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.25), inset 0 2px 4px rgba(255,255,255,0.8);
            position: relative;
            z-index: 2;
            overflow: hidden;
        }
        
        .goggle-frame::before {
            content: '';
            position: absolute;
            top: -110%;
            left: -10%;
            width: 120%;
            height: 120%;
            background: #ffdb00;
            z-index: 10;
            border-bottom: 2px solid rgba(0,0,0,0.2);
            animation: eyelidBlink 5s infinite;
        }
        @keyframes eyelidBlink {
            0%, 94%, 98%, 100% { top: -110%; }
            96% { top: -45%; }
        }
        
        .eye {
            width: 32px;
            height: 32px;
            background: radial-gradient(circle at 10px 10px, #fff, #f8fafc);
            border-radius: 50%;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 4px rgba(0,0,0,0.1);
        }
        .pupil {
            width: 12px;
            height: 12px;
            background: #333;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -6px;
            margin-left: -6px;
            transition: transform 0.08s ease-out;
            box-shadow: inset -2px -2px 0 rgba(0,0,0,0.5);
        }
        .pupil::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 4px;
            height: 4px;
            background: #fff;
            border-radius: 50%;
        }

        .mouth {
            position: absolute;
            top: 76px;
            left: 50%;
            transform: translateX(-50%);
            width: 18px;
            height: 6px;
            border-radius: 0 0 15px 15px;
            border-bottom: 2px solid rgba(0,0,0,0.5); 
            border-left: 1px solid transparent;
            border-right: 1px solid transparent;
            box-sizing: border-box;
            background: transparent;
            transition: all 0.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 2;
        }
        
        .animate-jump .mouth, .animate-cheer .mouth, .talking .mouth, .dizzy .mouth {
            width: 14px;
            height: 16px;
            background: #7a2323;
            border-radius: 50%;
            top: 72px;
            border: 2px solid rgba(0,0,0,0.6);
        }
        
        .password-active .mouth, .scared .mouth {
            width: 10px;
            height: 10px;
            background: #7a2323;
            border-radius: 50%;
            top: 75px;
            border: 2px solid rgba(0,0,0,0.6);
        }
        
        .animate-jump .mouth::after, .animate-cheer .mouth::after, .talking .mouth::after, .dizzy .mouth::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 8px;
            height: 6px;
            background: #ef4444;
            border-radius: 10px 10px 0 0;
        }

        .overalls {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 38px;
            background: #2563eb;
            border-radius: 0 0 20px 20px;
            box-shadow: inset 0 4px 6px rgba(0,0,0,0.1);
            z-index: 3;
            overflow: hidden;
        }
        .overalls::before, .overalls::after {
            content: '';
            position: absolute;
            top: -15px;
            width: 12px;
            height: 28px;
            background: #1e40af;
            border-radius: 4px;
        }
        .overalls::before { left: 10px; transform: rotate(-15deg); }
        .overalls::after { right: 10px; transform: rotate(15deg); }

        .pocket {
            position: absolute;
            top: 6px;
            left: 50%;
            width: 24px;
            height: 22px;
            background: #1d4ed8;
            border-radius: 0 0 12px 12px;
            transform: translateX(-50%);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
        }
        .pocket::after {
            content: 'G';
            font-size: 10px;
            color: #bfdbfe;
            position: absolute;
            top: 40%; left: 50%;
            transform: translate(-50%, -50%);
            font-weight: 900;
        }

        .arm {
            position: absolute;
            top: 70px;
            width: 12px;
            height: 36px;
            background: #ffdb00;
            left: -10px;
            transform: rotate(20deg);
            border-radius: 6px;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            transform-origin: top center;
            z-index: 10;
            box-shadow: inset -2px -2px 4px rgba(0,0,0,0.1);
        }
        .arm.right { left: auto; right: -10px; transform: rotate(-20deg); }
        .glove {
            position: absolute;
            bottom: -6px;
            left: -2px;
            width: 16px;
            height: 16px;
            background: #222;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .glove::after {
            content:'';
            position: absolute;
            top: 2px;
            right: -2px;
            width: 6px;
            height: 10px;
            background: #222;
            border-radius: 4px;
            transform: rotate(30deg);
        }
        .arm.right .glove::after { left: -2px; transform: rotate(-30deg); }

        /* SITTING MINION SPECIFIC */
        .minion-sitting {
            position: absolute;
            top: -110px;
            right: 40px; 
            z-index: 10; 
            width: 110px;
            height: 120px;
            perspective: 800px;
            cursor: pointer;
            /* Added 'right' transition so he elegantly walks across card during idle */
            transition: right 1.5s cubic-bezier(0.34, 1.15, 0.64, 1), transform 0.6s cubic-bezier(0.34, 1.15, 0.64, 1);
        }
        .leg-sitting {
            position: absolute;
            bottom: -15px;
            width: 16px;
            height: 22px;
            background: #1e40af;
            left: 18px;
            border-radius: 0 0 6px 6px;
            z-index: 1;
        }
        .leg-sitting.right { left: auto; right: 18px; }
        .shoe {
            position: absolute;
            bottom: -6px;
            width: 24px;
            height: 12px;
            background: #111;
            border-radius: 12px 12px 0 0;
            left: -4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.4);
        }
        
        .minion-sitting-shadow {
            position: absolute;
            bottom: -18px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 12px;
            background: rgba(0,0,0,0.15);
            border-radius: 50%;
            z-index: 0;
            filter: blur(2px);
            transition: all 0.3s;
        }

        /* MINION INTERACTION: PASSWORD COVER EYES & HIDE */
        .password-active {
            transform: translateY(90px) scale(0.95);
        }
        .password-active .minion-body .arm {
            transform: translate(4px, -4px) rotate(-145deg);
            height: 52px;
            z-index: 20;
        }
        .password-active .minion-body .arm.right {
            transform: translate(-4px, -4px) rotate(145deg);
            height: 52px;
        }

        .minion-sitting.peeking {
            transform: translateY(45px) scale(0.98); 
        }
        .minion-sitting.peeking .minion-body .arm {
            transform: translate(8px, 0px) rotate(-115deg);
            height: 45px;
        }
        .minion-sitting.peeking .minion-body .arm.right {
            transform: translate(-8px, 0px) rotate(115deg);
            height: 45px;
        }

        /* Kevin Walking top of card animation logic */
        @keyframes legSwingL { 0%, 100% { transform: rotate(-25deg); } 50% { transform: rotate(25deg); } }
        @keyframes legSwingR { 0%, 100% { transform: rotate(25deg); } 50% { transform: rotate(-25deg); } }
        
        .walking-top .leg-sitting { animation: legSwingL 0.4s infinite linear; }
        .walking-top .leg-sitting.right { animation: legSwingR 0.4s infinite linear; }
        .walking-top .minion-body { animation: breathe 1s infinite ease-in-out, walkBounce 0.2s infinite alternate ease-in-out !important; }

        /* WALKING MINION (STUART) */
        .minion-walking-container {
            position: fixed;
            bottom: 20px;
            left: -150px;
            z-index: 50;
            cursor: pointer;
            animation: walkAround 25s linear infinite;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        .minion-walking {
            transform: scale(0.7);
            transform-origin: bottom center;
            position: relative;
        }
        .minion-walking .minion-body {
            width: 92px;
            height: 115px;
            border-radius: 46px 46px 20px 20px;
            animation: breathe 2s infinite ease-in-out, walkBounce 0.4s infinite alternate ease-in-out;
        }
        @keyframes walkBounce {
            0% { transform: scaleY(1) translateY(0); }
            100% { transform: scaleY(0.98) translateY(-6px); }
        }

        .minion-walking .goggles { justify-content: center; gap: 0; }
        .minion-walking .goggle-frame { width: 48px; height: 48px; border-width: 5px; }
        .minion-walking .goggle-frame:nth-child(2) { display: none; }
        .minion-walking .eye { width: 34px; height: 34px; }
        .minion-walking .pupil { width: 14px; height: 14px; margin-top:-7px; margin-left:-7px; }

        .leg-walking {
            position: absolute;
            bottom: -22px;
            width: 16px;
            height: 28px;
            background: #1e40af;
            left: 24px;
            transform-origin: top center;
            z-index: 1;
            border-radius: 0 0 4px 4px;
        }
        .leg-walking.right { left: auto; right: 24px; }
        .minion-walking .shoe { bottom: -6px; height: 12px; width: 24px; }
        
        .minion-walking-shadow {
            position: absolute;
            bottom: -28px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 8px;
            background: rgba(0,0,0,0.15);
            border-radius: 50%;
            filter: blur(2px);
            animation: shadowBounce 0.4s infinite alternate ease-in-out;
        }
        @keyframes shadowBounce {
            0% { transform: translateX(-50%) scale(1); opacity: 0.8; }
            100% { transform: translateX(-50%) scale(0.8); opacity: 0.4; }
        }

        @keyframes walkAround {
            0% { left: -150px; transform: scaleX(1); }
            48% { left: 110vw; transform: scaleX(1); }
            50% { left: 110vw; transform: scaleX(-1); }
            98% { left: -150px; transform: scaleX(-1); }
            100% { left: -150px; transform: scaleX(1); }
        }
        
        .minion-walking .leg-walking { animation: legSwingL 0.8s infinite linear; }
        .minion-walking .leg-walking.right { animation: legSwingR 0.8s infinite linear; }
        @keyframes armSwing { 0%, 100% { transform: rotate(20deg); } 50% { transform: rotate(-20deg); } }
        .minion-walking .arm { animation: armSwing 0.8s infinite linear alternate-reverse; }
        .minion-walking .arm.right { animation: armSwing 0.8s infinite linear alternate; }

        /* Dialog Bubble */
        .dialog-bubble {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            padding: 10px 16px;
            border-radius: 18px;
            font-size: 14px;
            font-weight: 800;
            color: #1e293b;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            position: relative;
            transform: translateY(10px) scale(0.8);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            white-space: nowrap;
            pointer-events: none;
        }
        .dialog-bubble.show { opacity: 1; transform: translateY(0) scale(1); }
        .dialog-bubble::after {
            content: ''; position: absolute; bottom: -8px; left: 50%; margin-left: -8px;
            border-width: 8px 8px 0; border-style: solid; border-color: #e2e8f0 transparent transparent transparent;
        }
        .dialog-bubble::before {
            content: ''; position: absolute; bottom: -5px; left: 50%; margin-left: -6px;
            border-width: 6px 6px 0; border-style: solid; border-color: #fff transparent transparent transparent;
            z-index: 2;
        }

        /* Jumps, Cheers, Dizzy */
        @keyframes happy-jump {
            0%, 100% { transform: translateY(0) scale(1); }
            40% { transform: translateY(-40px) scaleY(1.05) rotate(5deg); }
            60% { transform: translateY(-40px) scaleY(1.05) rotate(-5deg); }
        }
        .animate-jump { animation: happy-jump 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .animate-jump ~ .minion-sitting-shadow { display: none; }

        @keyframes cheering {
            0%, 100% { transform: scale(1) rotate(0); }
            25% { transform: scale(1.05) translateY(-5px) rotate(-5deg); }
            75% { transform: scale(1.05) translateY(-5px) rotate(5deg); }
        }
        .animate-cheer { animation: cheering 0.3s infinite; }
        .animate-cheer .arm { transform: rotate(150deg) translateX(5px); height:45px; }
        .animate-cheer .arm.right { transform: rotate(-150deg) translateX(-5px); height:45px; }

        @keyframes getting-dizzy {
            0%, 100% { transform: rotate(0deg); filter: blur(0px); }
            25% { transform: rotate(-15deg); filter: blur(0.5px); }
            75% { transform: rotate(15deg); filter: blur(0.5px); }
        }
        .dizzy { animation: getting-dizzy 0.3s infinite; }
        .dizzy .eye { animation: dizzyEye 0.2s infinite; }
        @keyframes dizzyEye {
            0% { transform: translate(-2px, -2px); }
            50% { transform: translate(2px, 2px); }
            100% { transform: translate(-2px, 2px); }
        }

        /* Used to lift Stuart up when dragging */
        .drag-lift {
            transform: scale(1.15) translateY(-30px);
            z-index: 100;
        }

        /* FORM STYLES */
        .login-header { margin-bottom: 30px; }
        .login-title { font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
        .login-subtitle { color: #64748b; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; }
        .form-control { width: 100%; padding: 14px 16px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 15px; transition: all 0.3s ease; }
        .form-control:focus { outline: none; border-color: #1f7a45; background: #fff; box-shadow: 0 0 0 4px rgba(31, 122, 69, 0.1); }
        .btn-login { width: 100%; padding: 14px; background: linear-gradient(135deg, #1e293b, #0f172a); color: #fff; border: none; border-radius: 12px; font-weight: 700; font-size: 15px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2); }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(15, 23, 42, 0.3); }
        .alert-error { background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; }
        
        .login-footer { margin-top: 32px; padding-top: 20px; border-top: 1px solid rgba(226, 232, 240, 0.8); display: flex; justify-content: center; }
        .credit-bottom { color: #94a3b8; font-size: 13px; display: flex; align-items: center; gap: 5px; }
        .credit-bottom i { color: #f59e0b; font-size: 12px; animation: pulse-icon 2s infinite; }
        .creator-link { position: relative; color: #475569; font-weight: 700; text-decoration: none; transition: color 0.3s ease; }
        .creator-link::after { content: ''; position: absolute; width: 100%; height: 2px; bottom: -2px; left: 0; background: linear-gradient(90deg, #1f7a45, #10b981); transform: scaleX(0); transform-origin: right; transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1); border-radius: 2px; }
        .creator-link:hover { color: #1f7a45; }
        .creator-link:hover::after { transform: scaleX(1); transform-origin: left; }
        @keyframes pulse-icon { 0% { transform: scale(1); opacity: 0.8; } 50% { transform: scale(1.2); opacity: 1; } 100% { transform: scale(1); opacity: 0.8; } }

        @media (max-width: 500px) {
            .login-card { width: 90vw; padding: 35px 25px; }
            .minion-sitting { right: 20px !important; transform: scale(0.8); top: -90px; }
            .brand-text { font-size: 17px; }
            .minion-walking-container { display: none; }
        }
    </style>

    <div class="login-page">
        <div class="login-container">
            {{-- MINION 1: SITTING & HIDING --}}
            <div class="minion-sitting" id="minionContainer" title="Klik aku!">
                <div class="hair-tuft"></div>
                <div class="minion-body" id="minionBody">
                    <div class="goggle-strap"></div>
                    <div class="goggles">
                        <div class="goggle-frame"><div class="eye"><div class="pupil"></div></div></div>
                        <div class="goggle-frame"><div class="eye"><div class="pupil"></div></div></div>
                    </div>
                    <div class="mouth"></div>
                    <div class="overalls">
                        <div class="pocket"></div>
                    </div>
                    <div class="arm">
                        <div class="glove"></div>
                    </div>
                    <div class="arm right">
                        <div class="glove"></div>
                    </div>
                    <div class="leg-sitting">
                        <div class="shoe"></div>
                    </div>
                    <div class="leg-sitting right">
                        <div class="shoe"></div>
                    </div>
                </div>
                <div class="minion-sitting-shadow"></div>
            </div>

            <div class="login-card">
                <div class="brand-container">
                    <div class="brand-icon"><i class="fas fa-seedling"></i></div>
                    <div>
                        <div class="brand-text">Saraswanti Plantation<span>ERP</span></div>
                        <span class="brand-subtitle">Divisi Kebun</span>
                    </div>
                </div>

                <div class="login-header">
                    <h1 class="login-title">Selamat Datang</h1>
                    <p class="login-subtitle">Akses sentralisasi data dan modul operasional kebun.</p>
                </div>

                @if($errors->any())
                    <div class="alert-error">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login.submit') }}" method="POST" id="loginForm">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Email atau Username</label>
                        <input type="text" name="login" id="loginField" class="form-control" value="{{ old('login') }}" placeholder="user@saraswanti.test / username" required autofocus>
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" id="passwordField" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #64748b; cursor: pointer; font-weight: 500;">
                            <input type="checkbox" name="remember" style="width: 16px; height: 16px; accent-color: #1f7a45; border-radius: 4px;">
                            Ingat sesi saya
                        </label>
                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">
                        <span id="btnText">Login ke Sistem</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <div class="login-footer">
                    <div class="credit-bottom">
                        Diracik dengan <i class="fas fa-bolt"></i> oleh 
                        <a href="https://github.com/rvanza453" class="creator-link" target="_blank" rel="noopener noreferrer">Muhammad Revanza</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- MINION 2: WALKING (STUART) --}}
        <div class="minion-walking-container" title="Bello!">
            <div class="dialog-bubble" id="stuartDialog">Poopaye! 👋</div>
            <div class="minion-walking" id="walkingMinionWrap">
                <div class="hair-tuft"></div>
                <div class="minion-body">
                    <div class="goggle-strap"></div>
                    <div class="goggles">
                        <div class="goggle-frame"><div class="eye"><div class="pupil"></div></div></div>
                        <div class="goggle-frame" style="display:none;"></div> 
                    </div>
                    <div class="mouth"></div>
                    <div class="overalls">
                        <div class="pocket"></div>
                    </div>
                    <div class="arm">
                        <div class="glove"></div>
                    </div>
                    <div class="arm right">
                        <div class="glove"></div>
                    </div>
                    <div class="leg-walking">
                        <div class="shoe"></div>
                    </div>
                    <div class="leg-walking right">
                        <div class="shoe"></div>
                    </div>
                </div>
                <div class="minion-walking-shadow"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const minionContainer = document.getElementById('minionContainer');
            const minionBody = document.getElementById('minionBody');
            const pupils = document.querySelectorAll('.minion-sitting .pupil');
            const walkingPupil = document.querySelector('.minion-walking .pupil');
            const walkingMinionBody = document.querySelector('.minion-walking .minion-body');
            const walkingContainer = document.querySelector('.minion-walking-container');
            const walkingMinionWrap = document.getElementById('walkingMinionWrap');
            const dialog = document.getElementById('stuartDialog');
            
            const loginField = document.getElementById('loginField');
            const passwordField = document.getElementById('passwordField');
            const loginForm = document.getElementById('loginForm');
            
            let isInteracting = false;
            let isCoveringEyes = false;
            let peekInterval;
            let focusTarget = null;

            // Globals
            let dragK = false;
            let dragS = false;

            // --- IDLE PATROL LOGIC (KEVIN) ---
            let idleTimer = null;
            let isIdle = false;
            let currentPatrolPos = 40; 
            const patrolPoints = [40, 160, 260, 310]; 

            const resetIdleTimer = () => {
                clearTimeout(idleTimer);
                
                // Only touch DOM if state is actually changing to save performance!
                if(isIdle) {
                    isIdle = false;
                    minionContainer.classList.remove('walking-top');
                }
                
                // Keep scheduling idle logic
                if(!isInteracting && !dragK && !isCoveringEyes) {
                    idleTimer = setTimeout(() => triggerIdleMove(), 5000);
                }
            };

            const triggerIdleMove = () => {
                isIdle = true;
                let availableSpots = patrolPoints.filter(p => p !== currentPatrolPos);
                currentPatrolPos = availableSpots[Math.floor(Math.random() * availableSpots.length)];
                
                minionContainer.classList.add('walking-top');
                minionContainer.style.right = currentPatrolPos + 'px';
                
                setTimeout(() => {
                    if(isIdle) {
                        minionContainer.classList.remove('walking-top');
                        idleTimer = setTimeout(() => triggerIdleMove(), 5000 + Math.random() * 3000);
                    }
                }, 1500);
            };

            // Reset idle on major activities
            document.addEventListener('mousemove', resetIdleTimer);
            document.addEventListener('click', resetIdleTimer);
            document.addEventListener('keydown', resetIdleTimer);
            resetIdleTimer(); // Initial boot

            // --- 1. Tickle Physics (Kevin) ---
            let lastTickleTime = Date.now();
            let tickleCount = 0;
            minionContainer.addEventListener('mousemove', (e) => {
                if(isCoveringEyes || dragK) return;
                let now = Date.now();
                if(now - lastTickleTime < 50) {
                    tickleCount++;
                    if(tickleCount > 15) {
                        minionBody.classList.add('animate-cheer'); // Laugh via cheering
                        setTimeout(() => minionBody.classList.remove('animate-cheer'), 400);
                        tickleCount = 0;
                    }
                } else if(now - lastTickleTime > 150) {
                    tickleCount = 0; // decay
                }
                lastTickleTime = now;
            });

            // --- 2. Interactive Drag, Slingshot & Click (Kevin) ---
            dragK = false;
            let kStartX = 0, kStartY = 0;
            let wasDragged = false;
            
            minionContainer.addEventListener('mousedown', (e) => {
                if(isCoveringEyes) return; 
                dragK = true;
                wasDragged = false;
                kStartX = e.clientX;
                kStartY = e.clientY;
                minionBody.classList.add('scared'); 
                document.body.style.cursor = 'grabbing';
            });

            // --- 3. Interactive Pick-up & Drop Dizzyness (Stuart) ---
            dragS = false;
            let sStartX = 0, sStartY = 0;
            let dizzyTimeout = null;
            
            if(walkingContainer) {
                walkingContainer.addEventListener('mousedown', (e) => {
                    dragS = true;
                    sStartX = e.clientX;
                    sStartY = e.clientY;
                    
                    clearTimeout(dizzyTimeout);
                    walkingMinionBody.classList.remove('dizzy');
                    
                    // Stop wandering and lift up
                    walkingContainer.style.animationPlayState = 'paused';
                    walkingContainer.style.transition = 'none';
                    walkingMinionWrap.classList.add('drag-lift');
                    
                    walkingMinionBody.classList.add('scared'); 
                    dialog.textContent = "Aaahh!! 😱";
                    dialog.classList.add('show');
                    
                    // Thrash legs
                    document.querySelectorAll('.minion-walking .leg-walking').forEach(leg => leg.style.animationDuration = '0.08s');
                    document.querySelectorAll('.minion-walking .arm').forEach(arm => arm.style.animationDuration = '0.08s');
                    
                    document.body.style.cursor = 'grabbing';
                });
            }

            // Global Mouse tracking (Follows Cursor & Drag updates)
            document.addEventListener('mousemove', (e) => {
                const x = e.clientX;
                const y = e.clientY;

                if(dragK) {
                    let dx = x - kStartX;
                    let dy = y - kStartY;
                    if(Math.abs(dx) > 5 || Math.abs(dy) > 5) wasDragged = true; // Mark as actual drag, not a click
                    minionContainer.style.transition = 'none';
                    minionContainer.style.transform = `translate(${dx * 0.5}px, ${dy * 0.5}px) rotate(${dx * 0.05}deg)`;
                    return; 
                }

                if(dragS && walkingContainer) {
                    let dx = x - sStartX;
                    let dy = y - sStartY;
                    walkingContainer.style.transform = `translate(${dx}px, ${dy}px) rotate(${dx * 0.03}deg)`;
                    return;
                }

                // Eye tracking calculations
                let targetX = x;
                let targetY = y;

                if (isInteracting && focusTarget && !isCoveringEyes) {
                    const rect = focusTarget.getBoundingClientRect();
                    targetX = rect.left + rect.width / 2;
                    targetY = rect.top + rect.height / 2;
                }

                let isPeekingActive = minionContainer.classList.contains('peeking');

                // Update SITTING pupils
                if(!isCoveringEyes || isPeekingActive) {
                    pupils.forEach(pupil => {
                        const rect = pupil.parentElement.getBoundingClientRect();
                        const center = { x: rect.left + rect.width / 2, y: rect.top + rect.height / 2 };
                        const angle = Math.atan2(targetY - center.y, targetX - center.x);
                        const distance = Math.hypot(targetX - center.x, targetY - center.y);
                        const maxDistance = distance < 100 ? distance / 15 : 6;
                        /* Explicitly updating transform overrides CSS transitions safely */
                        pupil.style.transform = `translate(${Math.cos(angle) * maxDistance}px, ${Math.sin(angle) * maxDistance}px)`;
                    });
                }

                // Update WALKING pupils
                if(walkingPupil) {
                    const wRect = walkingPupil.parentElement.getBoundingClientRect();
                    const wCenter = { x: wRect.left + wRect.width / 2, y: wRect.top + wRect.height / 2 };
                    const wAngle = Math.atan2(y - wCenter.y, x - wCenter.x);
                    const wDistance = Math.hypot(x - wCenter.x, y - wCenter.y);
                    const wMaxDistance = wDistance < 200 ? wDistance / 20 : 5;
                    walkingPupil.style.transform = `translate(${Math.cos(wAngle) * wMaxDistance}px, ${Math.sin(wAngle) * wMaxDistance}px)`;
                }
            });

            // Global Mouse Release
            document.addEventListener('mouseup', () => {
                document.body.style.cursor = 'default';

                if(dragK) {
                    dragK = false;
                    minionBody.classList.remove('scared');
                    minionContainer.style.transition = 'right 1.5s cubic-bezier(0.34, 1.15, 0.64, 1), transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)'; 
                    minionContainer.style.transform = '';
                    
                    // Allow simple clicks to jump!
                    if(!wasDragged && !isCoveringEyes) {
                        // Small timeout helps ensure CSS re-flow handles animation properly
                        setTimeout(() => {
                            minionBody.classList.add('animate-jump');
                            setTimeout(() => minionBody.classList.remove('animate-jump'), 600);
                        }, 10);
                    }
                }

                if(dragS && walkingContainer) {
                    dragS = false;
                    dialog.textContent = "Oof! 😵‍💫"; 
                    
                    walkingMinionWrap.classList.remove('drag-lift');
                    walkingContainer.style.transition = 'transform 0.4s cubic-bezier(0.4, 1.5, 0.6, 1)';
                    walkingContainer.style.transform = ''; 
                    
                    document.querySelectorAll('.minion-walking .leg-walking').forEach(leg => leg.style.animationDuration = '');
                    document.querySelectorAll('.minion-walking .arm').forEach(arm => arm.style.animationDuration = '');
                    walkingMinionBody.classList.remove('scared');
                    
                    walkingMinionBody.classList.add('dizzy');
                    
                    dizzyTimeout = setTimeout(() => {
                        walkingMinionBody.classList.remove('dizzy');
                        walkingContainer.style.animationPlayState = 'running';
                        dialog.classList.remove('show');
                        walkingContainer.style.transition = '';
                    }, 2000); 
                }
            });

            // Click Dialogs for Stuart (only if not dragged)
            if(walkingContainer) {
                walkingContainer.addEventListener('click', () => {
                    if(!dragS && walkingContainer.style.animationPlayState !== 'paused') { 
                        const messages = ["Bello!", "Poopaye!", "Banana! 🍌", "Look at you!", "Me want banana!"];
                        dialog.textContent = messages[Math.floor(Math.random() * messages.length)];
                        dialog.classList.add('show');
                        walkingMinionBody.classList.add('talking');
                        walkingMinionBody.style.transform = 'scaleY(1.05) translateY(-20px)';
                        
                        setTimeout(() => walkingMinionBody.style.transform = '', 300);

                        clearTimeout(dizzyTimeout);
                        dizzyTimeout = setTimeout(() => {
                            dialog.classList.remove('show');
                            walkingMinionBody.classList.remove('talking');
                        }, 2500);
                    }
                });
            }

            // --- Form Inputs Focus / Blur Logic ---
            loginField.addEventListener('focus', (e) => {
                isInteracting = true;
                focusTarget = e.target;
                minionContainer.classList.remove('password-active', 'peeking');
                isCoveringEyes = false;
                clearInterval(peekInterval);
            });

            passwordField.addEventListener('focus', (e) => {
                isInteracting = true;
                focusTarget = e.target;
                
                minionContainer.classList.add('password-active');
                isCoveringEyes = true;
                
                pupils.forEach(pupil => pupil.style.transform = `translate(0px, 0px)`);
                minionBody.style.transform = 'rotateX(0deg) rotateY(0deg)';
                
                clearInterval(peekInterval);
                peekInterval = setInterval(() => {
                    if(Math.random() > 0.5 && document.activeElement === passwordField) {
                        minionContainer.classList.add('peeking');
                        setTimeout(() => minionContainer.classList.remove('peeking'), 1500);
                    }
                }, 3500);
            });

            const handleBlur = () => {
                if(document.activeElement !== loginField && document.activeElement !== passwordField) {
                    isInteracting = false;
                    focusTarget = null;
                    minionContainer.classList.remove('password-active', 'peeking');
                    isCoveringEyes = false;
                    clearInterval(peekInterval);
                }
            };
            loginField.addEventListener('blur', handleBlur);
            passwordField.addEventListener('blur', handleBlur);

            loginForm.addEventListener('submit', () => {
                minionBody.classList.add('animate-cheer');
                minionContainer.classList.remove('password-active', 'peeking', 'walking-top');
                clearInterval(peekInterval);
                document.getElementById('btnText').innerText = 'Mengautentikasi...';
            });
        });
    </script>
</body>
</html>