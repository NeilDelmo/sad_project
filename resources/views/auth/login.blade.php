<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ config('app.name', 'SeaLedger') }} - Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts / Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Koulen&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>

    <style>
        /* Shared visual styles (adapted from register) */
        .title-font { font-family: "Koulen", sans-serif; font-weight:400; font-size:42px; color:#0074b3; }
        .subhead-font { font-family: "Koulen", sans-serif; font-weight:400; font-size:22px; color:#0074b3; }
        .text-font { font-family: "Jost", sans-serif; font-size:15px; color:#161523; }

        /* page layout */
        .bg { background-image: url("{{ asset('images/background.png') }}"); background-size:cover; background-position:center; min-height:100vh; position:relative; overflow:hidden; }
        .bg::before { content:""; position:absolute; inset:0; background:rgba(0,71,128,0.35); }
        .bg .logo { position:absolute; top:1rem; left:1rem; width:56px; height:56px; border-radius:50%; background-color:rgba(255,255,255,0.92); display:flex; align-items:center; justify-content:center; box-shadow:0 6px 18px rgba(0,0,0,0.18); z-index:30; }
        .bg .logo i { color:#0074b3; font-size:20px; }

        /* small logo next to heading */
        .form-hero-heading { display:flex; align-items:center; gap:12px; }
        .form-hero-logo { width:48px; height:auto; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.12); background:linear-gradient(180deg,#e6f4ff 0%,#d0ecff 100%); padding:8px; display:inline-flex; align-items:center; justify-content:center; border:1px solid rgba(13,110,253,0.12); }

        /* button - centered label and right-aligned icon; use primary gradient */
        .btn-container {
            display: flex;
            align-items: center;
            justify-content: center; /* center the label */
            position: relative;
            width: 100%;
            padding: 0.5rem 1.25rem;
            border-radius: 50rem;
            cursor: pointer;
            border: none;
            background: linear-gradient(135deg, #0075B5 0%, #1B5E88 100%);
            color: #ffffff;
            transition: transform 180ms ease, box-shadow 180ms ease;
        }
        .btn-container:active { transform: translateY(1px); }

        /* center the visible label inside the button */
        .btn-container > .text { flex: 1 1 auto; text-align: center; font-weight: 700; color: #fff; display: block; }

        /* icon pinned to the right inside the button */
        .login-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            background-color: rgba(195,228,233,0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 2px solid rgba(0,117,181,0.12);
        }
        .login-icon i { font-size: 12px; color: #0075B5; transition: transform .28s ease; }
        .btn-container:hover .login-icon i { transform: translateX(4px); }

        /* inputs */
        .form-control { box-shadow:none !important; outline:none; transition:border-color 220ms ease; }
        .form-control:focus { box-shadow:none !important; border-color:#457492 !important; }
        .rounded-pill { border-radius:50rem; }

        /* small helpers */
        .forgot { color:#939494; text-decoration:none; transition:color 220ms ease; }
        .forgot:hover { color:#457492; }

        /* responsive */
        @media (max-width: 767.98px) {
            .bg { display:none; } /* hide left image on small screens */
        }
    </style>
</head>
<body class="m-0">

<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">

        <!-- Left image column -->
        <div class="col-12 col-md-6 bg d-flex align-items-center justify-content-center">
            <div class="logo" aria-hidden="true">
                <i class="fa-solid fa-fish-fins"></i>
            </div>
        </div>

        <!-- Right form column -->
        <div class="col-12 col-md-6 d-flex align-items-center justify-content-center">
            <div style="max-width:520px; width:95%;">
                <div class="card shadow-sm p-4">
                    <div class="form-hero-heading mb-3">
                        <img src="{{ asset('images/logo.png') }}" alt="SeaLedger logo" class="form-hero-logo" />
                        <h3 class="mb-0 title-font">Welcome back.</h3>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label text-font">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="form-control rounded-pill">
                            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label text-font">Password</label>
                            <div class="position-relative">
                                <input id="password" name="password" type="password" required class="form-control pe-5 rounded-pill">
                                <button class="btn btn-sm btn-link position-absolute end-0 top-50 translate-middle-y me-3" type="button" id="togglePassword" aria-label="Show password">
                                    <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                                </button>
                                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Remember + Forgot -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="form-check">
                                <input id="remember_me" name="remember" class="form-check-input" type="checkbox">
                                <label for="remember_me" class="form-check-label text-font">Remember me</label>
                            </div>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="forgot">Forgot your password?</a>
                            @endif
                        </div>

                        <!-- Login button -->
                        <button type="submit" class="btn-container my-3 w-100">
                            <span class="text text-center subhead-font" style="color:#95beff;">Log in</span>
                            <span class="login-icon"><i class="fa-solid fa-fish-fins"></i></span>
                        </button>

                        <div class="text-font text-center" style="font-size:13px;">
                            <span>Don't have an account? </span>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="forgot">Register</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
(function(){
    var toggle = document.getElementById('togglePassword');
    var pwd = document.getElementById('password');
    var icon = document.getElementById('togglePasswordIcon');
    if (toggle && pwd && icon) {
        toggle.addEventListener('click', function () {
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
                toggle.setAttribute('aria-label', 'Hide password');
            } else {
                pwd.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
                toggle.setAttribute('aria-label', 'Show password');
            }
        });
    }
})();
</script>
</body>
</html>