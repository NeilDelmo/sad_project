<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name', 'Register') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Koulen&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --primary:#0075B5;
            --primary-dark:#004c81;
            --text-dark:#161523;
        }
        body { font-family:"Jost",sans-serif; background:#f3f6fb; }
        .title-font { font-family:"Koulen",sans-serif; font-weight:400; letter-spacing:0.4px; color:var(--primary); }
        .subhead-font { font-family:"Koulen",sans-serif; font-weight:400; color:var(--primary); }
        .text-muted-sm { color:#6f7d8c; font-size:14px; }

        .hero-pane {
            background-image:linear-gradient(135deg,rgba(0,117,181,0.88),rgba(0,65,126,0.85)),url("{{ asset('images/background.png') }}");
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
            width:280px;
            height:280px;
            border-radius:38% 62% 60% 40% / 45% 70% 30% 55%;
            background:rgba(255,255,255,0.1);
            top:-70px;
            right:-40px;
        }
        .hero-content { position:relative; z-index:1; padding:3.5rem 3rem; }
        .hero-badge { display:inline-flex; align-items:center; gap:0.4rem; padding:0.35rem 0.85rem; border-radius:999px; border:1px solid rgba(255,255,255,0.4); font-size:13px; background:rgba(255,255,255,0.12); }
        .hero-bullets { list-style:none; padding:0; margin:1.5rem 0 0; }
        .hero-bullets li { display:flex; align-items:center; gap:0.6rem; margin-bottom:0.6rem; font-size:15px; }

        .form-pane { min-height:100vh; background:linear-gradient(180deg,#fafdff 0%,#f3f6fb 100%); }
        .form-card { width:100%; max-width:520px; background:rgba(255,255,255,0.96); border:1px solid rgba(0,0,0,0.04); box-shadow:0 25px 45px rgba(15,30,45,0.09); border-radius:32px; padding:2.5rem 2.25rem; }
        .form-hero-heading { display:flex; align-items:center; gap:12px; }
        .form-hero-logo { width:52px; height:52px; border-radius:16px; background:linear-gradient(160deg,#e6f4ff,#c9e6ff); display:flex; align-items:center; justify-content:center; box-shadow:0 8px 22px rgba(0,0,0,0.1); }
        .text-font { font-size:15px; color:var(--text-dark); }

        .btn-container {
            display:flex; align-items:center; justify-content:center; position:relative; width:100%; padding:0.65rem 1.25rem;
            border-radius:999px; border:none; background:linear-gradient(135deg,var(--primary) 0%, var(--primary-dark) 100%);
            color:#fff; font-weight:600; letter-spacing:0.3px; transition:transform 180ms ease, box-shadow 180ms ease;
        }
        .btn-container:hover { box-shadow:0 15px 30px rgba(0,70,120,0.25); }
        .btn-container:active { transform:translateY(1px); }

        /* logo */
        .bg .logo {
            position: absolute;
            top: 1rem;
            left: 1rem;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.92);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
            z-index: 30;
        }

        /* not necessary once changed to actual 
        logo image */
        .bg .logo i {
            color: #0074b3;
            font-size: 20px;
        }

        .login-icon {
            width: 38px; height: 38px; background-color: rgba(255,255,255,0.95); display:flex; align-items:center; justify-content:center; border-radius:50%; border: none; margin-left:auto; color:var(--primary);
        }

        /* login icon animation */
        .login-icon i {
            font-size: 12px;
            color: #0074b3;
            transition: transform 0.6s ease, opacity 0.6s ease;
            transform: translateX(0);
            opacity: 0.95;
        }

        .btn-container:hover .login-icon i {
            transform: translateX(6px);
            opacity: 1;
        }

        /* show / hide pass */
        #togglePasswordIcon {
            color: #ced4da;
            transition: color 220ms ease, transform 180ms ease;
        }

        .position-relative input.form-control:focus+button #togglePasswordIcon {
            color: #457492;
        }

        /* confirm password toggle icon (same behavior as main password) */
        #toggleConfirmPasswordIcon {
            color: #ced4da;
            transition: color 220ms ease, transform 180ms ease;
        }

        .position-relative input.form-control:focus+button #toggleConfirmPasswordIcon {
            color: #457492;
        }


        .remember {
            color: #939494;
            text-decoration: none;
        }

        /* forgot pass */
        .forgot {
            color: #939494;
            text-decoration: none;
            position: relative;
            transition: color 220ms ease;
        }

        .forgot::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -2px;
            height: 2px;
            width: 100%;
            background-color: currentColor;
            transform-origin: left center;
            transform: scaleX(0);
            transition: transform 260ms cubic-bezier(.2, .8, .2, 1);
            opacity: 0.95;
        }

        .forgot:hover {
            color: #457492;
        }

        .forgot:hover::after {
            transform: scaleX(1);
        }

        /* checkbox - center align w label */
        .checkbox.d-flex .checkbox-input {
            margin-top: 0;
            vertical-align: middle;
            margin-right: .375rem;
        }

        .checkbox.d-flex .checkbox-label {
            margin-bottom: 0;
            display: inline-flex;
            align-items: center;
        }


        .form-control {
            border-radius:999px;
            box-shadow:none !important;
            outline:none;
            transition:border-color 180ms ease, box-shadow 180ms ease;
            border:1px solid #d8dde6;
            padding:0.6rem 1rem;
        }

        .form-control:focus {
            border-color: var(--primary) !important;
            box-shadow:0 0 0 3px rgba(0,117,181,0.15) !important;
        }


        #togglePassword {
            background: transparent;
            border: 0;
            padding: 0;
        }

        #togglePassword:focus {
            outline: none;
            box-shadow: none;
        }

        #togglePassword:focus-visible {
            outline: 2px solid rgba(69, 116, 146, 0.18);
            outline-offset: 3px;
        }


        /* phone num input */
        .phone-prefix {
            border-top-left-radius: 50rem;
            border-bottom-left-radius: 50rem;
            border-right: 0;
            background-color: #f8f9fa;
            color: #161523;
            padding: 0.375rem 0.75rem;
            line-height: 1.2;
        }

        .phone-input {
            border-top-right-radius: 50rem;
            border-bottom-right-radius: 50rem;
        }

        /* Make selects match the rounded-pill inputs */
        .form-select.rounded-pill {
            border-radius: 50rem;
            padding: 0.375rem 0.75rem;
            line-height: 1.2;
            height: 2.5rem;
            box-sizing: border-box;
            display: flex;
            align-items: center;
        }

        .form-select:focus {
            box-shadow: none !important;
            outline: none;
            border-color: #457492 !important;
        }

        .input-group .form-control:focus {
            box-shadow: none;
            border-color: #457492;
        }

        /* slide effect */
        #confirm-password-row {
            max-height: 0;
            opacity: 0;
            transform: translateY(-6px);
            overflow: hidden;
            transition: max-height 260ms cubic-bezier(.2, .9, .2, 1), opacity 260ms ease, transform 260ms ease;
        }

        #confirm-password-row.show {
            max-height: 160px;
            opacity: 1;
            transform: translateY(0);
        }

        /* show / hide pass button alignment */
        #togglePassword,
        #toggleConfirmPassword {
            right: 0.75rem !important;
            margin-right: 0 !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            padding: 0 !important;
        }
    </style>
