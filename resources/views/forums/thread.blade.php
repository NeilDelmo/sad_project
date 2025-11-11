<style>
    body {
        background-color: #0b1d3a;
        color: #f1f3f5;
    }
    .forum-card {
        background-color: #132d55;
        border: 1px solid #1f3b6e;
        color: #f1f3f5;
        transition: all 0.25s ease;
    }
    .forum-card:hover {
        background-color: #1a3b70;
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.35);
    }
    .text-muted {
        color: #9fb3d2 !important;
    }
</style>

<div id="forum-thread" class="animate-fadeIn" data-category="{{ $category_id }}">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button id="backToCategory" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left"></i> Back 
        </button>
        <small class="text-muted">
            {{ $thread->replies->count() }} {{ Str::plural('comment', $thread->replies->count()) }}
        </small>
    </div>

    <!-- Main Thread -->
    <div class="card forum-card mb-4">
        <div class="card-body d-flex">
            <div class="text-center me-3 vote-buttons" data-type="thread" data-id="{{ $thread->id }}">
                <button class="btn btn-sm text-secondary upvote"><i class="bi bi-hand-thumbs-up"></i></button>
                <div class="fw-bold my-2 score {{ ($thread->upvotes - $thread->downvotes) > 0 ? 'text-success' : (($thread->upvotes - $thread->downvotes) < 0 ? 'text-danger' : 'text-secondary') }}">
                    {{ $thread->upvotes - $thread->downvotes }}
                </div>
                <button class="btn btn-sm text-secondary downvote"><i class="bi bi-hand-thumbs-down"></i></button>
            </div>

            <div class="flex-grow-1">
                <h5 class="fw-bold text-white mb-2">{{ $thread->title }}</h5>
                <p class="text-light">{{ $thread->body }}</p>
                <div class="border-top border-secondary pt-2 small text-muted">
                    <i class="bi bi-person"></i> {{ $thread->user->username }} •
                    <i class="bi bi-clock"></i> {{ $thread->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Replies -->
    <div class="mb-4">
        <h6 class="text-white mb-3"><i class="bi bi-chat-left-text text-info"></i> Comments</h6>
        @if($thread->replies->count() > 0)
            <div class="d-flex flex-column gap-3">
                @foreach($thread->replies as $reply)
                    <div class="card forum-card">
                        <div class="card-body d-flex">
                            <div class="text-center me-3 vote-buttons" data-type="reply" data-id="{{ $reply->id }}">
                                <button class="btn btn-sm text-secondary upvote"><i class="bi bi-hand-thumbs-up"></i></button>
                                <div class="fw-bold my-1 score {{ ($reply->upvotes - $reply->downvotes) > 0 ? 'text-success' : (($reply->upvotes - $reply->downvotes) < 0 ? 'text-danger' : 'text-secondary') }}">
                                    {{ $reply->upvotes - $reply->downvotes }}
                                </div>
                                <button class="btn btn-sm text-secondary downvote"><i class="bi bi-hand-thumbs-down"></i></button>
                            </div>
                            <div>
                                <p class="text-light mb-1">{!! $reply->body !!}</p>
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> {{ $reply->user->username }} •
                                    <i class="bi bi-clock"></i> {{ $reply->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-chat-left-dots fs-1 mb-2 d-block"></i>
                <p>No comments yet. Be the first to reply!</p>
            </div>
        @endif
    </div>

     <!-- Reply Form -->
    <div class="card forum-card mt-5">
        <div class="card-body">
            <h6 class="text-white mb-3"><i class="bi bi-reply text-success"></i> Add Your Comment</h6>
            <form id="reply-form" data-thread="{!! $thread->id !!}">
                @csrf
                <div class="mb-3">
                    <textarea id="reply-body" name="body" rows="6" class="form-control bg-secondary text-white border-0"
                              placeholder="Write something cool..."></textarea>
                </div>
                <div class="text-end">
                    <button class="btn btn-success">
                        Post Comment <i class="bi bi-send ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

