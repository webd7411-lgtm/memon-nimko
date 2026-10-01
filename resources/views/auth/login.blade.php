<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>Login | {{ sys_setting('software_name', 'Memon Nimko') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #800000;
            --primary-hover: #9e1414;
            --gold: #D4AF37;
            --gold-light: #F4DF4E;
            --dark: #0e0b0a;
            --dark-card: rgba(18, 14, 12, 0.82);
            --light: #fbf9f5;
            --muted: rgba(255, 255, 255, 0.7);
            --border-gold: rgba(212, 175, 55, 0.28);
            --border-subtle: rgba(255, 255, 255, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            min-height: 100%;
            font-family: 'Outfit', sans-serif;
            background-color: var(--dark);
            color: var(--light);
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Fixed Ambient Bakery Background - Bright, Warm & Clear */
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(0, 0, 0, 0.32), rgba(0, 0, 0, 0.48)),
                url('{{ asset("assets/images/memon_nimko_hero.png") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: 0;
            transform: scale(1.03);
            animation: subtleZoom 25s infinite alternate ease-in-out;
        }

        @keyframes subtleZoom {
            from { transform: scale(1.01); }
            to { transform: scale(1.06); }
        }

        /* Centered Page Layout */
        .login-wrapper {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
        }

        /* Glassmorphism Card */
        .login-card {
            background: rgba(0, 0, 0, 0.52);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            width: 100%;
            max-width: 440px;
            padding: clamp(2rem, 5vw, 2.8rem) clamp(1.4rem, 4vw, 2.4rem);
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6), 0 0 30px rgba(212, 175, 55, 0.08);
            animation: cardFadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
        }

        @keyframes cardFadeUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Brand Header */
        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-header .brand-logo-img {
            max-height: 75px;
            width: auto;
            margin-bottom: 12px;
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.5));
            object-fit: contain;
        }

        .brand-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 4vw, 2.2rem);
            font-weight: 700;
            background: linear-gradient(135deg, #FFFFFF 20%, #F5E6AB 65%, var(--gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.35rem;
            letter-spacing: -0.5px;
        }

        .brand-header h2 span {
            -webkit-text-fill-color: var(--gold);
        }

        .brand-header p {
            color: var(--gold);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.78rem;
            opacity: 0.9;
        }

        /* Alerts */
        .alert-error {
            background: rgba(220, 38, 38, 0.18);
            border: 1px solid rgba(220, 38, 38, 0.35);
            color: #fca5a5;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 0.88rem;
            margin-bottom: 1.4rem;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .alert-error li {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .input-icon-left {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gold);
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-subtle);
            padding: 14px 44px 14px 46px;
            border-radius: 12px;
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-size: 0.96rem;
            transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
            outline: none;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.92rem;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--gold);
            box-shadow: 0 0 16px rgba(212, 175, 55, 0.25);
        }

        /* Password Show/Hide Toggle */
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.5);
            font-size: 1rem;
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .toggle-password:hover {
            color: var(--gold);
        }

        /* Form Row: Remember Me */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.4rem;
            font-size: 0.88rem;
        }

        .remember-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.75);
            cursor: pointer;
            user-select: none;
        }

        .remember-label input[type="checkbox"] {
            accent-color: var(--gold);
            width: 16px;
            height: 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        /* Submit Button */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
            box-shadow: 0 8px 24px rgba(128, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(128, 0, 0, 0.55), 0 0 25px rgba(212, 175, 55, 0.2);
            border-color: rgba(212, 175, 55, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Back to Homepage */
        .back-to-site {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 1.8rem;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .back-to-site i {
            transition: transform 0.3s ease;
        }

        .back-to-site:hover {
            color: var(--gold);
        }

        .back-to-site:hover i {
            transform: translateX(-4px);
        }

        /* Developer Credit */
        .dev-credit {
            text-align: center;
            margin-top: 1.4rem;
            padding-top: 1.1rem;
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.5);
            border-top: 1px dashed rgba(255, 255, 255, 0.12);
        }

        .dev-credit strong {
            color: var(--gold);
            font-weight: 600;
        }

        /* ══════════ RESPONSIVE BREAKPOINTS ══════════ */
        @media (max-width: 480px) {
            .login-wrapper {
                padding: 24px 12px;
            }

            .login-card {
                padding: 2rem 1.25rem 1.6rem;
                border-radius: 20px;
            }

            .brand-header {
                margin-bottom: 1.5rem;
            }

            .brand-header .brand-logo-img {
                max-height: 60px;
                margin-bottom: 8px;
            }

            .brand-header h2 {
                font-size: 1.7rem;
            }

            .brand-header p {
                font-size: 0.72rem;
                letter-spacing: 1.5px;
            }

            .form-control {
                padding: 13px 40px 13px 42px;
                font-size: 16px; /* Prevents auto-zoom in iOS Safari */
                border-radius: 10px;
            }

            .input-icon-left {
                left: 14px;
                font-size: 0.95rem;
            }

            .btn-login {
                padding: 13px;
                font-size: 0.94rem;
                border-radius: 10px;
            }

            .back-to-site {
                margin-top: 1.4rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 360px) {
            .login-card {
                padding: 1.6rem 1rem 1.4rem;
            }

            .brand-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="bg-overlay"></div>

    <div class="login-wrapper">
        <div class="login-card">
            <!-- Brand Header -->
            <div class="brand-header">
                <img src="{{ asset('assets/images/logo.png') }}" class="brand-logo-img" alt="{{ shop_name() }}" onerror="this.style.display='none'">
                <h2>{{ shop_name() }}<span>.</span></h2>
                <p>{{ shop_tagline() }}</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                @if ($errors->any())
                    <ul class="alert-error">
                        @foreach ($errors->all() as $error)
                            <li><i class="fas fa-circle-exclamation"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <!-- Email Input -->
                <div class="form-group">
                    <i class="fas fa-envelope input-icon-left"></i>
                    <input type="email" name="email" class="form-control" placeholder="Email Address" value="{{ old('email') }}" required autofocus autocomplete="email">
                </div>

                <!-- Password Input with Show/Hide Toggle -->
                <div class="form-group">
                    <i class="fas fa-lock input-icon-left"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required autocomplete="current-password">
                    <button type="button" class="toggle-password" id="togglePasswordBtn" aria-label="Toggle password visibility">
                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>

                <!-- Remember Me -->
                <div class="form-options">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-login">
                    <i class="fas fa-right-to-bracket"></i>
                    <span>Sign In</span>
                </button>
            </form>

            <!-- Back to Site Link -->
            <a href="/" class="back-to-site">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Homepage</span>
            </a>

            <!-- Developer Credit -->
            <div class="dev-credit">
                Software Developed by <strong>{{ developer_name() }}</strong>
            </div>
        </div>
    </div>

    <!-- Toggle Password Visibility Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (toggleBtn && passwordInput && toggleIcon) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    toggleIcon.classList.toggle('fa-eye', !isPassword);
                    toggleIcon.classList.toggle('fa-eye-slash', isPassword);
                });
            }
        });
    </script>
</body>
</html>