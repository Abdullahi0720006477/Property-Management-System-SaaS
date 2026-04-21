<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BizConnect — Smart Property Management for Modern Teams</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #1B2A4A;
            --primary-dark: #0F172A;
            --accent: #F59E0B;
            --accent-hover: #D97706;
            --text: #E2E8F0;
            --text-muted: #94A3B8;
            --surface: #1E293B;
            --surface-light: #334155;
            --white: #FFFFFF;
            --radius: 12px;
            --radius-lg: 20px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--primary-dark);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        a { text-decoration: none; color: inherit; }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ── Navbar ── */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            padding: 18px 0;
            transition: all 0.35s ease;
            background: transparent;
        }

        .navbar.scrolled {
            padding: 10px 0;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .navbar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--white);
            letter-spacing: -0.5px;
        }

        .navbar-brand span { color: var(--accent); }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
        }

        .nav-links a {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-muted);
            transition: color 0.2s;
        }

        .nav-links a:hover { color: var(--white); }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-ghost:hover { color: var(--white); border-color: rgba(255, 255, 255, 0.3); }

        .btn-accent {
            background: var(--accent);
            color: var(--primary-dark);
        }

        .btn-accent:hover { background: var(--accent-hover); transform: translateY(-1px); }

        .btn-outline-white {
            background: transparent;
            color: var(--white);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-outline-white:hover { border-color: var(--white); }

        .btn-lg { padding: 14px 32px; font-size: 1rem; border-radius: 10px; }

        .hamburger {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Mobile menu */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(15, 23, 42, 0.97);
            backdrop-filter: blur(20px);
            z-index: 999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 24px;
        }

        .mobile-menu.active { display: flex; }

        .mobile-menu a {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text);
        }

        .mobile-menu .close-btn {
            position: absolute;
            top: 20px;
            right: 24px;
            background: none;
            border: none;
            color: var(--white);
            font-size: 2rem;
            cursor: pointer;
        }

        /* ── Hero ── */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            background: linear-gradient(160deg, var(--primary) 0%, var(--primary-dark) 100%);
            overflow: hidden;
            padding: 120px 0 80px;
        }

        .hero .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .hero-text h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            color: var(--white);
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .hero-text h1 span { color: var(--accent); }

        .hero-text p {
            font-size: 1.15rem;
            color: var(--text-muted);
            margin-bottom: 36px;
            max-width: 500px;
            line-height: 1.7;
        }

        .hero-buttons { display: flex; gap: 14px; flex-wrap: wrap; }

        /* Dashboard mockup */
        .hero-visual {
            position: relative;
            perspective: 1000px;
        }

        .mockup {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 24px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
            transform: rotateY(-5deg) rotateX(2deg);
            transition: transform 0.4s ease;
        }

        .mockup:hover { transform: rotateY(0deg) rotateX(0deg); }

        .mockup-header {
            display: flex;
            gap: 6px;
            margin-bottom: 20px;
        }

        .mockup-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .mockup-dot:nth-child(1) { background: #EF4444; }
        .mockup-dot:nth-child(2) { background: #F59E0B; }
        .mockup-dot:nth-child(3) { background: #22C55E; }

        .mockup-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.04);
            border-radius: 10px;
            padding: 14px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .stat-card .label {
            font-size: 0.65rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .value {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--white);
            margin-top: 4px;
        }

        .stat-card .change {
            font-size: 0.65rem;
            color: #22C55E;
            margin-top: 2px;
        }

        .mockup-chart {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            height: 80px;
            margin-bottom: 18px;
            padding: 0 4px;
        }

        .chart-bar {
            flex: 1;
            border-radius: 4px 4px 0 0;
            background: linear-gradient(to top, var(--accent), #FBBF24);
            opacity: 0.7;
            animation: barGrow 1.5s ease forwards;
        }

        @keyframes barGrow {
            from { transform: scaleY(0); }
            to { transform: scaleY(1); }
        }

        .mockup-list { display: flex; flex-direction: column; gap: 8px; }

        .list-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 10px 14px;
        }

        .list-row .name {
            font-size: 0.75rem;
            color: var(--text);
        }

        .list-row .badge {
            font-size: 0.6rem;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-green { background: rgba(34, 197, 94, 0.15); color: #22C55E; }
        .badge-amber { background: rgba(245, 158, 11, 0.15); color: #F59E0B; }
        .badge-red   { background: rgba(239, 68, 68, 0.15); color: #EF4444; }

        /* Floating shapes */
        .hero-shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.06;
            pointer-events: none;
        }

        .shape-1 {
            width: 400px; height: 400px;
            background: var(--accent);
            top: -100px; right: -100px;
            animation: floatA 8s ease-in-out infinite;
        }

        .shape-2 {
            width: 250px; height: 250px;
            background: #3B82F6;
            bottom: -50px; left: -80px;
            animation: floatB 10s ease-in-out infinite;
        }

        @keyframes floatA {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-30px, 30px); }
        }

        @keyframes floatB {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(20px, -20px); }
        }

        /* ── Sections shared ── */
        section { padding: 100px 0; }

        .section-label {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--accent);
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 2.4rem;
            font-weight: 800;
            color: var(--white);
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .section-subtitle {
            font-size: 1.05rem;
            color: var(--text-muted);
            max-width: 560px;
            margin: 0 auto 60px;
            line-height: 1.7;
        }

        .text-center { text-align: center; }

        /* Fade-up animations */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── Features ── */
        .features { background: var(--primary-dark); }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .feature-card {
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: var(--radius);
            padding: 36px 28px;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            border-color: rgba(245, 158, 11, 0.2);
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: rgba(245, 158, 11, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: var(--accent);
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 10px;
        }

        .feature-card p {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.65;
        }

        /* ── How It Works ── */
        .how-it-works { background: var(--surface); }

        .steps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            position: relative;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 36px;
            left: calc(16.66% + 20px);
            right: calc(16.66% + 20px);
            height: 2px;
            background: linear-gradient(90deg, var(--accent), rgba(245, 158, 11, 0.2));
        }

        .step { text-align: center; position: relative; }

        .step-number {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin: 0 auto 24px;
            position: relative;
            z-index: 2;
        }

        .step h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 10px;
        }

        .step p {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.65;
            max-width: 280px;
            margin: 0 auto;
        }

        /* ── Stats ── */
        .stats {
            background: linear-gradient(160deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 32px;
            text-align: center;
        }

        .stat-item .stat-number {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--accent);
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-item .stat-text {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        /* ── Pricing ── */
        .pricing { background: var(--primary-dark); }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            align-items: start;
        }

        .price-card {
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-lg);
            padding: 40px 32px;
            position: relative;
            transition: transform 0.3s ease;
        }

        .price-card:hover { transform: translateY(-4px); }

        .price-card.popular {
            border-color: var(--accent);
            box-shadow: 0 0 40px rgba(245, 158, 11, 0.1);
        }

        .popular-badge {
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--accent);
            color: var(--primary-dark);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 5px 18px;
            border-radius: 20px;
        }

        .price-card h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 8px;
        }

        .price-amount {
            font-size: 2.6rem;
            font-weight: 800;
            color: var(--white);
            margin-bottom: 4px;
        }

        .price-amount span { font-size: 0.9rem; font-weight: 500; color: var(--text-muted); }

        .price-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 28px;
            padding-bottom: 28px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .price-features {
            list-style: none;
            margin-bottom: 32px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .price-features li {
            font-size: 0.88rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .price-features li i { color: var(--accent); font-size: 0.8rem; }

        .price-card .btn { width: 100%; justify-content: center; }

        /* ── Footer ── */
        .footer {
            background: var(--primary-dark);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 60px 0 0;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 48px;
            margin-bottom: 48px;
        }

        .footer-brand p {
            font-size: 0.88rem;
            color: var(--text-muted);
            margin-top: 12px;
            line-height: 1.65;
            max-width: 280px;
        }

        .footer-col h4 {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--white);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 18px;
        }

        .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }

        .footer-col ul li a {
            font-size: 0.85rem;
            color: var(--text-muted);
            transition: color 0.2s;
        }

        .footer-col ul li a:hover { color: var(--accent); }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 24px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-bottom p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .social-links {
            display: flex;
            gap: 16px;
        }

        .social-links a {
            color: var(--text-muted);
            font-size: 1.1rem;
            transition: color 0.2s;
        }

        .social-links a:hover { color: var(--accent); }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .hero-text h1 { font-size: 2.8rem; }
            .features-grid,
            .pricing-grid { grid-template-columns: repeat(2, 1fr) !important; }
            .steps::before { display: none; }
        }

        @media (max-width: 768px) {
            .nav-links, .nav-actions { display: none; }
            .hamburger { display: block; }

            .hero .container {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-text h1 { font-size: 2.2rem; }
            .hero-text p { margin-left: auto; margin-right: auto; }
            .hero-buttons { justify-content: center; }
            .hero-visual { display: none; }

            .section-title { font-size: 1.8rem; }

            .features-grid,
            .steps,
            .pricing-grid { grid-template-columns: 1fr !important; }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 40px;
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 32px;
            }

            .footer-bottom { flex-direction: column; gap: 12px; text-align: center; }
        }

        @media (max-width: 480px) {
            .hero-text h1 { font-size: 1.8rem; }
            .btn-lg { padding: 12px 24px; font-size: 0.9rem; }
            .section-title { font-size: 1.5rem; }
            .price-amount { font-size: 2rem; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar" id="navbar">
    <div class="container">
        <a href="?" class="navbar-brand"><img src="assets/img/logo-full.svg" alt="BizConnect" style="height:36px;"></a>
        <ul class="nav-links">
            <li><a href="#features">Features</a></li>
            <li><a href="#how-it-works">How It Works</a></li>
            <li><a href="#pricing">Pricing</a></li>
        </ul>
        <div class="nav-actions">
            <a href="?page=auth&action=login" class="btn btn-ghost">Sign In</a>
            <a href="?page=auth&action=register" class="btn btn-accent">Get Started</a>
        </div>
        <button class="hamburger" id="hamburgerBtn" aria-label="Open menu">
            <i class="bi bi-list"></i>
        </button>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <button class="close-btn" id="closeMenuBtn" aria-label="Close menu">
        <i class="bi bi-x"></i>
    </button>
    <a href="#features" class="mobile-link">Features</a>
    <a href="#how-it-works" class="mobile-link">How It Works</a>
    <a href="#pricing" class="mobile-link">Pricing</a>
    <a href="?page=auth&action=login" class="btn btn-ghost" style="margin-top: 16px;">Sign In</a>
    <a href="?page=auth&action=register" class="btn btn-accent">Get Started</a>
</div>

<!-- Hero -->
<section class="hero">
    <div class="hero-shape shape-1"></div>
    <div class="hero-shape shape-2"></div>
    <div class="container">
        <div class="hero-text fade-up">
            <h1>Smart Property Management for <span>Modern Teams</span></h1>
            <p>BizConnect helps property management companies streamline operations, track payments, and manage tenants &mdash; all from one powerful platform.</p>
            <div class="hero-buttons">
                <a href="?page=auth&action=register" class="btn btn-accent btn-lg">Get Started Free <i class="bi bi-arrow-right"></i></a>
                <a href="?page=auth&action=login" class="btn btn-outline-white btn-lg">Sign In</a>
            </div>
        </div>
        <div class="hero-visual fade-up" style="transition-delay: 0.15s;">
            <div class="mockup">
                <div class="mockup-header">
                    <div class="mockup-dot"></div>
                    <div class="mockup-dot"></div>
                    <div class="mockup-dot"></div>
                </div>
                <div class="mockup-stats">
                    <div class="stat-card">
                        <div class="label">Properties</div>
                        <div class="value">128</div>
                        <div class="change">+12%</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Occupancy</div>
                        <div class="value">94%</div>
                        <div class="change">+3.2%</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Revenue</div>
                        <div class="value">2.4M</div>
                        <div class="change">+8.5%</div>
                    </div>
                </div>
                <div class="mockup-chart">
                    <div class="chart-bar" style="height: 45%;"></div>
                    <div class="chart-bar" style="height: 65%; animation-delay: 0.1s;"></div>
                    <div class="chart-bar" style="height: 50%; animation-delay: 0.2s;"></div>
                    <div class="chart-bar" style="height: 80%; animation-delay: 0.3s;"></div>
                    <div class="chart-bar" style="height: 60%; animation-delay: 0.4s;"></div>
                    <div class="chart-bar" style="height: 90%; animation-delay: 0.5s;"></div>
                    <div class="chart-bar" style="height: 70%; animation-delay: 0.6s;"></div>
                    <div class="chart-bar" style="height: 55%; animation-delay: 0.7s;"></div>
                </div>
                <div class="mockup-list">
                    <div class="list-row">
                        <span class="name">Sunset Apartments — Unit 4B</span>
                        <span class="badge badge-green">Paid</span>
                    </div>
                    <div class="list-row">
                        <span class="name">Riverside Complex — Unit 2A</span>
                        <span class="badge badge-amber">Pending</span>
                    </div>
                    <div class="list-row">
                        <span class="name">Highland Towers — Unit 7C</span>
                        <span class="badge badge-red">Overdue</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="features" id="features">
    <div class="container text-center">
        <span class="section-label fade-up">Features</span>
        <h2 class="section-title fade-up">Everything You Need to Manage Properties</h2>
        <p class="section-subtitle fade-up">A comprehensive suite of tools designed to streamline every aspect of property management.</p>
        <div class="features-grid">
            <div class="feature-card fade-up">
                <div class="feature-icon"><i class="bi bi-building"></i></div>
                <h3>Property Management</h3>
                <p>Track all your properties, units, and amenities in one centralized dashboard.</p>
            </div>
            <div class="feature-card fade-up" style="transition-delay: 0.08s;">
                <div class="feature-icon"><i class="bi bi-people"></i></div>
                <h3>Tenant Portal</h3>
                <p>Manage tenant profiles, communications, and self-service registration.</p>
            </div>
            <div class="feature-card fade-up" style="transition-delay: 0.16s;">
                <div class="feature-icon"><i class="bi bi-file-earmark-text"></i></div>
                <h3>Lease Tracking</h3>
                <p>Create, renew, and monitor leases with automatic expiry alerts.</p>
            </div>
            <div class="feature-card fade-up" style="transition-delay: 0.06s;">
                <div class="feature-icon"><i class="bi bi-credit-card"></i></div>
                <h3>Payment Collection</h3>
                <p>Record rent payments, track overdue amounts, and generate receipts.</p>
            </div>
            <div class="feature-card fade-up" style="transition-delay: 0.14s;">
                <div class="feature-icon"><i class="bi bi-tools"></i></div>
                <h3>Maintenance Requests</h3>
                <p>Handle repair requests from submission to resolution with status tracking.</p>
            </div>
            <div class="feature-card fade-up" style="transition-delay: 0.22s;">
                <div class="feature-icon"><i class="bi bi-graph-up"></i></div>
                <h3>Reports &amp; Analytics</h3>
                <p>Generate income, expense, occupancy, and overdue reports with charts.</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="how-it-works" id="how-it-works">
    <div class="container text-center">
        <span class="section-label fade-up">How It Works</span>
        <h2 class="section-title fade-up">Get Started in 3 Simple Steps</h2>
        <p class="section-subtitle fade-up">From sign-up to full property oversight in minutes, not months.</p>
        <div class="steps">
            <div class="step fade-up">
                <div class="step-number">1</div>
                <h3>Add Properties</h3>
                <p>Register your buildings and individual units with details and photos.</p>
            </div>
            <div class="step fade-up" style="transition-delay: 0.1s;">
                <div class="step-number">2</div>
                <h3>Manage Tenants</h3>
                <p>Assign tenants to units, create leases, and set up payment schedules.</p>
            </div>
            <div class="step fade-up" style="transition-delay: 0.2s;">
                <div class="step-number">3</div>
                <h3>Track Everything</h3>
                <p>Monitor payments, handle maintenance, and generate comprehensive reports.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item fade-up">
                <div class="stat-number"><span class="counter" data-target="1000">0</span>+</div>
                <div class="stat-text">Properties Managed</div>
            </div>
            <div class="stat-item fade-up" style="transition-delay: 0.08s;">
                <div class="stat-number"><span class="counter" data-target="5000">0</span>+</div>
                <div class="stat-text">Tenants Served</div>
            </div>
            <div class="stat-item fade-up" style="transition-delay: 0.16s;">
                <div class="stat-number"><span class="counter" data-target="50000">0</span>+</div>
                <div class="stat-text">Payments Processed</div>
            </div>
            <div class="stat-item fade-up" style="transition-delay: 0.24s;">
                <div class="stat-number"><span class="counter" data-target="99.9" data-decimals="1">0</span>%</div>
                <div class="stat-text">Uptime</div>
            </div>
        </div>
    </div>
</section>

<!-- Trusted By -->
<section class="stats" style="padding:60px 0;">
    <div class="container text-center">
        <span class="section-label fade-up">Trusted Partners</span>
        <h2 class="section-title fade-up" style="margin-bottom:16px;">Trusted by 100+ Property Companies</h2>
        <p class="section-subtitle fade-up">Property managers across Kenya rely on BizConnect to run their operations efficiently.</p>
    </div>
</section>

<!-- Pricing -->
<section class="pricing" id="pricing">
    <div class="container text-center">
        <span class="section-label fade-up">Pricing</span>
        <h2 class="section-title fade-up">Simple, Transparent Pricing</h2>
        <p class="section-subtitle fade-up">Choose the plan that fits your portfolio. Upgrade or downgrade at any time.</p>
        <div class="pricing-grid" style="grid-template-columns:repeat(4,1fr);">
            <div class="price-card fade-up">
                <h3>Trial</h3>
                <div class="price-amount">Free</div>
                <div class="price-desc">14-day free trial to explore the platform.</div>
                <ul class="price-features">
                    <li><i class="bi bi-check-circle-fill"></i> Up to 3 properties</li>
                    <li><i class="bi bi-check-circle-fill"></i> Up to 10 units</li>
                    <li><i class="bi bi-check-circle-fill"></i> Basic reports</li>
                    <li><i class="bi bi-check-circle-fill"></i> Email support</li>
                </ul>
                <a href="?page=auth&action=register" class="btn btn-ghost" style="width:100%;justify-content:center;">Get Started Free</a>
            </div>
            <div class="price-card fade-up" style="transition-delay: 0.08s;">
                <h3>Starter</h3>
                <div class="price-amount">KES 2,500<span>/mo</span></div>
                <div class="price-desc">Perfect for getting started with a small portfolio.</div>
                <ul class="price-features">
                    <li><i class="bi bi-check-circle-fill"></i> Up to 10 properties</li>
                    <li><i class="bi bi-check-circle-fill"></i> Up to 50 units</li>
                    <li><i class="bi bi-check-circle-fill"></i> Basic reports</li>
                    <li><i class="bi bi-check-circle-fill"></i> Email support</li>
                </ul>
                <a href="?page=auth&action=register" class="btn btn-ghost" style="width:100%;justify-content:center;">Get Started Free</a>
            </div>
            <div class="price-card popular fade-up" style="transition-delay: 0.16s;">
                <div class="popular-badge">Most Popular</div>
                <h3>Professional</h3>
                <div class="price-amount">KES 7,500<span>/mo</span></div>
                <div class="price-desc">For growing property management businesses.</div>
                <ul class="price-features">
                    <li><i class="bi bi-check-circle-fill"></i> Up to 50 properties</li>
                    <li><i class="bi bi-check-circle-fill"></i> Unlimited units</li>
                    <li><i class="bi bi-check-circle-fill"></i> Advanced reports</li>
                    <li><i class="bi bi-check-circle-fill"></i> M-Pesa integration</li>
                    <li><i class="bi bi-check-circle-fill"></i> Priority support</li>
                </ul>
                <a href="?page=auth&action=register" class="btn btn-accent" style="width:100%;justify-content:center;">Get Started Free</a>
            </div>
            <div class="price-card fade-up" style="transition-delay: 0.24s;">
                <h3>Enterprise</h3>
                <div class="price-amount">KES 20,000<span>/mo</span></div>
                <div class="price-desc">For large-scale property management operations.</div>
                <ul class="price-features">
                    <li><i class="bi bi-check-circle-fill"></i> Unlimited properties</li>
                    <li><i class="bi bi-check-circle-fill"></i> Unlimited everything</li>
                    <li><i class="bi bi-check-circle-fill"></i> Custom reports</li>
                    <li><i class="bi bi-check-circle-fill"></i> API access</li>
                    <li><i class="bi bi-check-circle-fill"></i> Dedicated support</li>
                </ul>
                <a href="?page=auth&action=register" class="btn btn-ghost" style="width:100%;justify-content:center;">Get Started Free</a>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="?" class="navbar-brand"><img src="assets/img/logo-full.svg" alt="BizConnect" style="height:36px;"></a>
                <p>A modern property management platform built to simplify how you manage buildings, tenants, and finances.</p>
            </div>
            <div class="footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Product</h4>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li><a href="#">Integrations</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Documentation</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 BizConnect. All rights reserved.</p>
            <div class="social-links">
                <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                <a href="#" aria-label="GitHub"><i class="bi bi-github"></i></a>
            </div>
        </div>
    </div>
</footer>

<script>
(function() {
    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', function() {
        navbar.classList.toggle('scrolled', window.scrollY > 50);
    }, { passive: true });

    // Mobile menu
    const hamburger = document.getElementById('hamburgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const closeBtn = document.getElementById('closeMenuBtn');

    hamburger.addEventListener('click', function() { mobileMenu.classList.add('active'); });
    closeBtn.addEventListener('click', function() { mobileMenu.classList.remove('active'); });

    document.querySelectorAll('.mobile-link').forEach(function(link) {
        link.addEventListener('click', function() { mobileMenu.classList.remove('active'); });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // IntersectionObserver for fade-up
    var fadeEls = document.querySelectorAll('.fade-up');
    if ('IntersectionObserver' in window) {
        var obs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        fadeEls.forEach(function(el) { obs.observe(el); });
    } else {
        fadeEls.forEach(function(el) { el.classList.add('visible'); });
    }

    // Counter animation
    var counters = document.querySelectorAll('.counter');
    if ('IntersectionObserver' in window) {
        var cObs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    cObs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(function(c) { cObs.observe(c); });
    }

    function animateCounter(el) {
        var target = parseFloat(el.getAttribute('data-target'));
        var decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
        var duration = 2000;
        var start = 0;
        var startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = start + (target - start) * eased;
            el.textContent = decimals > 0 ? current.toFixed(decimals) : Math.floor(current).toLocaleString();
            if (progress < 1) requestAnimationFrame(step);
        }

        requestAnimationFrame(step);
    }
})();
</script>

</body>
</html>
