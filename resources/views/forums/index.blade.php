<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('bootstrap5/css/bootstrap.min.css') }}" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger - Community Forum</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        body {
            background-color: #0b1d3a;
            color: #f1f3f5;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Navbar styles from marketplace/dashboard */
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

        .nav-links {
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

        /* Forum content container */
        .forum-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        /* Forum content styles */
        .forum-card {
            background-color: #132d55;
            border: 1px solid #1f3b6e;
            color: #f1f3f5;
            transition: all 0.25s ease;
            border-radius: 8px;
        }
        
        .forum-card:hover {
            background-color: #1a3b70;
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.35);
        }
        
        .text-muted {
            color: #9fb3d2 !important;
        }

        .category-link {
            display: block;
            color: inherit;
            text-decoration: none;
        }

        .category-link:hover {
            color: inherit;
            text-decoration: none;
        }

        /* Welcome section */
        .welcome-section {
            background: #132d55;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            margin-bottom: 30px;
            border: 1px solid #1f3b6e;
        }

        .welcome-title {
            font-family: "Koulen", sans-serif;
            font-size: 42px;
            color: #E7FAFE;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="nav-brand" href="{{ route('marketplace.index') }}" style="text-decoration: none;">🐟 SeaLedger</a>
            <div class="nav-links">
                @if(Auth::check())
                    <a href="{{ route('fisherman.dashboard') }}" class="nav-link">
                        <i class="fa-solid fa-gauge-high"></i> Dashboard
                    </a>
                    <a href="{{ route('marketplace.shop') }}" class="nav-link">
                        <i class="fa-solid fa-fish"></i> Marketplace
                    </a>
                    <a href="{{ route('fishing-safety.public') }}" class="nav-link">
                        <i class="fa-solid fa-life-ring"></i> Safety Map
                    </a>
                    <a href="{{ route('forums.index') }}" class="nav-link active">
                        <i class="fa-solid fa-comments"></i> Community Forum
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer;">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-link">
                        <i class="fa-solid fa-right-to-bracket"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="nav-link">
                        <i class="fa-solid fa-user-plus"></i> Register
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <div class="forum-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-title">🗣️ Community Forum</div>
            <p style="font-size: 18px; color: #9fb3d2; margin-bottom: 0;">Connect with fellow fishermen and share your knowledge</p>
            <p style="font-size: 16px; color: #7A96AC;">Join discussions, ask questions, and learn from the community</p>
        </div>

        <div id="forum-content">
            <!-- Categories will be loaded here by JavaScript -->
            <div class="forum-card p-4">
                <h3 class="text-2xl font-bold mb-6" style="color: #E7FAFE; font-size: 28px; margin-bottom: 24px;">Forum Categories</h3>
                
                @if($categories->isEmpty())
                    <p class="text-muted">No categories available yet.</p>
                @else
                    <div class="row g-4">
                        @foreach($categories as $category)
                            <div class="col-md-6 col-lg-4">
                                <a href="javascript:void(0)" class="category-link" data-category-id="{{ $category->id }}">
                                    <div class="forum-card p-4 h-100">
                                        <h4 class="mb-3" style="color: #0075B5;">
                                            <i class="fa-solid fa-folder-open"></i> {{ $category->name }}
                                        </h4>
                                        <p class="text-muted mb-3" style="font-size: 14px;">
                                            {{ $category->description }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted" style="font-size: 12px;">
                                                {{ $category->threads_count }} thread{{ $category->threads_count !== 1 ? 's' : '' }}
                                            </span>
                                            <span style="color: #0075B5; font-weight: 600;">
                                                View →
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('forums.forum-script')
</body>
</html>
