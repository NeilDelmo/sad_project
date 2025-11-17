<!DOCTYPE html>
<style>
    body { background-color: #0b1d3a; color: #f1f3f5; }
    .forum-card { 
        background-color: #132d55; 
        border: 1px solid #1f3b6e; 
        color:#f1f3f5; 
        transition:.25s;
        border-radius: 12px;
    }
    .forum-card:hover { 
        background-color:#1a3b70; 
        transform:translateY(-2px); 
        box-shadow:0 6px 16px rgba(0,0,0,0.4); 
    }
    .post-body, .reply-body {
        line-height: 1.7;
    }
    .post-body img, .reply-body img { 
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 12px 0;
        display: block;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        cursor: pointer;
        transition: transform 0.2s;
    }
    .post-body img:hover, .reply-body img:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 16px rgba(23, 162, 184, 0.4);
    }
    .post-body h1, .post-body h2, .post-body h3,
    .reply-body h1, .reply-body h2, .reply_body h3 { 
        color:#E7FAFE;
        margin-top: 16px;
        margin-bottom: 12px;
    }
    .post-body p, .reply-body p {
        line-height: 1.7;
        margin-bottom: 12px;
    }
    .post-body ul, .post-body ol,
    .reply-body ul, .reply_body ol {
        margin-left: 20px;
        margin-bottom: 12px;
    }
    .attached-images-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #2d5a8f;
    }
    .attached-images-section h6 {
        color: #17a2b8;
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .attached-images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }
    .attached-image-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #2d5a8f;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        aspect-ratio: 1;
    }
    .attached-image-item:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 16px rgba(23, 162, 184, 0.4);
        border-color: #17a2b8;
    }
    .attached-image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block !important;
        margin: 0 !important;
        box-shadow: none !important;
    }
    .form-control {
        background-color: #1a3b70;
        border: 1px solid #2d5a8f;
        color: #f1f3f5;
        padding: 12px;
        border-radius: 8px;
    }
    .form-control:focus {
        background-color: #1e4580;
        border-color: #17a2b8;
        color: #f1f3f5;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
    }
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        padding: 10px 24px;
        font-weight: 600;
    }
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }
    .image-upload-area {
        background: #1a3b70;
        border: 2px dashed #2d5a8f;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .image-upload-area:hover {
        border-color: #17a2b8;
        background: #1e4580;
    }
    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 15px;
    }
    .image-preview-item {
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #2d5a8f;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block !important;
    }
    .image-preview-remove {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 18px;
        line-height: 1;
        transition: all 0.2s ease;
        z-index: 10;
    }
    .image-preview-remove:hover {
        background: rgba(220, 53, 69, 1);
        transform: scale(1.1);
    }
    /* Image modal with fixed dimensions */
    .image-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.95);
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s ease;
    }
    .image-modal.active {
        display: flex;
    }
    .image-modal-content {
        position: relative;
        width: 900px;
        height: 700px;
        background-color: #1a3b70;
        border-radius: 12px;
        border: 3px solid #2d5a8f;
        box-shadow: 0 0 50px rgba(0,0,0,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        animation: zoomIn 0.3s ease;
    }
    .image-modal-content img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        padding: 10px;
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
        text-shadow: 0 0 10px rgba(0,0,0,0.8);
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
    
    /* Responsive modal for smaller screens */
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
        }
    }

    .vote-section {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-top: 1px solid #2d5a8f;
        margin-top: 16px;
    }
    .vote-btn {
        background: transparent;
        border: 2px solid #2d5a8f;
        color: #9fb3d2;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }
    .vote-btn:hover {
        border-color: #17a2b8;
        background: rgba(23, 162, 184, 0.1);
    }
    .vote-btn.active-upvote {
        border-color: #28a745;
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
    }
    .vote-btn.active-downvote {
        border-color: #dc3545;
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
    }
    .vote-count {
        color: #E7FAFE;
        font-weight: 700;
        font-size: 18px;
        min-width: 40px;
        text-align: center;
    }
</style>

<div id="forum-thread" class="animate-fadeIn" data-category="{{ $category_id }}">

    <div class="mb-4">
        <button id="backToCategory" class="btn btn-sm btn-outline-light">
            <i class="bi bi-arrow-left"></i> Back to Category
        </button>
    </div>

    <div class="card forum-card mb-4">
        <div class="card-body">
            <h3 class="card-title mb-3">{{ $thread->title }}</h3>
            <p class="text-muted small mb-3">
                By <strong>{{ $thread->user->username }}</strong> • 
                {{ $thread->created_at->diffForHumans() }}
            </p>
            <div class="post-body" data-thread-id="{{ $thread->id }}">
                {!! $thread->body_without_marker !!}
            </div>
            
            <!-- Thread Voting -->
            @auth
            <div class="vote-section" data-thread-id="{{ $thread->id }}">
                <button class="vote-btn upvote-btn {{ $thread->user_vote === 'upvote' ? 'active-upvote' : '' }}" 
                        onclick="voteThread({{ $thread->id }}, 'upvote')">
                    <i class="bi bi-arrow-up-circle-fill"></i> Upvote
                </button>
                <span class="vote-count">{{ $thread->net_votes }}</span>
                <button class="vote-btn downvote-btn {{ $thread->user_vote === 'downvote' ? 'active-downvote' : '' }}" 
                        onclick="voteThread({{ $thread->id }}, 'downvote')">
                    <i class="bi bi-arrow-down-circle-fill"></i> Downvote
                </button>
            </div>
            @endauth
            
            @if(count($thread->all_images) > 0)
                <div class="attached-images-section">
                    <h6><i class="bi bi-paperclip"></i> All Attached Images ({{ count($thread->all_images) }})</h6>
                    <div class="attached-images-grid">
                        @foreach($thread->all_images as $image)
                            <div class="attached-image-item" data-image-src="{{ $image }}">
                                <img src="{{ $image }}" alt="Attached image">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="mb-4">
        <h5>{{ $thread->replies->count() }} Reply(ies)</h5>
    </div>

    @if($thread->replies->count() > 0)
        @foreach($thread->replies as $reply)
            <div class="card forum-card mb-3">
                <div class="card-body">
                    <p class="text-muted small mb-2">
                        <strong>{{ $reply->user->username }}</strong> • 
                        {{ $reply->created_at->diffForHumans() }}
                    </p>
                    <div class="reply-body" data-reply-id="{{ $reply->id }}">
                        {!! $reply->body_without_marker !!}
                    </div>
                    
                    <!-- Reply Voting -->
                    @auth
                    <div class="vote-section" data-reply-id="{{ $reply->id }}">
                        <button class="vote-btn upvote-btn {{ $reply->user_vote === 'upvote' ? 'active-upvote' : '' }}" 
                                onclick="voteReply({{ $reply->id }}, 'upvote')">
                            <i class="bi bi-arrow-up-circle-fill"></i> Upvote
                        </button>
                        <span class="vote-count">{{ $reply->net_votes }}</span>
                        <button class="vote-btn downvote-btn {{ $reply->user_vote === 'downvote' ? 'active-downvote' : '' }}" 
                                onclick="voteReply({{ $reply->id }}, 'downvote')">
                            <i class="bi bi-arrow-down-circle-fill"></i> Downvote
                        </button>
                    </div>
                    @endauth
                    
                    @if(count($reply->all_images) > 0)
                        <div class="attached-images-section">
                            <h6><i class="bi bi-paperclip"></i> All Attached Images ({{ count($reply->all_images) }})</h6>
                            <div class="attached-images-grid">
                                @foreach($reply->all_images as $image)
                                    <div class="attached-image-item" data-image-src="{{ $image }}">
                                        <img src="{{ $image }}" alt="Attached image">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">
            No replies yet. Be the first to reply!
        </div>
    @endif

    <div class="card forum-card">
        <div class="card-body">
            <h5 class="mb-3">Post a Reply</h5>
            <form id="reply-form" data-thread="{{ $thread->id }}">
                @csrf
                <div class="mb-3">
                    <label for="reply-body" class="form-label">Your Message</label>
                    <textarea class="form-control" id="reply-body" name="body" rows="5" placeholder="Write your reply..."></textarea>
                    <small class="text-muted">You can paste or insert images directly in the editor above</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-paperclip"></i> Or Attach Additional Images
                    </label>
                    <small class="d-block text-muted mb-2">Images uploaded here will appear in the "All Attached Images" section</small>
                    <div id="reply-image-upload" class="image-upload-area">
                        <input type="file" id="reply-image-input" accept="image/*" multiple style="display: none;">
                        <i class="bi bi-cloud-upload" style="font-size: 2.5rem; color: #17a2b8;"></i>
                        <p class="mb-0 mt-2">Click to upload or drag & drop</p>
                        <small class="text-muted">PNG, JPG, GIF, WEBP up to 5MB each</small>
                    </div>
                    <div id="reply-image-previews" class="image-preview-container"></div>
                    <input type="hidden" id="reply-image-urls" name="image_urls">
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-send"></i> Post Reply
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

async function voteThread(threadId, voteType) {
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
    } catch (error) {
        console.error('Vote error:', error);
    }
}

async function voteReply(replyId, voteType) {
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
    } catch (error) {
        console.error('Vote error:', error);
    }
}
</script>

