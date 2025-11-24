<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'SeaLedger') }}</title>
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

        <!-- Bootstrap CSS -->
        <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous"
        />

        <!-- koulen font -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
        href="https://fonts.googleapis.com/css2?family=Koulen&display=swap"
        rel="stylesheet"
        />

        <!-- jost font -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
        href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet"
        />

        <!-- AOS Animation CSS -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

        <!-- fontawesome kit -->
        <script
        src="https://kit.fontawesome.com/19696dbec5.js"
        crossorigin="anonymous"
        ></script>

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Tailwind fallback if needed */
            </style>
        @endif

        <style>
            :root {
                --nav-height: 64px;
                --primary-color: #0074b3;
            }

            html,
            body {
                height: 100%;
            }

            body {
                padding: 0;
                padding-top: 64px;
                margin: 0;
                background-color: #f8fafc;
                min-height: 100vh;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                cursor: url("{{ asset('images/fishing-hook-b.svg') }}") 0 0, auto;
            }

            /* fonts */
            .title-font {
                font-family: "Koulen", sans-serif;
                font-weight: 400;
                font-style: normal;
                font-size: 80px;
                color: #0074b3;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            }

            .subhead-font {
                font-family: "Koulen", sans-serif;
                font-weight: 400;
                font-style: normal;
                font-size: 50px;
                color: #0074b3;
            }

            .market-font {
                font-family: "Koulen", sans-serif;
                font-weight: 400;
                font-style: normal;
                font-size: 30px;
                color: #444253;
            }

            .text-font {
                font-family: "Jost", sans-serif;
                font-optical-sizing: auto;
                font-weight: 400;
                font-style: normal;
                font-size: 20px;
                color: #444253;
            }

            .desc-font {
                font-family: "Jost", sans-serif;
                font-optical-sizing: auto;
                font-weight: 400;
                font-style: normal;
                font-size: 18px;
                color: #64748b;
                line-height: 1.6;
            }

            /* title card responsive */
            .bg-section .title-font {
                font-size: clamp(36px, 6vw, 90px);
                line-height: 1;
            }

            .bg-section .text-font {
                font-size: clamp(16px, 2.3vw, 22px);
            }

            /* med screens */
            @media (max-width: 991.98px) {
                .subhead-font {
                font-size: 45px;
                }

                .desc-font {
                font-size: 16px;
                }
            }

            /* small screens */
            @media (max-width: 575.98px) {
                .subhead-font {
                font-size: 32px;
                }

                .desc-font {
                font-size: 15px;
                }
            }

            /* bg container */
            .bg-section {
                position: relative;
                width: 100%;
                min-height: 85vh; /* Made longer */
                height: auto;
                background-image: linear-gradient(rgba(255,255,255,0.1), rgba(255,255,255,0.2)), url("{{ asset('images/landing.jpg') }}");
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed; /* Parallax effect */

                margin-top: calc(-1 * var(--nav-height));
                padding-top: calc(var(--nav-height) + 1rem);

                display: flex;
                justify-content: center;
                align-items: center;
                padding-left: 1rem;
                padding-right: 1rem;
            }

            @media (max-width: 767.98px) {
                .bg-section {
                height: auto;
                min-height: 70vh;
                }

                .title-card {
                margin-top: 0;
                }
            }

            .content-card {
                width: 90%;
                max-width: 1100px;
                box-sizing: border-box;
                border-radius: 20px;
                background: white;
                box-shadow: 0 20px 40px rgba(0,0,0,0.05);
                border: 1px solid rgba(0,0,0,0.02);
                transition: transform 0.3s ease;
            }
            
            .content-card:hover {
                transform: translateY(-5px);
            }

            .title-card {
                background: transparent;
                border: none;
                box-shadow: none;
                z-index: 1;
                width: 100%;
                max-width: 650px;
            }

            @media (min-width: 768px) {
                .title-card {
                max-width: 700px;
                }

                .content-card {
                width: 75%;
                min-height: 380px;
                display: flex;
                flex-direction: column;
                }
            }

            .content-card > .row {
                flex: 1 1 auto;
                min-height: 0;
            }

            .content-card .col-6 {
                display: flex;
                flex-direction: column;
            }

            .content-card .col-6 > .border.h-100 {
                flex: 1 1 auto;
            }

            .img-content {
                border-radius: 12px;
            }

            /* button hover */
            .btn-hero {
                transition: all 0.3s ease;
                will-change: transform;
                border-radius: 50px !important; /* Rounded buttons */
                min-width: 180px; /* Same size */
                padding: 15px 30px;
                font-weight: 600;
                letter-spacing: 1px;
                text-transform: uppercase;
                font-family: "Koulen", sans-serif;
                font-size: 1.2rem;
            }

            .btn-hero:hover,
            .btn-hero:focus {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(0, 116, 179, 0.3);
            }

            @keyframes pulse-blue {
                0% {
                    box-shadow: 0 0 0 0 rgba(0, 116, 179, 0.7);
                }
                70% {
                    box-shadow: 0 0 0 15px rgba(0, 116, 179, 0);
                }
                100% {
                    box-shadow: 0 0 0 0 rgba(0, 116, 179, 0);
                }
            }

            .btn-pulse {
                animation: pulse-blue 2s infinite;
            }

            /* button style */
            .btn-style {
                --btn-bg: #0074b3;
                --btn-color: #ffffff;
                --btn-hover-bg: #005f93;
                --btn-hover-color: #ffffff;
                background: var(--btn-bg);
                color: var(--btn-color);
                border: none;
                transition: all 0.3s ease;
            }

            .btn-style:hover,
            .btn-style:focus {
                background: var(--btn-hover-bg);
                color: var(--btn-hover-color);
                text-decoration: none;
            }

            /* nav sticky */
            .nav-style {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: 64px;
                z-index: 1100;
                width: 100%;
                display: flex;
                align-items: center;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            }

            .login-icon:hover {
                cursor: pointer;
                transform: scale(1.1);
                transition: transform 0.2s;
            }

            /* base content card height on content */
            .content-card.auto-height {
                height: auto;
                display: block;
            }

            /* marketplace cards */
            .top-box,
            .bottom-box {
                width: 100%;
                padding: 1rem;
            }

            .top-box {
                min-height: 220px;
                border-bottom: none;
                padding: 0;
            }

            .bottom-box {
                min-height: 100px;
                margin-top: -1px;
                background: #fff;
            }

            .top-box-img {
                width: 100%;
                height: 100%;
                display: block;
                object-fit: cover;
            }

            .market-card {
                height: auto;
                display: block;
                background: #fff;
                border-radius: 20px;
                overflow: hidden;
                transition: all 0.3s ease;
                will-change: transform;
                transform-origin: center;
                box-shadow: 0 10px 20px rgba(0,0,0,0.05);
                border: 1px solid rgba(0,0,0,0.05);
            }

            .market-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            }

            /* footer */
            .site-footer {
                background-color: #0f172a;
                color: #94a3b8;
            }

            .site-footer a {
                color: #cbd5e1;
                text-decoration: none;
                transition: color 0.2s;
            }
            
            .site-footer a:hover {
                color: #fff;
            }

            .footer-logo {
                height: 32px;
                width: auto;
                filter: brightness(0) invert(1);
            }

            /* content card imgs */
            .img-box {
                position: relative;
                width: 100%;
                height: 100%;
                overflow: hidden;
                border-radius: 12px;
            }

            /* content card img hover */
            .img-card {
                transition: transform 0.5s ease;
                will-change: transform;
                transform-origin: center;
                border-radius: 20px;
                height: 100%;
                width: 100%;
            }

            .img-card img {
                transition: transform 0.5s ease;
            }

            .content-card:hover .img-card img {
                transform: scale(1.05);
            }

            .img-fit {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            /* top brand above hero title (logo + SEALEDGER) */
            .hero-brand-top {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
                margin-bottom: 20px;
                z-index: 2;
            }
            .hero-brand-top .hero-logo-top {
                width: 100px;
                height: auto;
                display: block;
                filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
            }
            .hero-brand-top .hero-brand-text {
                font-family: "Koulen", sans-serif;
                color: #ffffff;
                font-weight: 700;
                font-size: 48px;
                letter-spacing: 2px;
                text-transform: uppercase;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            }
            @media (max-width: 575.98px) {
                .hero-brand-top .hero-logo-top { width: 80px; }
                .hero-brand-top .hero-brand-text { font-size: 32px; }
            }

            /* New Modern Sections */
            .modern-icon-box {
                background: white;
                border-radius: 20px;
                padding: 2rem;
                height: 100%;
                box-shadow: 0 10px 30px rgba(0,0,0,0.03);
                transition: transform 0.3s ease;
                border: 1px solid rgba(0,0,0,0.02);
            }
            .modern-icon-box:hover {
                transform: translateY(-10px);
                box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            }
            .section-title {
                font-family: "Koulen", sans-serif;
                font-size: 3.5rem;
                color: #0074b3;
                margin-bottom: 1rem;
            }
            .stat-number {
                font-family: "Koulen", sans-serif;
                font-size: 4rem;
                color: #0074b3;
                line-height: 1;
            }
        </style>
    </head>
<body>
<!-- nav -->
<nav class="navbar nav-style p-0">
  <div class="container-fluid px-4 d-flex align-items-center justify-content-between">

      {{-- Brand --}}
      <a
        class="navbar-brand title-font d-flex align-items-center gap-2"
        style="font-size: 28px; color: #0074b3;"
        href="{{ url('/') }}"
      >
        <img src="{{ asset('images/logo.png') }}" alt="SeaLedger Logo" style="height: 40px; width: auto;">
        {{ config('app.name', 'SeaLedger') }}
      </a>

      {{-- Right side --}}
      @if (Route::has('login'))
          @auth
              {{-- Dashboard Icon --}}
              <a href="{{ url('/dashboard') }}" class="text-decoration-none btn btn-style rounded-pill px-4">
                  Dashboard
              </a>
          @else
              <div class="d-flex gap-3">
                  <a href="{{ route('login') }}" class="text-decoration-none text-dark fw-bold pt-2">Login</a>
                  @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-style rounded-pill px-4">Get Started</a>
                  @endif
              </div>
          @endauth
      @endif

  </div>
</nav>
<!-- end of nav -->

    <!-- bg / hero section -->
    <div class="bg-section d-flex justify-content-center align-items-center">
        <!-- title card -->
        <div class="card title-card d-flex flex-column text-center p-3 mb-4 mx-auto">
            <!-- top brand: logo + SEALEDGER (does not affect hero text layout) -->
            <div class="hero-brand-top" data-aos="fade-down" data-aos-duration="1000">
                <img src="{{ asset('images/logo.png') }}" alt="SeaLedger logo" class="hero-logo-top" />
                <div class="hero-brand-text">SEALEDGER</div>
            </div>
            <div class="container title-font lh-1 mb-4" data-aos="fade-up" data-aos-delay="200">
            <span>Fish smart.<br /></span>
            <span>Fish safe.<br /></span>
            <span>Fish as one.<br /></span>
            </div>

            <p class="text-font mb-5" data-aos="fade-up" data-aos-delay="400" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                Your personal integrated maritime assistant.
            </p>

            {{-- Hero form --}}
            @guest
                <div class="d-flex mt-3 justify-content-center gap-3 flex-wrap" data-aos="zoom-in" data-aos-delay="600">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="btn btn-style btn-hero btn-pulse">Login</a>
                    @endif

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-light btn-hero text-primary btn-pulse">Register</a>
                    @endif
                </div>
            @else
                <div class="d-flex mt-3 justify-content-center" data-aos="zoom-in" data-aos-delay="600">
                    @if(Auth::user()->user_type === 'buyer')
                        <a href="{{ route('marketplace.index') }}" class="btn btn-style btn-hero btn-pulse">Go to Marketplace</a>
                    @else
                        <a href="{{ url('/dashboard') }}" class="btn btn-style btn-hero btn-pulse">Go to Dashboard</a>
                    @endif
                </div>
            @endguest
        </div>
        
        <!-- Scroll Down Indicator -->
        <div class="position-absolute bottom-0 mb-4 text-white text-center w-100" style="animation: bounce 2s infinite;">
            <i class="fa-solid fa-chevron-down fa-2x"></i>
        </div>
    </div>

    <!-- Stats Section (New) -->
    <div class="container-fluid bg-white py-5">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="stat-number">24/7</div>
                    <div class="text-font">Zone Monitoring</div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-number">100%</div>
                    <div class="text-font">Data Privacy</div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-number">Free</div>
                    <div class="text-font">Community Access</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Why Choose Us (New) -->
    <div class="container my-5 py-5">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Why Choose SeaLedger?</h2>
            <p class="desc-font">Empowering your maritime journey with data-driven insights.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="modern-icon-box text-center">
                    <div class="mb-4 text-primary">
                        <i class="fa-solid fa-chart-line fa-3x"></i>
                    </div>
                    <h3 class="market-font mb-3">Real-Time Analytics</h3>
                    <p class="desc-font">Get up-to-the-minute data on weather, zones, and catch rates to maximize your efficiency.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="modern-icon-box text-center">
                    <div class="mb-4 text-primary">
                        <i class="fa-solid fa-shield-halved fa-3x"></i>
                    </div>
                    <h3 class="market-font mb-3">Safety First</h3>
                    <p class="desc-font">Automated alerts for restricted zones and dangerous weather conditions keep you safe at sea.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="modern-icon-box text-center">
                    <div class="mb-4 text-primary">
                        <i class="fa-solid fa-users fa-3x"></i>
                    </div>
                    <h3 class="market-font mb-3">Community Driven</h3>
                    <p class="desc-font">Join a growing network of fishermen sharing insights, tips, and market trends.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- content 1 -->
    <div class="card content-card d-flex flex-column text-start p-0 mx-auto mt-5 overflow-hidden" data-aos="fade-right">
        <div class="row g-0 h-100">
            <div class="col-12 col-md-6 order-md-1">
                <div class="p-5 h-100 d-flex flex-column justify-content-center align-items-start">
                    <span class="subhead-font mb-3">Zone Navigator</span>
                    <p class="desc-font mb-4">
                    Stay ahead on your journey with automated tracking of fishing
                    zones, smart route predictions, weather patterns, and maritime
                    alerts. Fish confidently, efficiently, and with peace of mind.
                    </p>
                    <a href="#" class="btn btn-outline-primary rounded-pill px-4">Learn More</a>
                </div>
            </div>

            <div class="col-12 col-md-6 order-md-2">
                <div class="h-100 position-relative overflow-hidden img-card">
                    <video class="img-fit" autoplay loop muted playsinline>
                        <source src="{{ asset('images/zone-nav.mp4') }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </div>
    <!-- end of content 1 card -->

    <!-- content 2 -->
    <div class="card content-card text-start p-0 mx-auto mt-5 overflow-hidden" data-aos="fade-left">
        <div class="row g-0 h-100">
            <div class="col-12 col-md-6">
                <div class="h-100 position-relative overflow-hidden img-card">
                    <video class="img-fit" autoplay loop muted playsinline>
                        <source src="{{ asset('images/rental.mp4') }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="p-5 h-100 d-flex flex-column justify-content-center align-items-start">
                    <span class="subhead-font mb-3">Gear Tracking Manager</span>
                    <p class="desc-font mb-4">
                    Effortlessly manage equipments with digital asset logs and
                    automated maintenance schedules, keeping everything in top
                    condition and ready when you need it.
                    </p>
                    <a href="#" class="btn btn-outline-primary rounded-pill px-4">Explore Features</a>
                </div>
            </div>
        </div>
    </div>
    <!-- end of content 2 card -->

    <!-- content 3 -->
    <div class="card content-card text-start p-0 mx-auto mt-5 overflow-hidden" data-aos="fade-right">
        <div class="row g-0 h-100">

            <div class="col-12 col-md-6 order-md-1">
                <div class="p-5 h-100 d-flex flex-column justify-content-center align-items-start">
                    <span class="subhead-font mb-3">SeaConnect</span>
                    <p class="desc-font mb-4">
                    A community forum for fishermen and buyers to connect, exchange
                    insights, discuss challenges, and share knowledge to build a
                    stronger, more informed community together.
                    </p>
                    <a href="#" class="btn btn-outline-primary rounded-pill px-4">Join Community</a>
                </div>
            </div>

            <div class="col-12 col-md-6 order-md-2">
                <div class="h-100 position-relative overflow-hidden img-card">
                    <video class="img-fit" autoplay loop muted playsinline style="object-fit: contain;">
                        <source src="{{ asset('images/SEACONNECT.mp4') }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>

        </div>
    </div>
    <!-- end of content 3 card -->

    <!-- market card -->
    <div class="container mt-5 pt-5">
        <div class="text-center mb-5">
            <span class="subhead-font" data-aos="fade-up">Marketplace</span>
            <p class="desc-font mb-3" data-aos="fade-up" data-aos-delay="100">
                From fresh fishes to quality gears, we've got it all.
            </p>
        </div>

        <div class="row g-4 justify-content-center">

            <!-- Gear -->
            <div class="col-12 col-md-4" data-aos="flip-up" data-aos-delay="200">
            <div class="market-card h-100">
                <div class="border-bottom top-box position-relative overflow-hidden">
                <img src="{{ asset('images/gear.jpg') }}" alt="Gear" class="top-box-img" />
                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-0 hover-overlay transition"></div>
                </div>
                <div class="bottom-box p-4 text-start">
                <span class="market-font d-block mb-2">Gear</span>
                <span class="desc-font">
                    Find reliable fishing tools and equipment suited for every need.
                </span>
                <div class="mt-3">
                    <a href="#" class="text-decoration-none fw-bold" style="color: #0074b3;">Shop Gear <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
                </div>
            </div>
            </div>

            <!-- Fish -->
            <div class="col-12 col-md-4" data-aos="flip-up" data-aos-delay="300">
            <div class="market-card h-100">
                <div class="border-bottom top-box position-relative overflow-hidden">
                <img src="{{ asset('images/fish.jpg') }}" alt="Fish" class="top-box-img" />
                </div>
                <div class="bottom-box p-4 text-start">
                <span class="market-font d-block mb-2">Fish</span>
                <span class="desc-font">
                    Purchase fresh, high-quality seafood directly from trusted fishermen.
                </span>
                <div class="mt-3">
                    <a href="#" class="text-decoration-none fw-bold" style="color: #0074b3;">Buy Fish <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
                </div>
            </div>
            </div>

            <!-- Rent -->
            <div class="col-12 col-md-4" data-aos="flip-up" data-aos-delay="400">
            <div class="market-card h-100">
                <div class="border-bottom top-box position-relative overflow-hidden">
                <img src="{{ asset('images/trade.jpg') }}" alt="Rent" class="top-box-img" />
                </div>
                <div class="bottom-box p-4 text-start">
                <span class="market-font d-block mb-2">Rent</span>
                <span class="desc-font">
                    Easily rent gears and other essentials for your fishing trips.
                </span>
                <div class="mt-3">
                    <a href="#" class="text-decoration-none fw-bold" style="color: #0074b3;">Rent Now <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
                </div>
            </div>
            </div>

        </div>
    </div>
    <!-- end of market card -->

    <!-- Testimonials (New) -->
    <div class="container-fluid bg-white py-5 mt-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Community Voices</h2>
            </div>
            <div class="row g-4">
                <!-- Testimonial 1 -->
                <div class="col-md-6" data-aos="fade-right">
                    <div class="p-4 border rounded-3 bg-light h-100 shadow-sm">
                        <div class="mb-3 text-warning">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p class="fst-italic desc-font">"SeaLedger has completely transformed how I plan my trips. The zone tracking is a lifesaver and helps me avoid restricted areas effortlessly."</p>
                        <div class="d-flex align-items-center mt-4">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 50px; height: 50px; font-size: 1.2rem;">JD</div>
                            <div class="ms-3">
                                <h5 class="mb-0 market-font" style="font-size: 1.2rem;">John Doe</h5>
                                <small class="text-muted">Commercial Fisherman</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 2 -->
                <div class="col-md-6" data-aos="fade-left">
                    <div class="p-4 border rounded-3 bg-light h-100 shadow-sm">
                        <div class="mb-3 text-warning">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                        </div>
                        <p class="fst-italic desc-font">"The marketplace makes it so easy to sell my catch directly to buyers. No more middlemen taking a cut. It's fair trade at its best."</p>
                        <div class="d-flex align-items-center mt-4">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 50px; height: 50px; font-size: 1.2rem;">JS</div>
                            <div class="ms-3">
                                <h5 class="mb-0 market-font" style="font-size: 1.2rem;">Jane Smith</h5>
                                <small class="text-muted">Local Supplier</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section (New) -->
    <div class="container-fluid py-5" style="background: #0074b3;">
        <div class="container text-center text-white">
            <h2 class="title-font mb-3" style="font-size: 3rem; color: white;">Ready to Set Sail?</h2>
            <p class="desc-font text-white-50 mb-4" style="font-size: 1.2rem;">Join the community modernizing their trade.</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-5 py-3 fw-bold" style="color: #0074b3;">Create Free Account</a>
        </div>
    </div>

    <!-- footer -->
    <footer class="site-footer py-5">
        <div class="container">

            <div class="row gy-4">
                <!-- Logo + site name -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <a
                            class="navbar-brand title-font d-flex align-items-center gap-2"
                            style="font-size: 25px; color: white;"
                            href="{{ url('/') }}"
                        >
                            <img src="/images/logo.png" alt="SeaLedger Logo" class="footer-logo" />
                            {{ config('app.name', 'SeaLedger') }}
                        </a>
                    </div>
                    <p class="small text-muted">Empowering the maritime community with technology, safety, and sustainability.</p>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="text-white mb-3">Platform</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Features</a></li>
                        <li><a href="#">Marketplace</a></li>
                        <li><a href="#">Pricing</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="text-white mb-3">Company</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Privacy</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h5 class="text-white mb-3">Newsletter</h5>
                    <form class="d-flex gap-2">
                        <input type="email" class="form-control rounded-pill" placeholder="Enter your email">
                        <button class="btn btn-primary rounded-pill">Subscribe</button>
                    </form>
                </div>
            </div>

            <!-- copyright -->
            <div class="row mt-5 pt-4 border-top border-secondary">
                <div class="col-md-6 text-center text-md-start">
                    <small>&copy; {{ date('Y') }} SeaLedger Inc. All rights reserved.</small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="d-flex gap-3 justify-content-center justify-content-md-end">
                        <a href="#"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
            </div>

        </div>
    </footer>
    <!-- end footer -->

    <!-- AOS Animation JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>

    <!-- 100% privacy-first analytics -->
    <script data-collect-dnt="true" async src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
    </body>
</html>
