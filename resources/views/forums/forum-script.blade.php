<!DOCTYPE html>

<script src="https://cdn.tiny.cloud/1/eeqmij25flkdyumonik6xoofb4fu0bb4sfg76ahpf6mogxet/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const forumContent = document.getElementById('forum-content');
    const csrf = document.querySelector("meta[name='csrf-token']").content;
    let uploadHandlersInitialized = false;

    // Add notification function
    function showNotification(message, type = 'info') {
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'success' ? 'alert-success' : 
                          type === 'warning' ? 'alert-warning' : 'alert-info';
        
        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        notification.style.zIndex = '9999';
        notification.style.minWidth = '300px';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    function initEditors() {
        if (!window.tinymce) return;
        tinymce.remove();
        ['#reply-body','#new-thread-body','#thread-editor'].forEach(sel => {
            if (document.querySelector(sel)) {
                const textarea = document.querySelector(sel);
                if (textarea) {
                    textarea.removeAttribute('required');
                }
                
                tinymce.init({
                    selector: sel,
                    menubar: false,
                    plugins: 'link image code lists',
                    toolbar: 'bold italic underline | bullist numlist | link image | code',
                    height: sel === '#reply-body' ? 250 : 300,
                    automatic_uploads: true,
                    images_upload_handler: async (blobInfo, progress) => {
                        return new Promise(async (resolve, reject) => {
                            const formData = new FormData();
                            formData.append('file', blobInfo.blob(), blobInfo.filename());

                            try {
                                const response = await fetch('{{ route("forums.upload-image") }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': csrf
                                    },
                                    body: formData
                                });

                                if (!response.ok) {
                                    throw new Error(`Upload failed: ${response.status}`);
                                }

                                const json = await response.json();
                                
                                if (json.location) {
                                    resolve(json.location);
                                } else {
                                    reject('Upload failed: No location returned');
                                }
                            } catch (err) {
                                reject('Upload failed: ' + err.message);
                            }
                        });
                    },
                    file_picker_types: 'image',
                    convert_urls: false,
                    relative_urls: false,
                    remove_script_host: false,
                    content_style: "body { font-family:Arial; font-size:14px; color:#212529; } img { max-width:100%; height:auto; border-radius:4px; }",
                    setup: (editor) => {
                        editor.on('init', () => {
                            console.log('TinyMCE initialized for:', sel);
                        });
                    }
                });
            }
        });
        
        if (!uploadHandlersInitialized) {
            setTimeout(() => {
                initImageUpload('thread');
                initImageUpload('reply');
                uploadHandlersInitialized = true;
            }, 100);
        }
    }

    function initImageUpload(type) {
        const uploadArea = document.getElementById(`${type}-image-upload`);
        const fileInput = document.getElementById(`${type}-image-input`);
        const previewContainer = document.getElementById(`${type}-image-previews`);
        const hiddenInput = document.getElementById(`${type}-image-urls`);

        if (!uploadArea || !fileInput || !previewContainer || !hiddenInput) {
            return;
        }

        let uploadedImages = [];
        let isUploading = false;

        const newUploadArea = uploadArea.cloneNode(true);
        uploadArea.parentNode.replaceChild(newUploadArea, uploadArea);
        const uploadAreaRef = newUploadArea;

        const newFileInput = fileInput.cloneNode(true);
        fileInput.parentNode.replaceChild(newFileInput, fileInput);
        const fileInputRef = newFileInput;

        uploadAreaRef.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (!isUploading) {
                fileInputRef.click();
            }
        });

        uploadAreaRef.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadAreaRef.classList.add('dragover');
        });

        uploadAreaRef.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadAreaRef.classList.remove('dragover');
        });

        uploadAreaRef.addEventListener('drop', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadAreaRef.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                await handleFiles(files);
            }
        });

        fileInputRef.addEventListener('change', async (e) => {
            if (e.target.files.length > 0) {
                await handleFiles(e.target.files);
            }
            e.target.value = '';
        });

        async function handleFiles(files) {
            if (isUploading) return;
            isUploading = true;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (!file.type.startsWith('image/')) continue;

                const formData = new FormData();
                formData.append('file', file);

                try {
                    const response = await fetch('/forums/upload-image', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf
                        },
                        body: formData
                    });
                    const data = await response.json();
                    if (data.location) {
                        uploadedImages.push(data.location);
                        addImagePreview(data.location);
                    }
                } catch (err) {
                    console.error('Upload failed:', err);
                }
            }

            updateHiddenInput();
            isUploading = false;
        }

        function addImagePreview(url) {
            const previewItem = document.createElement('div');
            previewItem.className = 'image-preview-item';
            previewItem.innerHTML = `
                <img src="${url}" alt="Preview">
                <button type="button" class="image-preview-remove" onclick="this.parentElement.remove(); window.updateImageUrls_${type}();">
                    ×
                </button>
            `;
            previewContainer.appendChild(previewItem);
        }

        function updateHiddenInput() {
            hiddenInput.value = JSON.stringify(uploadedImages);
        }

        function resetUpload() {
            uploadedImages = [];
            previewContainer.innerHTML = '';
            hiddenInput.value = '';
        }

        window[`reset_${type}_upload`] = resetUpload;
        window[`updateImageUrls_${type}`] = function() {
            uploadedImages = Array.from(previewContainer.querySelectorAll('img'))
                .map(img => img.src);
            updateHiddenInput();
        };
    }

    async function loadCategory(id, sort) {
        uploadHandlersInitialized = false;
        const res = await fetch(`/forums/category/${id}${sort ? '?sort=' + sort : ''}`);
        const html = await res.text();
        forumContent.innerHTML = html;
        initEditors();
    }

    async function loadThread(id) {
        uploadHandlersInitialized = false;
        const res = await fetch(`/forums/thread/${id}`);
        const html = await res.text();
        forumContent.innerHTML = html;
        initEditors();
        
        // Make sure modal handlers are ready after content load
        setTimeout(() => {
            console.log('Thread loaded, modal handlers should be active');
        }, 100);
    }

    forumContent.addEventListener('click', e => {
        const catLink = e.target.closest('.category-link');
        if (catLink) {
            e.preventDefault();
            loadCategory(catLink.href.split('/').pop());
            return;
        }
        const threadCard = e.target.closest('.thread-card');
        if (threadCard) {
            loadThread(threadCard.dataset.thread);
            return;
        }
        if (e.target.id === 'backToCategories' || e.target.closest('#backToCategories')) {
            window.location.href = '{{ route("forums.index") }}';
            return;
        }
        if (e.target.id === 'backToCategory' || e.target.closest('#backToCategory')) {
            const wrapper = document.getElementById('forum-thread');
            if (wrapper) loadCategory(wrapper.dataset.category);
        }
    });

    document.addEventListener('submit', async e => {
        const form = e.target;
        if (!['new-thread-form','reply-form'].includes(form.id)) return;
        e.preventDefault();
        e.stopPropagation();
        
        if (window.tinymce) tinymce.triggerSave();

        const isThread = form.id === 'new-thread-form';
        
        const titleInput = form.querySelector('input[name="title"]');
        const bodyTextarea = form.querySelector('textarea[name="body"]');
        
        if (isThread && titleInput && !titleInput.value.trim()) {
            showNotification('Please enter a title', 'error');
            titleInput.focus();
            return;
        }
        
        if (bodyTextarea && !bodyTextarea.value.trim()) {
            showNotification('Please enter a message', 'error');
            const editorId = bodyTextarea.id;
            const editor = tinymce.get(editorId);
            if (editor) {
                editor.focus();
            }
            return;
        }

        const endpoint = isThread
            ? `/forums/category/${form.dataset.category}/thread`
            : `/forums/thread/${form.dataset.thread}/reply`;

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Posting...';

        try {
            const formData = new FormData(form);
            
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': csrf, 
                    'Accept':'application/json' 
                },
                body: formData
            });
            
            const json = await res.json();
            
            if (!res.ok || !json.success) {
                showNotification(json.message || 'Validation failed', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                return;
            }
            
            showNotification(json.message || 'Posted!', 'success');
            form.reset();
            
            if (window.tinymce) {
                tinymce.get('reply-body')?.setContent('');
                tinymce.get('new-thread-body')?.setContent('');
            }

            if (isThread && window.reset_thread_upload) {
                window.reset_thread_upload();
            } else if (!isThread && window.reset_reply_upload) {
                window.reset_reply_upload();
            }
            
            if (isThread) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('newThreadModal'));
                if (modal) modal.hide();
            }
            
            uploadHandlersInitialized = false;
            
            if (isThread) {
                await loadCategory(json.category_id);
            } else {
                await loadThread(json.thread_id);
            }
            
        } catch(err) {
            console.error('Form submission error:', err);
            showNotification('Request failed: ' + err.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });

    // Handle sort dropdown change
    document.addEventListener('change', function(e) {
        if (e.target.id === 'sortThreads') {
            const categoryId = document.getElementById('forum-category')?.dataset.category;
            const sortValue = e.target.value;
            if (categoryId) {
                loadCategory(categoryId, sortValue);
            }
        }
    });

    // === Search Functionality (Add at the end) ===
    let searchTimeout;

    // Handle search input with debounce
    document.addEventListener('input', function(e) {
        if (e.target.id === 'searchThreads') {
            console.log('Search input detected:', e.target.value);
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performCategorySearch();
            }, 500);
        }
    });

    // Handle Enter key in search
    document.addEventListener('keypress', function(e) {
        if (e.target.id === 'searchThreads' && e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            performCategorySearch();
        }
    });

    // Handle search button click
    document.addEventListener('click', function(e) {
        if (e.target.id === 'searchBtn' || e.target.closest('#searchBtn')) {
            e.preventDefault();
            performCategorySearch();
        }

        // Clear search buttons
        if (e.target.id === 'clearSearchBtn' || e.target.closest('#clearSearchBtn') ||
            e.target.id === 'clearSearchBtn2' || e.target.closest('#clearSearchBtn2')) {
            e.preventDefault();
            const searchInput = document.getElementById('searchThreads');
            if (searchInput) {
                searchInput.value = '';
                performCategorySearch();
            }
        }
    });

    function performCategorySearch() {
        const categoryEl = document.getElementById('forum-category');
        if (!categoryEl) return;

        const categoryId = categoryEl.dataset.category;
        const sortDropdown = document.getElementById('sortThreads');
        const searchInput = document.getElementById('searchThreads');

        const sort = sortDropdown ? sortDropdown.value : 'newest';
        const search = searchInput ? searchInput.value.trim() : '';

        console.log('Searching via AJAX:', { categoryId, sort, search });

        let url = `/forums/category/${categoryId}?sort=${sort}`;
        if (search) {
            url += `&search=${encodeURIComponent(search)}`;
        }

        // Update clear button visibility
        const clearBtn = document.getElementById('clearSearchBtn');
        if (clearBtn) {
            clearBtn.style.display = search ? 'block' : 'none';
        }

        // Use AJAX instead of full page reload
        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newCategoryContent = doc.getElementById('forum-category');
                
                if (newCategoryContent && categoryEl) {
                    // Replace only the category content
                    categoryEl.outerHTML = newCategoryContent.outerHTML;
                }
                
                // Update browser URL without reload
                window.history.pushState({}, '', url);
            })
            .catch(error => console.error('Search error:', error));
    }

    console.log('✅ Search functionality initialized');

    document.addEventListener('click', function(e) {
        const categoryLink = e.target.closest('.category-link');
        if (categoryLink) {
            e.preventDefault();
            const categoryId = categoryLink.getAttribute('data-category-id') || 
                              categoryLink.getAttribute('href').split('/').pop().split('?')[0];
            loadCategory(categoryId);
        }

        // Navigate to thread (ADD THIS UPDATED VERSION)
        const threadCard = e.target.closest('.thread-card');
        if (threadCard && !e.target.closest('button') && !e.target.closest('a')) {
            e.preventDefault();
            const threadId = threadCard.getAttribute('data-thread');
            if (threadId) {
                loadThread(threadId);
            }
        }

        // Back to categories
        if (e.target.id === 'backToCategories' || e.target.closest('#backToCategories')) {
            e.preventDefault();
            loadCategories();
        }

        // === Pagination Handler ===
        const paginationLink = e.target.closest('.page-link');
        if (paginationLink && paginationLink.getAttribute('href')) {
            e.preventDefault();
            const url = paginationLink.getAttribute('href');
            
            // Extract category ID from current page
            const categoryEl = document.getElementById('forum-category');
            if (categoryEl) {
                console.log('📄 Pagination clicked:', url);
                
                // Fetch the new page via AJAX
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newCategoryContent = doc.getElementById('forum-category');
                        
                        if (newCategoryContent) {
                            const currentCategoryEl = document.getElementById('forum-category');
                            if (currentCategoryEl) {
                                // Replace category content
                                currentCategoryEl.outerHTML = newCategoryContent.outerHTML;
                                
                                // Scroll to top of content
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            }
                        }
                        
                        // Update browser URL
                        window.history.pushState({}, '', url);
                    })
                    .catch(error => console.error('Pagination error:', error));
            }
            return;
        }
    });
});
</script>
