<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- bootstrap -->
    <link rel="stylesheet" href="bootstrap5/css/bootstrap.min.css" />

    <!-- icons -->
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>

    <title>Marketplace</title>

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

        .nav-bar {
            background-color: #E7FAFE;  
            height: 48px;
        }

        /* center div */
        .center-div {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 48px);
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
    <nav class="navbar py-0">
        <div class="container-fluid nav-bar">

            <!-- logo -->
            <a class="navbar-brand" href="#">
                <!-- <img src="photos/sealedger-logo.png" width="50" height="50"> -->
                 Logo Here
            </a>

            <!-- center group -->
            <div class="btn-text blue gap-5 d-flex">
                <a href="#">Latest</a>
                <a href="#">Fish</a>
                <a href="#">Gears</a>
            </div>

            <!-- right group -->
            <div class="btn-text blue gap-3 d-flex">
                <a href="#">
                    <i class="fa-solid fa-magnifying-glass fa-xs"></i>
                </a>
                <a href="#">
                    <i class="fa-solid fa-heart fa-xs"></i>
                </a>
                <a href="#">
                    <i class="fa-solid fa-cart-shopping fa-2xs"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="d-flex flex-column align-items-center center-div light-blue">

        <div>
            <!-- change Amber to name of user logged in -->
            <span class="font-subtitle">Amber, ready for today's catch?</span>
            <span class="font-title">Marketplace</span>
            <button class="btn btn-text btn-size rounded-pill light-blue" style="background-color: #0075B5;">Shop</button>
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