</head>

<body class="m-0">

    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">

            <!-- Left BG Column -->
            <div class="col-12 col-md-6 hero-pane d-flex align-items-center">
                <div class="hero-content w-100">
                    <div class="hero-badge"><i class="fa-solid fa-compass"></i> Create your SeaLedger ID</div>
                    <h1 class="title-font display-5 text-white mt-3 mb-2">Set up your account.</h1>
                    <p class="text-white-50" style="max-width:420px;">Register once to share listings, receive orders, and access the same dashboard tools as everyone else.</p>
                    <ul class="hero-bullets">
                        <li><i class="fa-solid fa-life-ring"></i> Fill in basic profile details</li>
                        <li><i class="fa-solid fa-box"></i> Post catches or rental offers</li>
                        <li><i class="fa-solid fa-bell"></i> Get notified when someone reaches out</li>
                    </ul>
                </div>
            </div>

            <!-- Right Form Column -->
            <div class="col-12 col-md-6 form-pane d-flex align-items-center justify-content-center">
                <div class="form-card">
                        <div class="form-hero-heading mb-3">
                            <img src="{{ asset('images/logo.png') }}" alt="SeaLedger logo" class="form-hero-logo" />
                        <div>
                            <h3 class="mb-1 title-font" style="font-size:32px;">Start your journey.</h3>
                            <p class="text-muted-sm mb-0">Create your marketplace credentials.</p>
                        </div>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Username & Phone -->
                            <div class="mt-2 mb-3 w-100">
                                <div class="row g-2">
                                    <div class="col-12 col-sm-6">
                                        <label for="username" class="form-label text-font">Username</label>
                                        <input id="username" name="username" type="text"
                                            class="form-control rounded-pill" value="{{ old('username') }}" required
                                            autofocus>
                                        @error('username') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-12 col-sm-6">
                                        <label for="phone" class="form-label text-font">Phone</label>
                                        <div class="input-group">
                                            <span class="input-group-text phone-prefix text-font">+63</span>
                                            <input id="phone" name="phone" type="tel"
                                                class="form-control phone-input text-font" placeholder="9123456789"
                                                value="{{ old('phone') }}" inputmode="numeric" pattern="9[0-9]{9}" maxlength="10" minlength="10">
                                        </div>
                                        @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Email & User Type -->
                            <div class="mb-3 w-100">
                                <div class="row g-2">
                                    <div class="col-12 col-sm-6">
                                        <label for="email" class="form-label text-font">Email</label>
                                        <input id="email" name="email" type="email" class="form-control rounded-pill"
                                            value="{{ old('email', request('email')) }}" required>
                                        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-12 col-sm-6">
                                        <label for="user_type" class="form-label text-font">User Type</label>
                                        <select id="user_type" name="user_type"
                                            class="form-select rounded-pill text-font" required>
                                            <option value="" disabled {{ old('user_type') ? '' : 'selected' }}>Select a user type</option>
                                            <option value="fisherman" {{ old('user_type') === 'fisherman' ? 'selected' : '' }}>Fisherman</option>
                                            <option value="vendor" {{ old('user_type') === 'vendor' ? 'selected' : '' }}>Vendor</option>
                                            <option value="buyer" {{ old('user_type') === 'buyer' ? 'selected' : '' }}>Buyer</option>
                                        </select>
                                        @error('user_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3 w-100">
                                <label for="password" class="form-label text-font">Password</label>
                                <div class="position-relative">
                                    <input id="password" name="password" type="password"
                                        class="form-control pe-5 rounded-pill" required>
                                    <button class="btn btn-sm btn-link position-absolute end-0 top-50 translate-middle-y me-3"
                                        type="button" id="togglePassword" aria-label="Show password">
                                        <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                                    </button>
                                    @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div id="confirm-password-row" class="mb-3 w-100" aria-hidden="true">
                                <label for="password_confirmation" class="form-label text-font">Confirm Password</label>
                                <div class="position-relative">
                                    <input id="password_confirmation" name="password_confirmation" type="password"
                                        class="form-control pe-5 rounded-pill">
                                    <button class="btn btn-sm btn-link position-absolute end-0 top-50 translate-middle-y me-3"
                                        type="button" id="toggleConfirmPassword" aria-label="Show confirm password">
                                        <i class="fa-solid fa-eye" id="toggleConfirmPasswordIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Register Button -->
                            <button type="submit" class="btn-container rounded-pill my-3 w-100">
                                <span class="text text-center subhead-font">Register</span>
                                <span class="login-icon"><i class="fa-solid fa-fish-fins"></i></span>
                            </button>

                            <div class="text-font text-center" style="font-size: 13px;">
                                <span>Already have an account? </span>
                                <a href="{{ route('login') }}" class="forgot">Login</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        (function () {
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

                var confirmRow = document.getElementById('confirm-password-row');
                var confirmInput = document.getElementById('password_confirmation');
                var confirmToggle = document.getElementById('toggleConfirmPassword');
                var confirmIcon = document.getElementById('toggleConfirmPasswordIcon');

                var toggleConfirmVisibility = function () {
                    if (pwd.value.length > 0) {
                        confirmRow.classList.add('show');
                        confirmRow.setAttribute('aria-hidden', 'false');
                    } else {
                        confirmRow.classList.remove('show');
                        confirmRow.setAttribute('aria-hidden', 'true');
                        if (confirmInput) confirmInput.value = '';
                    }
                };

                pwd.addEventListener('input', toggleConfirmVisibility);
                toggleConfirmVisibility();

                if (confirmToggle && confirmInput && confirmIcon) {
                    confirmToggle.addEventListener('click', function () {
                        if (confirmInput.type === 'password') {
                            confirmInput.type = 'text';
                            confirmIcon.classList.replace('fa-eye', 'fa-eye-slash');
                            confirmToggle.setAttribute('aria-label', 'Hide confirm password');
                        } else {
                            confirmInput.type = 'password';
                            confirmIcon.classList.replace('fa-eye-slash', 'fa-eye');
                            confirmToggle.setAttribute('aria-label', 'Show confirm password');
                        }
                    });
                }
            }
        })();
    </script>
</body>
</html>
