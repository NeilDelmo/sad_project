<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger - Community Forum</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        body {
            background-color: #f8f9fa;
            color: #1B5E88;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
            padding: 15px 20px;
            box-shadow: 0 4px 12px rgba(27,94,136,0.06);
        }

        .nav-brand {
            color: white;
            font-size: 28px;
            font-weight: bold;
            font-family: "Koulen", sans-serif;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-logo {
            height: 40px;
            width: auto;
        }

        .nav-links {
            display: flex;
            gap: 10px;
            align-items: center;
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

        .forum-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        .forum-card {
            background-color: #ffffff;
            border: 1px solid #0075B5;
            color: #1B5E88;
            transition: all 0.25s ease;
            border-radius: 12px;
        }
        
        .forum-card:hover {
            background-color: #ffffff;
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(0,117,181,0.15);
        }
        
        .text-muted {
            color: #557a92 !important;
        }

        .text-secondary {
            color: #7A96AC !important;
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

        .welcome-section {
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(27,94,136,0.04);
            margin-bottom: 30px;
            border: 1px solid rgba(0,117,181,0.08);
        }

        .welcome-title {
            font-family: "Koulen", sans-serif;
            font-size: 48px;
            color: #1B5E88;
            margin-bottom: 15px;
        }

        .category-icon {
            font-size: 24px;
            color: #0075B5;
        }

        .category-title {
            color: #0075B5;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .category-stats {
            background: rgba(0,117,181,0.06);
            padding: 8px 12px;
            border-radius: 6px;
            display: inline-block;
            color: #1B5E88;
        }

        .tint-blue {
            color: #0d6efd !important; /* primary blue */
            background: rgba(13,110,253,0.06);
            padding: 6px 10px;
            border-radius: 6px;
            display: inline-block;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                width: 100%;
            }
            
            .welcome-title {
                font-size: 36px;
            }
        }

        /* Image Modal Styles */
        .image-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(27,94,136,0.6);
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }

        .image-modal.active {
            display: flex !important;
        }

        .image-modal-content {
            position: relative;
            width: 900px;
            height: 700px;
            max-width: 90vw;
            max-height: 90vh;
            background-color: #3a4ea0;
            border-radius: 12px;
            border: 2px solid #0075B5;
            box-shadow: 0 20px 50px rgba(27,94,136,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            animation: zoomIn 0.3s ease;
            padding: 15px;
        }

        .image-modal-content img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .image-modal-close {
            position: absolute;
            top: -15px;
            right: -15px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
            z-index: 10001;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #dc3545;
            border-radius: 50%;
            border: 3px solid #fff;
        }

        .image-modal-close:hover {
            background: #c82333;
            transform: rotate(90deg) scale(1.1);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes zoomIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 992px) {
            .image-modal-content {
                width: 90%;
                height: 80vh;
            }
        }

        @media (max-width: 576px) {
            .image-modal-content {
                width: 95%;
                height: 70vh;
                padding: 10px;
            }
        }

        /* Animation Styles */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-in;
        }

        .animate-fadeOut {
            animation: fadeOut 0.5s ease-out forwards;
        }

        .badge {
            font-weight: 500;
            padding: 4px 10px;
            background: rgba(0,117,181,0.08);
            color: #1B5E88;
        }

        /* Image-background variant for forum cards */
        .forum-card.bg-image {
            position: relative;
            color: #ffffff;
            /* keep fallback color */
            background-color: rgba(0,117,181,0.08);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(7,85,130,0.12);
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .forum-card.bg-image::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.18), rgba(0,0,0,0.36));
            z-index: 1;
            pointer-events: none;
        }
        /* make sure card content sits above overlay */
        .forum-card.bg-image > * { position: relative; z-index: 2; }

        /* Make text inside card high-contrast white */
        .forum-card.bg-image .category-title,
        .forum-card.bg-image .card-title,
        .forum-card.bg-image p,
        .forum-card.bg-image .text-muted {
            color: rgba(255,255,255,0.95) !important;
        }

        /* Adjust small UI pieces (badges, counts) */
        .forum-card.bg-image .badge,
        .forum-card.bg-image .thread-count {
            background: rgba(255,255,255,0.12);
            color: #fff;
            font-weight: 600;
        }

        /* Responsive tweaks if needed */
        @media (max-width: 768px) {
            .forum-card.bg-image { background-position: center top; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="nav-brand" href="{{ route('marketplace.index') }}" style="text-decoration: none;">
                <img src="{{ asset('images/logo.png') }}" alt="SeaLedger Logo" class="nav-logo">
                SeaLedger
            </a>
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
        <div class="welcome-section">
            <div class="welcome-title">🗣️ Community Forum</div>
            <p class="tint-blue" style="font-size: 20px; margin-bottom: 8px; font-weight: 500;">Connect with fellow fishermen and share your knowledge</p>
            <p style="font-size: 16px; color: #9fb3d2; margin-bottom: 0;">Join discussions, ask questions, and learn from the community</p>
        </div>

        <div id="forum-content">
            <div class="forum-card p-4">
                <h3 class="mb-4 tint-blue" style="font-size: 32px; font-weight: 700;">
                    <i class="bi bi-folder-open category-icon"></i> Forum Categories
                </h3>
                
                @if($categories->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-folder-x" style="font-size: 64px; color: #7A96AC;"></i>
                        <p class="text-muted mt-3" style="font-size: 18px;">No categories available yet.</p>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($categories as $cat)
                            <div class="col-md-6 col-lg-4">
                                <a href="{{ route('forums.category', $cat->id) }}" class="category-link">
                                    <div class="forum-card p-4 h-100 d-flex flex-column bg-image"
                                         style="background-image: url('{{ asset('images/forum-card-bg.jpg') }}');">
                                        <div class="mb-3">
                                            <h5 class="category-title d-flex align-items-center gap-2">
                                                <i class="bi bi-folder2-open"></i> 
                                                {{ $cat->name }}
                                            </h5>
                                        </div>
                                        <p class="text-muted flex-grow-1" style="font-size: 14px; line-height: 1.6;">
                                            {{ Str::limit($cat->description, 100) }}
                                        </p>
                                        <div class="category-stats mt-2" style="color: rgb(101, 160, 250)">
                                            <i class="bi bi-chat-dots"></i> 
                                            <strong>{{ $cat->threads_count }}</strong> 
                                            {{ Str::plural('thread', $cat->threads_count) }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Latest Discussions Section -->
        <div class="mt-5 animate-fadeIn">
            <h3 class="mb-4">
                <i class="bi bi-clock-history"></i> Latest Discussions
            </h3>
            
            @if(isset($latestThreads) && $latestThreads->count() > 0)
                <div class="row g-3">
                    @foreach($latestThreads as $thread)
                        <div class="col-12">
                            <div class="card forum-card thread-card" 
                                 data-thread="{{ $thread->id }}" 
                                 style="cursor: pointer;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title mb-2">
                                                <i class="bi bi-chat-dots text-info"></i>
                                                {{ $thread->title }}
                                            </h6>
                                            <p class="text-muted small mb-2">
                                                <span class="badge bg-secondary">{{ $thread->category->name }}</span>
                                                by <strong>{{ $thread->user->username }}</strong>
                                            </p>
                                            <p class="card-text text-secondary small mb-0">
                                                <i class="bi bi-reply"></i> {{ $thread->replies_count }} replies
                                                • <i class="bi bi-arrow-up"></i> {{ $thread->net_votes }} votes
                                                • <i class="bi bi-clock"></i> {{ $thread->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        @if($thread->thumbnail)
                                            <img src="{{ $thread->thumbnail }}" 
                                                 alt="Thread preview" 
                                                 class="ms-3"
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid #2d5a8f;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No discussions yet. Be the first to start a conversation!
                </div>
            @endif
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="image-modal">
        <div class="image-modal-content">
            <span class="image-modal-close" id="modalClose">&times;</span>
            <img id="modalImage" src="" alt="Full size image">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @include('forums.forum-script')
    <script>
    console.log('=== Global Modal Script Initializing ===');
    
    (function() {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        const closeBtn = document.getElementById('modalClose');
        const modalContent = document.querySelector('.image-modal-content');

        console.log('Modal elements:', { modal: !!modal, modalImg: !!modalImg, closeBtn: !!closeBtn });

        window.openImageModal = function(imageSrc) {
            console.log('🖼️ Opening modal with image:', imageSrc);
            if (modal && modalImg) {
                modal.classList.add('active');
                modalImg.src = imageSrc;
                document.body.style.overflow = 'hidden';
                console.log('✅ Modal opened!');
            } else {
                console.error('❌ Modal elements missing!');
            }
        };

        window.closeImageModal = function(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            console.log('🚪 Closing modal');
            if (modal && modalImg) {
                modal.classList.remove('active');
                modalImg.src = '';
                document.body.style.overflow = 'auto';
            }
        };

        document.addEventListener('click', function(e) {
            if (e.target.tagName === 'IMG') {
                const inPostBody = e.target.closest('.post-body');
                const inReplyBody = e.target.closest('.reply-body');
                
                if (inPostBody || inReplyBody) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('🎯 Inline image clicked:', e.target.src);
                    openImageModal(e.target.src);
                    return;
                }
            }

            const attachedItem = e.target.closest('.attached-image-item');
            if (attachedItem) {
                e.preventDefault();
                e.stopPropagation();
                const imgSrc = attachedItem.getAttribute('data-image-src');
                console.log('🎯 Attached image clicked:', imgSrc);
                openImageModal(imgSrc);
                return;
            }
        }, true);

        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                closeImageModal(e);
            });
        }

        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeImageModal(e);
                }
            });
        }

        if (modalContent) {
            modalContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
                closeImageModal();
            }
        });

        console.log('✅ Global Modal Script Ready');
    })();

    // Global Voting Functions
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    window.voteThread = async function(threadId, voteType) {
        try {
            const response = await fetch(`/forums/thread/${threadId}/vote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ vote_type: voteType })
            });

            const data = await response.json();

            if (data.success) {
                const voteSection = document.querySelector(`.vote-section[data-thread-id="${threadId}"]`);
                if (voteSection) {
                    const voteCount = voteSection.querySelector('.vote-count');
                    const upvoteBtn = voteSection.querySelector('.upvote-btn');
                    const downvoteBtn = voteSection.querySelector('.downvote-btn');

                    voteCount.textContent = data.net_votes;

                    upvoteBtn.classList.remove('active-upvote');
                    downvoteBtn.classList.remove('active-downvote');

                    if (data.user_vote === 'upvote') {
                        upvoteBtn.classList.add('active-upvote');
                    } else if (data.user_vote === 'downvote') {
                        downvoteBtn.classList.add('active-downvote');
                    }
                }
            }
        } catch (error) {
            console.error('Vote error:', error);
            alert('Failed to vote. Please try again.');
        }
    };

    window.voteReply = async function(replyId, voteType) {
        try {
            const response = await fetch(`/forums/reply/${replyId}/vote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ vote_type: voteType })
            });

            const data = await response.json();

            if (data.success) {
                const voteSection = document.querySelector(`.vote-section[data-reply-id="${replyId}"]`);
                if (voteSection) {
                    const voteCount = voteSection.querySelector('.vote-count');
                    const upvoteBtn = voteSection.querySelector('.upvote-btn');
                    const downvoteBtn = voteSection.querySelector('.downvote-btn');

                    voteCount.textContent = data.net_votes;

                    upvoteBtn.classList.remove('active-upvote');
                    downvoteBtn.classList.remove('active-downvote');

                    if (data.user_vote === 'upvote') {
                        upvoteBtn.classList.add('active-upvote');
                    } else if (data.user_vote === 'downvote') {
                        downvoteBtn.classList.add('active-downvote');
                    }
                }
            }
        } catch (error) {
            console.error('Vote error:', error);
            alert('Failed to vote. Please try again.');
        }
    };

    console.log('✅ Global Voting Functions Ready');
    </script>
</body>
</html>