<!DOCTYPE html>
<style>
/* Bring thread view into the same light-blue SeaLedger theme used in index/category */

body {
    background-color: #f8f9fa;
    color: #1B5E88;
    font-family: Arial, sans-serif;
}

/* Cards */
.forum-card {
    background-color: #ffffff;
    border: 1px solid rgba(0,117,181,0.12);
    color: #1B5E88;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    border-radius: 12px;
}
.forum-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0,117,181,0.12);
}

/* Post / reply body */
.post-body, .reply-body {
    line-height: 1.7;
    color: #1B5E88;
}
.post-body img, .reply-body img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 12px 0;
    display: block;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    cursor: pointer;
    transition: transform 0.2s;
}
.post-body img:hover, .reply-body img:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 18px rgba(0,117,181,0.12);
}

/* Headings inside content */
.post-body h1, .post-body h2, .post-body h3,
.reply-body h1, .reply-body h2, .reply-body h3 {
    color: #0d6efd;
    margin-top: 16px;
    margin-bottom: 12px;
}

/* Attached images section */
.attached-images-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(0,117,181,0.06);
}
.attached-images-section h6 {
    color: #0075B5;
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
    border: 2px solid rgba(45,90,143,0.12);
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
    aspect-ratio: 1;
}
.attached-image-item:hover {
    transform: scale(1.03);
    box-shadow: 0 6px 20px rgba(0,117,181,0.10);
    border-color: rgba(0,117,181,0.18);
}
.attached-image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Form controls */
.form-control {
    background-color: #ffffff;
    border: 1px solid rgba(27,94,136,0.08);
    color: #1B5E88;
    padding: 12px;
    border-radius: 8px;
}
.form-control:focus {
    border-color: #0075B5;
    box-shadow: 0 0 0 0.15rem rgba(0,123,255,0.08);
}

/* Buttons */
.btn-primary-custom {
    background: linear-gradient(135deg, #0075B5 0%, #1B5E88 100%);
    border: none;
    color: white;
    padding: 8px 16px;
    font-weight: 600;
    border-radius: 8px;
}
.btn-primary-custom:hover {
    transform: translateY(-2px);
}

/* Reply submit button keep green but crisp */
.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    padding: 10px 24px;
    font-weight: 600;
    border-radius: 8px;
}
.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(32,201,151,0.12);
}

/* Image upload / preview */
.image-upload-area {
    background: #ffffff;
    border: 2px dashed rgba(27,94,136,0.06);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: all 0.2s ease;
    cursor: pointer;
}
.image-upload-area:hover, .image-upload-area.dragover {
    border-color: #0075B5;
    background: #f8fbfd;
}
.image-preview-container { display:flex; flex-wrap:wrap; gap:12px; margin-top:15px; }
.image-preview-item { width:100px; height:100px; border-radius:8px; overflow:hidden; border:2px solid rgba(27,94,136,0.06); }

/* Image modal (light themed, consistent with index) */
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
.image-modal.active { display:flex !important; }
.image-modal-content {
    position: relative;
    width: 900px;
    height: 700px;
    max-width: 90vw;
    max-height: 90vh;
    background-color:  #0075B5;
    border-radius: 12px;
    border: 2px solid #1B5E88;
    box-shadow: 0 20px 50px rgba(27,94,136,0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 12px;
}
.image-modal-content img { max-width:100%; max-height:100%; object-fit:contain; display:block; }
.image-modal-close {
    position: absolute;
    top: -15px;
    right: -15px;
    color: #fff;
    font-size: 36px;
    font-weight: bold;
    cursor: pointer;
    z-index: 10001;
    width: 46px;
    height: 46px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #dc3545;
    border-radius: 50%;
    border: 3px solid #fff;
}
.image-modal-close:hover { transform: rotate(10deg) scale(1.05); }

/* Voting section */
.vote-section {
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 0;
    border-top: 1px solid rgba(27,94,136,0.06);
    margin-top:16px;
}
.vote-btn {
    background: transparent;
    border: 1px solid rgba(27,94,136,0.12);
    color: #1B5E88;
    padding: 8px 12px;
    border-radius: 8px;
    cursor:pointer;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:8px;
}
.vote-btn:hover {
    border-color: #0075B5;
    background: rgba(0,117,181,0.04);
}
.vote-btn.active-upvote {
    border-color: #28a745;
    background: rgba(40,167,69,0.08);
    color: #28a745;
}
.vote-btn.active-downvote {
    border-color: #dc3545;
    background: rgba(220,53,69,0.08);
    color: #dc3545;
}
.vote-count { color: #1B5E88; font-weight:700; font-size:16px; min-width:40px; text-align:center; }

/* Utilities */
.text-muted { color: #557a92 !important; }
.small { font-size: 0.875rem; }

/* Animations */
@keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
@keyframes zoomIn { from { transform: scale(0.9); opacity: 0 } to { transform: scale(1); opacity: 1 } }

@media (max-width: 992px) {
    .image-modal-content { width: 90%; height: 80vh; }
}
@media (max-width: 576px) {
    .image-modal-content { width: 95%; height: 70vh; padding:10px; }
}
</style>

<div id="forum-thread" class="animate-fadeIn" data-category="{{ $category_id }}">

    <div class="mb-4">
        <button id="backToCategory" class="btn btn-sm btn-primary-custom">
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
