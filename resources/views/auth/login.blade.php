<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ config('app.name', 'SeaLedger') }} - Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Fonts / Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Koulen&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --primary:#0075B5;
            --primary-dark:#004c81;
            --text-dark:#161523;
        }
        body { font-family:"Jost",sans-serif; background:#f3f6fb; }
        .title-font { font-family:"Koulen",sans-serif; font-weight:400; letter-spacing:0.5px; color:var(--primary); }
        .subhead-font { font-family:"Koulen",sans-serif; font-weight:400; color:var(--primary); }
        .text-muted-sm { color:#6f7d8c; font-size:14px; }

        .hero-pane {
            background-image:linear-gradient(135deg,rgba(0,117,181,0.85),rgba(0,65,126,0.85)),url("{{ asset('images/background.png') }}");
            background-size:cover;
            background-position:center;
            color:#fff;
            min-height:100vh;
            position:relative;
            overflow:hidden;
        }
        .hero-pane::after {
            content:"";
            position:absolute;
            width:260px;
            height:260px;
            border-radius:42% 58% 38% 62% / 55% 30% 70% 45%;
            background:rgba(255,255,255,0.08);
            bottom:-80px;
            right:-60px;
        }
        .hero-content { position:relative; z-index:1; padding:3.5rem 3rem; }
        .hero-bullets { list-style:none; padding:0; margin:1.5rem 0 0; }
        .hero-bullets li { display:flex; align-items:center; gap:0.6rem; margin-bottom:0.65rem; font-size:15px; }
        .hero-badge { display:inline-flex; align-items:center; gap:0.4rem; padding:0.35rem 0.85rem; border-radius:999px; border:1px solid rgba(255,255,255,0.4); font-size:13px; background:rgba(255,255,255,0.1); }

        .form-pane { min-height:100vh; background:linear-gradient(180deg,#fafdff 0%,#f3f6fb 100%); }
        .form-card { width:100%; max-width:460px; background:rgba(255,255,255,0.95); border:1px solid rgba(0,0,0,0.05); box-shadow:0 25px 45px rgba(15,30,45,0.08); border-radius:32px; padding:2.75rem 2.5rem; }
        .form-hero-heading { display:flex; align-items:center; gap:12px; margin-bottom:0.5rem; }
        .form-hero-logo { width:52px; height:52px; border-radius:16px; background:linear-gradient(160deg,#e6f4ff,#c9e6ff); display:flex; align-items:center; justify-content:center; box-shadow:0 10px 25px rgba(0,0,0,0.08); }

        .form-control { border-radius:999px; padding:0.6rem 1rem; border:1px solid #d8dde6; box-shadow:none!important; transition:border-color 180ms ease, box-shadow 180ms ease; }
        .form-control:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(0,117,181,0.15)!important; }
        .btn-container {
            display:flex; align-items:center; justify-content:center; position:relative; width:100%; padding:0.6rem 1.25rem;
            border-radius:999px; border:none; background:linear-gradient(135deg,var(--primary) 0%, var(--primary-dark) 100%);
            color:#fff; font-weight:600; letter-spacing:0.3px; transition:transform 180ms ease, box-shadow 180ms ease;
        }
        .btn-container:hover { box-shadow:0 15px 30px rgba(0,70,120,0.25); }
        .btn-container:active { transform:translateY(1px); }
        .login-icon { position:absolute; right:12px; top:50%; transform:translateY(-50%); width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,0.95); display:flex; align-items:center; justify-content:center; color:var(--primary); }
        .forgot { color:#7a8ba1; text-decoration:none; font-size:14px; }
        .forgot:hover { color:var(--primary); }

        @media (max-width: 991.98px) {
            .hero-pane { min-height:45vh; }
            .hero-content { padding:2rem 1.5rem 3rem; }
            .form-pane { min-height:auto; padding:3rem 1.5rem; }
            .form-card { padding:2rem 1.75rem; border-radius:24px; }
            .title-font { font-size:34px; }
        }
        @media (max-width: 575.98px) {
            .form-card { padding:1.75rem; }
        }
    </style>
</head>
<body class="m-0">

<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">

        <!-- Left image column -->
        <div class="col-12 col-md-6 hero-pane d-flex align-items-center">
            <div class="hero-content w-100">
                <div class="hero-badge"><i class="fa-solid fa-sailboat"></i> SeaLedger dashboard</div>
                <h1 class="title-font display-5 text-white mt-3 mb-2">Sign in to continue.</h1>
                <p class="text-white-50 mb-3" style="max-width:360px;">Access your dashboard, keep records up to date, and stay on top of marketplace activity.</p>
                <ul class="hero-bullets">
                    <li><i class="fa-solid fa-circle-check"></i> Review recent orders and updates</li>
                    <li><i class="fa-solid fa-pen-to-square"></i> Manage listings or catch entries</li>
                    <li><i class="fa-solid fa-bell"></i> See alerts from buyers and admins</li>
                </ul>
            </div>
        </div>

        <!-- Right form column -->
        <div class="col-12 col-md-6 form-pane d-flex align-items-center justify-content-center">
            <div class="form-card">
                    <div class="form-hero-heading mb-3">
                        <img src="{{ asset('images/logo.png') }}" alt="SeaLedger logo" class="form-hero-logo" />
                    <div>
                        <h3 class="mb-1 title-font" style="font-size:32px;">Welcome back.</h3>
                        <p class="text-muted-sm mb-0">Log in to see today’s catch dashboards.</p>
                    </div>
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