<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ sys_setting('software_name', 'Memon Nimko') }} | {{ sys_setting('tagline', 'Sweets & Bakers') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #800000;
            --primary-light: #a01515;
            --gold: #D4AF37;
            --gold-light: #F4DF4E;
            --dark: #0e0b0a;
            --light: #ffffff;
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
            scroll-behavior: smooth;
            overflow-x: hidden;
            width: 100%;
        }

        /* ══════════ FULL SCREEN HERO CONTAINER ══════════ */
        .hero-section {
            position: relative;
            min-height: 100vh;
            min-height: 100dvh;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
            padding: 90px 20px 60px;
        }

        /* Fixed Background Bakery Photo */
        .hero-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(0, 0, 0, 0.32), rgba(0, 0, 0, 0.46)),
                url('{{ asset("assets/images/memon_nimko_hero.png") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: 1;
            transform: scale(1.02);
            animation: slowZoom 25s infinite alternate ease-in-out;
        }

        @keyframes slowZoom {
            from { transform: scale(1.0); }
            to { transform: scale(1.05); }
        }

        /* ══════════ FLOATING TOP NAVBAR ══════════ */
        .site-nav {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 1.4rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 30;
        }

        .brand-link {
            text-decoration: none;
            display: inline-block;
        }

        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.35rem, 2.5vw, 1.85rem);
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.85);
        }

        .brand-name span {
            color: var(--gold);
        }

        .nav-actions {
            display: flex;
            align-items: center;
        }

        .nav-login-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.55rem 1.4rem;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--gold);
            background: rgba(0, 0, 0, 0.45);
            border: 1.5px solid var(--gold);
            text-decoration: none;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        }

        .nav-login-btn:hover {
            background: var(--gold);
            color: #0e0b0a;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.5);
            transform: translateY(-2px);
        }

        /* ══════════ HERO CONTENT (PERFECTLY CENTERED) ══════════ */
        .hero-content {
            position: relative;
            z-index: 10;
            text-align: center;
            width: 100%;
            max-width: 820px;
            margin: auto 0;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeInUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Brand Top Tagline */
        .brand-top {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: clamp(0.78rem, 1.8vw, 0.95rem);
            letter-spacing: 3.5px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 1.1rem;
            font-weight: 600;
            text-shadow: 
                0 2px 10px rgba(0, 0, 0, 0.95),
                0 0 20px rgba(0, 0, 0, 0.9);
        }

        .brand-top i {
            font-size: 0.72rem;
            color: var(--gold-light);
        }

        /* Main Heading */
        h1.hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 6.5vw, 4.8rem);
            font-weight: 700;
            line-height: 1.15;
            margin-bottom: 1.25rem;
            color: #ffffff;
            letter-spacing: -0.5px;
            text-shadow: 
                0 4px 20px rgba(0, 0, 0, 0.95),
                0 2px 8px rgba(0, 0, 0, 0.9),
                0 0 35px rgba(0, 0, 0, 0.85);
            word-break: normal;
            overflow-wrap: break-word;
        }

        h1.hero-title span.title-gold {
            color: var(--gold);
        }

        /* Hero Description */
        p.hero-desc {
            font-size: clamp(0.96rem, 1.9vw, 1.18rem);
            color: rgba(255, 255, 255, 0.92);
            line-height: 1.7;
            max-width: 650px;
            margin: 0 auto 2.4rem;
            font-weight: 400;
            text-shadow: 
                0 3px 15px rgba(0, 0, 0, 0.95),
                0 1px 6px rgba(0, 0, 0, 0.9);
        }

        /* CTA Button Container */
        .cta-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .btn-access {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 1.1rem 2.7rem;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            text-decoration: none;
            color: #ffffff;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            box-shadow: 
                0 12px 35px rgba(0, 0, 0, 0.75), 
                0 0 25px rgba(128, 0, 0, 0.65);
            border: 1.5px solid rgba(212, 175, 55, 0.45);
            transition: all 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .btn-access:hover {
            transform: translateY(-4px);
            box-shadow: 
                0 18px 45px rgba(0, 0, 0, 0.9), 
                0 0 35px rgba(212, 175, 55, 0.5);
            border-color: var(--gold);
            color: #ffffff;
        }

        .btn-access i {
            font-size: 1.05rem;
            transition: transform 0.3s ease;
        }

        .btn-access:hover i {
            transform: scale(1.15);
        }

        /* ══════════ BOTTOM SCROLL & FOOTER ══════════ */
        .scroll-down {
            position: absolute;
            bottom: 35px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.9);
            pointer-events: none;
        }

        .scroll-down i {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-8px); }
            60% { transform: translateY(-4px); }
        }

        .site-footer-note {
            position: absolute;
            bottom: 12px;
            left: 0;
            width: 100%;
            text-align: center;
            z-index: 10;
            font-size: 0.76rem;
            color: rgba(255, 255, 255, 0.65);
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.95);
            pointer-events: none;
            padding: 0 10px;
        }

        .site-footer-note strong {
            color: var(--gold);
            font-weight: 600;
        }

        /* ══════════ RESPONSIVE BREAKPOINTS ══════════ */
        @media (max-width: 768px) {
            .site-nav {
                padding: 1.1rem 4%;
            }

            .hero-section {
                padding: 75px 16px 45px;
                justify-content: center;
            }

            /* Natural soft vignette on mobile so text is legible against zoomed central plaque */
            .hero-content {
                padding: 24px 14px;
                background: radial-gradient(ellipse at center, rgba(14, 11, 9, 0.78) 0%, rgba(14, 11, 9, 0.45) 60%, rgba(14, 11, 9, 0) 100%);
                border-radius: 20px;
            }

            .brand-top {
                font-size: 0.76rem;
                letter-spacing: 2px;
                margin-bottom: 0.8rem;
            }

            h1.hero-title {
                font-size: clamp(2rem, 7.5vw, 2.75rem);
                margin-bottom: 0.95rem;
                line-height: 1.15;
            }

            p.hero-desc {
                font-size: 0.9rem;
                line-height: 1.55;
                max-width: 340px;
                margin: 0 auto 1.6rem;
            }

            .btn-access {
                padding: 0.95rem 2rem;
                font-size: 0.92rem;
                width: auto;
                min-width: 220px;
            }

            .scroll-down {
                display: none;
            }

            .site-footer-note {
                font-size: 0.72rem;
                bottom: 8px;
            }
        }

        @media (max-width: 480px) {
            .site-nav {
                padding: 0.85rem 14px;
            }

            .brand-name {
                font-size: 1.25rem;
            }

            .nav-login-btn {
                padding: 0.42rem 0.95rem;
                font-size: 0.78rem;
                letter-spacing: 1px;
            }

            .hero-section {
                padding: 68px 12px 38px;
            }

            .hero-content {
                padding: 20px 10px;
            }

            .brand-top {
                font-size: 0.72rem;
                letter-spacing: 1.8px;
                margin-bottom: 0.7rem;
            }

            h1.hero-title {
                font-size: 2.15rem;
                line-height: 1.15;
            }

            p.hero-desc {
                font-size: 0.86rem;
                line-height: 1.52;
                max-width: 300px;
                margin-bottom: 1.5rem;
            }

            .btn-access {
                padding: 0.88rem 1.8rem;
                font-size: 0.88rem;
                width: 100%;
                max-width: 260px;
            }

            .site-footer-note {
                font-size: 0.68rem;
                bottom: 6px;
            }
        }
    </style>
