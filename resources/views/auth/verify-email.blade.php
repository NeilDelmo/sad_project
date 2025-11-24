<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ config('app.name', 'SeaLedger') }} - Verify Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .alert-success { background:#d1f4e0;border-color:#a8e6c1;color:#0d6832;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem; }
        .btn-secondary { display:inline-flex;align-items:center;padding:.45rem 1.05rem;border-radius:50rem;border:1px solid #6c757d;background:transparent;color:#6c757d;font-size:14px;text-decoration:none;transition:all 180ms ease; }
        .btn-secondary:hover { background:#6c757d;color:#fff; }
    </style>
</head>
<body class="d-flex min-vh-100 align-items-center justify-content-center py-4">
    <div class="card shadow-sm p-4" style="max-width:500px;width:95%;">
        <div class="text-center mb-3">
            <img src="{{ asset('images/logo.png') }}" alt="SeaLedger logo" class="form-hero-logo mb-2" />
            <h1 class="title-font mb-0">Verify Email</h1>
        </div>
        <p class="text-font mb-4" style="color:#6c757d;font-size:14px;">Thanks for signing up! Click the link we emailed to you to verify your address. Didn't get it? Request another below.</p>
        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success">A new verification link has been sent to your email address.</div>
        @endif
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-container my-3 w-100">
                <span class="text" style="font-family:Koulen;color:#95beff;font-size:20px;">Resend Verification Email</span>
                <span class="login-icon"><i class="fa-solid fa-envelope"></i></span>
            </button>
        </form>
        <form method="POST" action="{{ route('logout') }}" class="text-center mt-2">
            @csrf
            <button type="submit" class="btn-secondary"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Log Out</button>
        </form>
    </div>
</body>
</html>
