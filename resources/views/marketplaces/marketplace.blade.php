<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- bootstrap -->
    <link rel="stylesheet" href="bootstrap5/css/bootstrap.min.css" />

    <!-- icons -->
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>

    <title>SeaLedger Marketplace</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        .koulen-regular {
            font-family: "Koulen", sans-serif;
            font-weight: 400;
            font-style: normal;
        }

        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .font-title {
            font-size: 110px;
            font-family: "Koulen", sans-serif;
            font-weight: 400;
            font-style: normal;
            line-height: 1;
            display: block;
        }

        .font-subtitle {
            font-size: 27px;
            font-family: "Koulen", sans-serif;
            font-weight: 400;
            font-style: normal;
        }

        .btn-text {
            font-size: 20px;
            font-family: "Koulen", sans-serif;
            font-weight: 400;
            font-style: normal;
        }

        .blue {
            color: #0075B5;
        }

        .light-blue {
            color: #E7FAFE;
        }

        .gray {
            color: #7A96AC;
        }

        .dark-blue {
            color: #1B5E88;
        }

        /* Modern Navbar */
        .navbar {
            background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-brand {
            color: white;
            font-size: 28px;
            font-weight: bold;
            font-family: "Koulen", sans-serif;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .nav-center-group {
            display: flex;
            gap: 10px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: white;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
        }

        .nav-link:hover::before {
            transform: translateX(0);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .nav-right-group {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .nav-icon-link {
            color: white;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .nav-icon-link:hover {
            transform: scale(1.2);
            color: #E7FAFE;
        }

        /* for bg uncomment later */
        /* .bg-cover {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('login-photo.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -1;
        } */

        /* center div */
        .center-div {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 68px);
            text-align: center;
        }

        .btn-size {
            padding: 3px 20px;
            width: auto;            
            max-width: 90%;
            white-space: nowrap;
        }

        /* page indicator */
        .page-indicators {
            position: fixed;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .indicator {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            background-color: #1B5E88;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .indicator.active {
            background-color: #1B5E88;
            opacity: 1;
            width: 40px;
        }

    </style>
</head>
<body style="background-color: #BFBFBF;"> <!-- temp - change to bg image later -->
    
    <!-- navbar -->
    <nav class="navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- logo -->
            <a class="nav-brand" href="{{ route('marketplace.index') }}" style="text-decoration: none;">
                🐟 SeaLedger
            </a>

            <!-- center group -->
            <div class="nav-center-group">
                <a href="{{ route('marketplace.shop') }}" class="nav-link">
                    <i class="fa-solid fa-fire"></i> Latest
                </a>
                <a href="{{ route('marketplace.shop') }}" class="nav-link">
                    <i class="fa-solid fa-fish"></i> Fish
                </a>
                <a href="{{ route('marketplace.shop') }}" class="nav-link">
                    <i class="fa-solid fa-screwdriver-wrench"></i> Gears
                </a>
            </div>

            <!-- right group -->
            <div class="nav-right-group">
                <a href="#" class="nav-icon-link">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </a>
                <a href="#" class="nav-icon-link">
                    <i class="fa-solid fa-heart"></i>
                </a>
                <a href="#" class="nav-icon-link">
                    <i class="fa-solid fa-cart-shopping"></i>
                </a>
                @if(Auth::check())
                    <a href="{{ route('dashboard') }}" title="Dashboard" class="nav-icon-link">
                        <i class="fa-solid fa-user"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" title="Login" class="nav-icon-link">
                        <i class="fa-solid fa-right-to-bracket"></i>
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <div class="d-flex flex-column align-items-center center-div light-blue">

        <div>
            <!-- change Amber to name of user logged in -->
            <span class="font-subtitle">{{ Auth::check() ? Auth::user()->name : 'Guest' }}, ready for today's catch?</span>
            <span class="font-title">SeaLedger</span>
            <div class="d-flex gap-3 justify-content-center mt-2">
                <a href="{{ route('marketplace.shop') }}" class="btn btn-text btn-size rounded-pill light-blue" style="background-color: #0075B5;">Marketplace</a>
                <a href="#" class="btn btn-text btn-size rounded-pill dark-blue" style="background-color: #E7FAFE; border: 2px solid #0075B5;">Community Forum</a>
            </div>
        </div>

        <!-- page indicator -->
        <div class="page-indicators gap-1">
            <span class="indicator active"></span>
            <span class="indicator"></span>
        </div>

    </div>

</body>

<!-- page indicator js -->
<script>
    
    // toggle to active
    document.addEventListener('click', () => {
        const indicators = document.querySelectorAll('.indicator');
        indicators.forEach(ind => ind.classList.toggle('active'));
    });
</script>


</html>