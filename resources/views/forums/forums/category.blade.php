<div id="forum-category" class="animate-fadeIn" data-category="{{ $category->id }}">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div>
                <h3 class="mb-0 text-white">{{ $category->name }}</h3>
                <small class="text-muted">{{ $category->description }}</small>
            </div>
        </div>
        <small class="text-muted">
            {{ $category->threads->count() }} {{ Str::plural('thread', $category->threads->count()) }}
        </small>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <button id="backToCategories" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Categories
            </button>
        </div>
    </div>
    <!-- New Thread -->
    <div class="card forum-card mb-4">
        <div class="card-body">
            <h5 class="fw-semibold mb-3">
                <i class="bi bi-pencil-square text-success"></i> Start New Discussion
            </h5>
            <form id="new-thread-form" data-category="{!! $category->id !!}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="title" class="form-control bg-secondary text-white border-0"
                           placeholder="What's your question or topic?" required>
                </div>
                <div class="mb-3">
                    <textarea id="new-thread-body" name="body" rows="4" class="form-control bg-secondary text-white border-0"
          placeholder="Describe your topic in detail..."></textarea>
                </div>
                <div class="text-end">
                    <button class="btn btn-success">
                        Post Thread <i class="bi bi-send ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Threads -->
    @if($category->threads->count() > 0)
        <div class="d-flex flex-column gap-3">
            @foreach($category->threads as $thread)
                <div class="thread-card card forum-card p-3" data-thread="{{ $thread->id }}" style="cursor: pointer;">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-info mb-2"><i class="bi bi-chat-dots"></i> {{ $thread->title }}</h5>
                            <p class="text-muted small mb-2">{!! Str::limit($thread->body, 120) !!}</p>
                            <div class="text-muted small">
                                <i class="bi bi-person"></i> {{ $thread->user->username }} •
                                <i class="bi bi-clock"></i> {{ $thread->created_at->diffForHumans() }} •
                                <i class="bi bi-chat-left-text"></i>
                                {{ $thread->replies_count ?? $thread->replies->count() }}
                                {{ Str::plural('reply', $thread->replies_count ?? $thread->replies->count()) }}
                            </div>
                        </div>
                        <div class="align-self-center text-secondary">
                            <i class="bi bi-chevron-right fs-5"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center text-muted py-5">
            <i class="bi bi-inboxes fs-1 mb-2 d-block"></i>
            <p>No discussions yet. Be the first to start one!</p>
        </div>
    @endif
</div>