</head>
<body>

    <section class="hero-section">
        <!-- 100% Unobstructed Vibrant Bakery Background -->
        <div class="hero-bg"></div>

        <!-- Top Navigation -->
        <header class="site-nav">
            <a href="/" class="brand-link">
                <div class="brand-name">{{ shop_name() }}<span>.</span></div>
            </a>
            <div class="nav-actions">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="nav-login-btn">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span>Login</span>
                    </a>
                @endif
            </div>
        </header>

        <!-- Pure Centered Floating Content -->
        <div class="hero-content">
            <div class="brand-top">
                <i class="fa-solid fa-crown"></i>
                <span>{{ shop_tagline() }}</span>
                <i class="fa-solid fa-crown"></i>
            </div>

            <h1 class="hero-title">{{ shop_name() }}<span class="title-gold">.</span></h1>

            <p class="hero-desc">
                Experience the fine art of baking with our handcrafted sweets, gourmet pastries, and traditional delicacies made with the finest ingredients and timeless passion.
            </p>

            <div class="cta-container">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-access">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Access Panel</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Scroll Indicator (Desktop/Tablet) -->
        <div class="scroll-down">
            <span>Scroll</span>
            <i class="fa-solid fa-chevron-down"></i>
        </div>

        <!-- Developer Credit (Always pinned at bottom) -->
        <div class="site-footer-note">
            Software Developed by <strong>{{ developer_name() }}</strong>
        </div>
    </section>

</body>
</html>