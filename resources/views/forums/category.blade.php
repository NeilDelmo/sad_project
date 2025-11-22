<style>
    /* Base styles */
body {
    background-color: #f8f9fa;
    color: #1B5E88;
}

a {
    color: #0075B5;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

/* Button styles */
.btn-primary-custom {
    background: linear-gradient(135deg, #0075B5 0%, #1B5E88 100%);
    border: none;
    color: white;
    padding: 10px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 117, 181, 0.4);
}

/* Form control styles */
.form-control {
    background-color: #ffffff;
    border: 1px solid rgba(27,94,136,0.08);
    color: #1B5E88;
    padding: 12px;
    border-radius: 8px;
}
.form-control:focus {
    border-color: #0075B5;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.form-control::placeholder {
    color: #7A96AC;
}

/* Thread preview styles */
.thread-preview-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #0075B5;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    flex-shrink: 0;
}

/* Flexbox utility classes */
.d-flex {
    display: flex !important;
}
.justify-content-between {
    justify-content: space-between !important;
}
.align-items-center {
    align-items: center !important;
}
.gap-3 {
    gap: 1rem !important;
}

/* Card styles */
.card, .forum-card {
    background-color: #ffffff;
    border: 1px solid #0075B5;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease, color 0.25s ease;
}
.card:hover, .forum-card:hover {
    background-color: #ffffff;
    color: #1B5E88;
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,117,181,0.14);
}

/* Modal styles */
.modal-content {
    background-color: #ffffff;
    color: #1B5E88;
    border-radius: 8px;
}
.modal-header {
    border-bottom: 1px solid rgba(27,94,136,0.08);
}
.modal-title {
    margin-bottom: 0;
}

/* Pagination styles */
.pagination {
    gap: 8px;
}
.page-link {
    background-color: #ffffff;
    border: 1px solid rgba(27,94,136,0.08);
    color: #1B5E88;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.page-link:hover {
    background-color: #f8f9fa;
    border-color: #0075B5;
    color: #0075B5;
}
.page-item.active .page-link {
    background-color: #0075B5;
    border-color: #0075B5;
    color: #fff;
}
.page-item.disabled .page-link {
    background-color: #e9ecef;
    border-color: rgba(27,94,136,0.08);
    color: #7A96AC;
}

/* Search container styles */
.search-container {
    display: flex;
    align-items: center;
    gap: 0;
    position: relative;
}
.search-input {
    background-color: #ffffff;
    border: 1px solid rgba(27,94,136,0.08);
    color: #1B5E88;
    padding: 8px 16px;
    border-radius: 8px 0 0 8px;
    min-width: 250px;
    transition: all 0.3s ease;
}
.search-input:focus {
    border-color: #0075B5;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.search-input::placeholder {
    color: #7A96AC;
}
.search-btn {
    background-color: #0075B5;
    border: 1px solid #0075B5;
    color: white;
    padding: 8px 16px;
    border-radius: 0 8px 8px 0;
    cursor: pointer;
    transition: all 0.3s ease;
}
.search-btn:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
.clear-search-btn {
    background: transparent;
    border: none;
    color: #dc3545;
    font-size: 20px;
    cursor: pointer;
    padding: 0 8px;
    margin-left: -40px;
    z-index: 10;
    transition: all 0.3s ease;
}
.clear-search-btn:hover {
    color: #c82333;
    transform: scale(1.2);
}

