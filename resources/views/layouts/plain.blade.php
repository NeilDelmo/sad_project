<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name','App') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body { background:#f5f7fa; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
        .container { max-width:880px; margin:0 auto; padding:24px 20px 60px; }
    </style>
</head>
<body>
    <div class="container">
        {{ $slot ?? '' }}
        @yield('content')
    </div>
    @stack('scripts')

    <!-- 100% privacy-first analytics -->
    <script data-collect-dnt="true" async src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
</body>
</html>
