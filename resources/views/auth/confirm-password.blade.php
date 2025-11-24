<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ config('app.name', 'SeaLedger') }} - Confirm Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Koulen&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <style>
        body { background: radial-gradient(circle at 30% 20%, #e6f4ff, #ffffff); }
        .title-font { font-family:"Koulen",sans-serif;font-weight:400;font-size:38px;color:#0074b3; }
        .text-font { font-family:"Jost",sans-serif;font-size:15px;color:#161523; }
        .form-hero-logo { width:56px; height:56px; border-radius:14px; background:#fff; box-shadow:0 6px 18px rgba(0,0,0,.12); padding:10px; border:1px solid rgba(13,110,253,0.12); }
        .btn-container { display:flex;align-items:center;justify-content:center;position:relative;width:100%;padding:.55rem 1.25rem;border-radius:50rem;cursor:pointer;border:none;background:linear-gradient(135deg,#0075B5 0%,#1B5E88 100%);color:#fff;transition:transform 180ms ease; }
        .btn-container:active { transform:translateY(1px); }
        .btn-container .login-icon { position:absolute;right:12px;top:50%;transform:translateY(-50%);width:36px;height:36px;background:rgba(195,228,233,.95);display:flex;align-items:center;justify-content:center;border-radius:50%;border:2px solid rgba(0,117,181,.12); }
        .btn-container .login-icon i { font-size:12px;color:#0075B5;transition:transform .28s ease; }
        .btn-container:hover .login-icon i { transform:translateX(4px); }
        .form-control { box-shadow:none!important;outline:none;transition:border-color 220ms ease; }
        .form-control:focus { border-color:#457492!important; }
        .rounded-pill { border-radius:50rem; }
        #togglePassword{background:transparent;border:0;padding:0;}
        #togglePasswordIcon{color:#ced4da;transition:color 220ms ease;}
        .position-relative input.form-control:focus+button #togglePasswordIcon{color:#457492;}

        /* Hide browser default password toggle */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }
    </style>
</head>
<body class="d-flex min-vh-100 align-items-center justify-content-center py-4">
    <div class="card shadow-sm p-4" style="max-width:460px;width:95%;">
        <div class="text-center mb-3">
            <img src="{{ asset('images/logo.png') }}" alt="SeaLedger logo" class="form-hero-logo mb-2" />
            <h1 class="title-font mb-0">Confirm Password</h1>
        </div>
        <p class="text-font mb-4" style="color:#6c757d;font-size:14px;">This is a secure area of the application. Please confirm your password before continuing.</p>
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div class="mb-3">
                <label for="password" class="form-label text-font">Password</label>
                <div class="position-relative">
                    <input id="password" name="password" type="password" required class="form-control pe-5 rounded-pill">
                    <button class="btn btn-sm btn-link position-absolute end-0 top-50 translate-middle-y me-3" type="button" id="togglePassword" aria-label="Show password"><i class="fa-solid fa-eye" id="togglePasswordIcon"></i></button>
                    @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
            <button type="submit" class="btn-container my-3 w-100">
                <span class="text" style="font-family:Koulen;color:#95beff;font-size:20px;">Confirm</span>
                <span class="login-icon"><i class="fa-solid fa-shield-halved"></i></span>
            </button>
        </form>
    </div>
    <script>
    (function(){
        var btn=document.getElementById('togglePassword');
        var input=document.getElementById('password');
        var icon=document.getElementById('togglePasswordIcon');
        if(!btn||!input||!icon) return;
        btn.addEventListener('click',function(){
            var isPwd=input.type==='password';
            input.type=isPwd?'text':'password';
            icon.classList.replace(isPwd?'fa-eye':'fa-eye-slash',isPwd?'fa-eye-slash':'fa-eye');
            btn.setAttribute('aria-label', (isPwd?'Hide':'Show') + ' password');
        });
    })();
    </script>
</body>
</html>