/* Image upload styles */
.image-upload-area {
    background: #ffffff;
    border: 2px dashed rgba(27,94,136,0.06);
    border-radius: 12px;
    padding: 30px 20px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    user-select: none;
    position: relative;
}
.image-upload-area:hover {
    border-color: #0075B5;
    background: #f8fbfd;
}
.image-upload-area.dragover {
    border-color: #0075B5;
    background: #f8fbfd;
}
.image-upload-area input[type="file"] {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

/* Image preview styles */
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
    border: 2px solid rgba(27,94,136,0.06);
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.image-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
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

/* Misc */
.alert {
    border-radius: 8px;
}
.text-muted {
    color: #557a92 !important;
}
.small {
    font-size: 0.875rem;
}
</style>

<div id="forum-category" class="animate-fadeIn" data-category="{{ $category->id }}">

    <div class="mb-4">
        <button id="backToCategories" class="btn btn-sm btn-primary-custom">
            <i class="bi bi-arrow-left"></i> Back to Categories
        </button>
    </div>

    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-1">{{ $category->name }}</h2>
            <p class="text-muted mb-0">{{ $category->description }}</p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <!-- Search Bar -->
            <div class="search-container">
                <input type="text" 
                       id="searchThreads" 
                       class="search-input" 
                       placeholder="Search threads..." 
                       value="{{ $search ?? '' }}">
                <button id="searchBtn" class="search-btn">
                    <i class="bi bi-search"></i>
                </button>
                <button id="clearSearchBtn" class="clear-search-btn" title="Clear search" style="display: {{ $search ? 'block' : 'none' }};">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
            
            <select id="sortThreads" class="sort-dropdown">
                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest First</option>
                <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                <option value="best" {{ $sort === 'best' ? 'selected' : '' }}>Best (Most Upvotes)</option>
            </select>
            
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#newThreadModal">
                <i class="bi bi-plus-circle"></i> New Thread
            </button>
        </div>
    </div>

    <!-- Show search results info -->
    <div id="searchResultsInfo" class="alert alert-info d-flex justify-content-between align-items-center" style="display: {{ $search ? 'flex' : 'none' }} !important;">
        <span>
            <i class="bi bi-search"></i> Showing results for: <strong id="searchTerm">"{{ $search }}"</strong> 
            (<span id="searchCount">{{ $threads->total() }}</span> {{ Str::plural('thread', $threads->total()) }} found)
        </span>
        <button id="clearSearchBtn2" class="btn btn-sm btn-outline-dark">
            Clear Search
        </button>
    </div>

    <!-- New Thread Modal -->
    <div class="modal fade" id="newThreadModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background-color:#132d55; color:#f1f3f5;">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Create New Thread</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="new-thread-form" data-category="{{ $category->id }}">
                        @csrf
                        <div class="mb-3">
                            <label for="thread-title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="thread-title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="new-thread-body" class="form-label">Body</label>
                            <textarea class="form-control" id="new-thread-body" name="body" rows="6"></textarea>
                            <small class="text-muted">You can paste or insert images directly in the editor above</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-paperclip"></i> Or Attach Additional Images
                            </label>
                            <small class="d-block text-muted mb-2">Images uploaded here will appear in the "All Attached Images" section</small>
                            <div id="thread-image-upload" class="image-upload-area">
                                <input type="file" id="thread-image-input" accept="image/*" multiple style="display: none;">
                                <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #17a2b8;"></i>
                                <p class="mb-0 mt-2">Click to upload or drag & drop</p>
                                <small class="text-muted">PNG, JPG, GIF, WEBP up to 5MB each</small>
                            </div>
                            <div id="thread-image-previews" class="image-preview-container"></div>
                            <input type="hidden" id="thread-image-urls" name="image_urls">
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-send"></i> Post Thread
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h5>{{ $threads->total() }} Thread(s)</h5>
        <span class="text-muted">Page {{ $threads->currentPage() }} of {{ $threads->lastPage() }}</span>
    </div>

    @if($threads->count() > 0)
        @foreach($threads as $thread)
            <div class="card forum-card mb-3 thread-card" data-thread="{{ $thread->id }}" style="cursor:pointer;">
                <div class="card-body">
                    <div class="thread-content-wrapper">
                        @if($thread->thumbnail)
                            <img src="{{ $thread->thumbnail }}" alt="Thread preview" class="thread-preview-image">
                        @endif
                        <div class="thread-text-content">
                            <h5 class="card-title mb-2">{{ $thread->title }}</h5>
                            <p class="text-muted small mb-2">
                                By <strong>{{ $thread->user->username }}</strong> • 
                                {{ $thread->created_at->diffForHumans() }}
                            </p>
                            <p class="card-text text-secondary small">
                                <i class="bi bi-chat-dots"></i> {{ $thread->replies->count() }} replies
                                @if($thread->image_count > 0)
                                    • <i class="bi bi-images"></i> {{ $thread->image_count }} {{ Str::plural('image', $thread->image_count) }}
                                @endif
                                • <i class="bi bi-arrow-up"></i> {{ $thread->net_votes }} votes
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $threads->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="alert alert-info">
            @if($search)
                No threads found matching "{{ $search }}". Try a different search term.
            @else
                No threads yet. Be the first to start a discussion!
            @endif
        </div>
    @endif
</div>

<script>
console.log('=== Category Search Script Loading ===');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    
    const categoryEl = document.getElementById('forum-category');
    const searchInput = document.getElementById('searchThreads');
    const sortDropdown = document.getElementById('sortThreads');
    const searchBtn = document.getElementById('searchBtn');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const clearSearchBtn2 = document.getElementById('clearSearchBtn2');
    
    console.log('Elements found:', {
        categoryEl: !!categoryEl,
        searchInput: !!searchInput,
        sortDropdown: !!sortDropdown,
        searchBtn: !!searchBtn,
        clearSearchBtn: !!clearSearchBtn
    });
    
    if (!categoryEl) {
        console.error('❌ forum-category element not found!');
        return;
    }
    
    if (!searchInput) {
        console.error('❌ searchThreads input not found!');
        return;
    }
    
    const categoryId = categoryEl.dataset.category;
    console.log('Category ID:', categoryId);
    console.log('Initial search value:', searchInput.value);
    
    let searchTimeout;
    
    // Auto-search as you type
    searchInput.addEventListener('input', function(e) {
        const searchValue = e.target.value.trim();
        console.log('🔍 Input event - Search value:', searchValue);
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            console.log('⏰ Timeout fired, performing search...');
            performSearch(searchValue);
        }, 500);
    });
    
    // Search on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const searchValue = e.target.value.trim();
            console.log('⏎ Enter pressed - Search value:', searchValue);
            clearTimeout(searchTimeout);
            performSearch(searchValue);
        }
    });
    
    // Search button click
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            const searchValue = searchInput.value.trim();
            console.log('🔘 Search button clicked - Search value:', searchValue);
            performSearch(searchValue);
        });
    }
    
    // Clear search buttons
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            console.log('❌ Clear button 1 clicked');
            searchInput.value = '';
            performSearch('');
        });
    }
    
    if (clearSearchBtn2) {
        clearSearchBtn2.addEventListener('click', function() {
            console.log('❌ Clear button 2 clicked');
            searchInput.value = '';
            performSearch('');
        });
    }
    
    // Sort dropdown
    if (sortDropdown) {
        sortDropdown.addEventListener('change', function() {
            const searchValue = searchInput.value.trim();
            console.log('📊 Sort changed:', this.value, '- Search value:', searchValue);
            performSearch(searchValue);
        });
    }
    
    function performSearch(searchValue) {
        const sort = sortDropdown ? sortDropdown.value : 'newest';
        
        console.log('🚀 Performing search with:', {
            categoryId: categoryId,
            sort: sort,
            search: searchValue
        });
        
        let url = `/forums/category/${categoryId}?sort=${sort}`;
        if (searchValue && searchValue.length > 0) {
            url += `&search=${encodeURIComponent(searchValue)}`;
        }
        
        console.log('📍 Navigating to URL:', url);
        
        // Update clear button visibility
        if (clearSearchBtn) {
            clearSearchBtn.style.display = searchValue ? 'block' : 'none';
        }
        
        // Navigate to URL
        window.location.href = url;
    }
    
    console.log('✅ Category search initialized');
});
</script>