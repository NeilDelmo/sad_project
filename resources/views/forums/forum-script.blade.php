<!-- forum-script.blade.php -->
<script src="https://cdn.tiny.cloud/1/eeqmij25flkdyumonik6xoofb4fu0bb4sfg76ahpf6mogxet/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const forumContent = document.getElementById('forum-content');
    
    console.log('Forum script loaded. forumContent:', forumContent);
    console.log('Category links found:', document.querySelectorAll('.category-link').length);

    // 🧠 Utility: initialize TinyMCE if textarea exists
    function initTinyMCE() {
        if (typeof tinymce === 'undefined') return;

        tinymce.remove(); // clear previous instances

        // Thread reply editor
        if (document.querySelector('#reply-body')) {
            tinymce.init({
                selector: '#reply-body',
                plugins: 'image emoticons link lists code',
                toolbar: 'undo redo | bold italic underline | bullist numlist | emoticons | image link | code',
                menubar: false,
                skin: 'oxide-dark',
                content_css: 'dark',
                height: 300,
                branding: false,
                automatic_uploads: true,
                images_upload_url: '/forums/upload',
                file_picker_types: 'image',
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                images_upload_handler: uploadHandler
            });
        }

        // New thread editor
        if (document.querySelector('#new-thread-body')) {
            tinymce.init({
                selector: '#new-thread-body',
                plugins: 'image emoticons link lists code',
                toolbar: 'undo redo | bold italic underline | bullist numlist | emoticons | image link | code',
                menubar: false,
                skin: 'oxide-dark',
                content_css: 'dark',
                height: 300,
                branding: false,
                automatic_uploads: true,
                images_upload_url: '/forums/upload',
                file_picker_types: 'image',
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                images_upload_handler: uploadHandler
            });
        }
    }

// 🧩 Image upload handler shared by both editors
function uploadHandler(blobInfo, success, failure) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/forums/upload');
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector("meta[name='csrf-token']").content);
    xhr.setRequestHeader('Accept', 'application/json');
    
    xhr.onload = function() {
        if (xhr.status !== 200) {
            failure('HTTP Error: ' + xhr.status);
            return;
        }
        
        try {
            const json = JSON.parse(xhr.responseText);
            if (!json || typeof json.location != 'string') {
                failure('Invalid response format');
                return;
            }
            success(json.location);
        } catch (e) {
            failure('Invalid JSON response: ' + xhr.responseText.substring(0, 100));
        }
    };
    
    xhr.onerror = function() {
        failure('Upload failed');
    };
    
    const formData = new FormData();
    formData.append('file', blobInfo.blob(), blobInfo.filename());
    xhr.send(formData);
}

    // 🗂️ Load category from initial page (category links on index)
    document.querySelectorAll('.category-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Category link clicked, preventing default');
            const categoryId = link.dataset.categoryId;
            console.log('Loading category ID:', categoryId);
            
            if (!categoryId) {
                console.error('No category ID found');
                return;
            }
            
            fetch(`/forums/category/${categoryId}`)
                .then(res => {
                    console.log('Fetch response:', res);
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.text();
                })
                .then(html => {
                    console.log('HTML received, length:', html.length);
                    forumContent.innerHTML = html;
                    initTinyMCE(); // ✅ Initialize TinyMCE for new-thread form
                })
                .catch(err => {
                    console.error('Category load error:', err);
                    showNotification('Failed to load category: ' + err.message, 'error');
                });
        });
    });

    // 🧭 Load a category (from forum-card elements)
    document.querySelectorAll('.forum-card').forEach(card => {
        card.addEventListener('click', () => {
            const categoryId = card.dataset.category;
            fetch(`/forums/category/${categoryId}`)
                .then(res => res.text())
                .then(html => {
                    forumContent.innerHTML = html;
                    initTinyMCE(); // ✅ Initialize TinyMCE for new-thread form
                })
                .catch(err => {
                    console.error('Category load error:', err);
                    showNotification('Failed to load category', 'error');
                });
        });
    });

    // 🔙 Back navigation (categories and threads)
    forumContent.addEventListener('click', e => {
        if (e.target.id === 'backToCategories' || e.target.closest('#backToCategories')) {
            window.location.href = '/forums';
        }

        if (e.target.id === 'backToCategory' || e.target.closest('#backToCategory')) {
            const container = e.target.closest('.animate-fadeIn');
            const category_id = container ? container.dataset.category : null;

            if (category_id) {
                fetch(`/forums/category/${category_id}`)
                    .then(res => res.text())
                    .then(html => {
                        forumContent.innerHTML = html;
                        initTinyMCE(); // ✅ reinit for new-thread form
                    })
                    .catch(err => {
                        console.error('Back to category error:', err);
                        window.location.reload();
                    });
            } else {
                window.location.reload();
            }
        }
    });

    // 🧨 Load a thread
    forumContent.addEventListener('click', e => {
        const threadCard = e.target.closest('.thread-card');
        if (!threadCard) return;
        const threadId = threadCard.dataset.thread;
        if (!threadId) return;

        fetch(`/forums/thread/${threadId}`)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                return res.text();
            })
            .then(html => {
                forumContent.innerHTML = html;
                initTinyMCE(); // ✅ initialize reply editor
            })
            .catch(err => {
                console.error('Thread load error:', err);
                showNotification('Failed to load thread', 'error');
            });
    });

// 📨 Handle thread + reply form submissions
document.addEventListener('submit', e => {
    e.preventDefault();
    const form = e.target;

    // ✅ Collect TinyMCE data before sending
    if (typeof tinymce !== 'undefined') {
        tinymce.triggerSave();
    }

    // New thread form
    if (form.id === 'new-thread-form') {
        const formData = new FormData(form);
        const category_id = form.dataset.category;

        fetch(`/forums/category/${category_id}/thread`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid JSON response: ' + text.substring(0, 100));
            }
        })
        .then(data => {
            if (data.success) {
                form.reset();
                if (typeof tinymce !== 'undefined') {
                    tinymce.get('new-thread-body').setContent('');
                }
                showNotification(data.message || 'Thread posted successfully!', 'success');
                
                // Reload the category view
                fetch(`/forums/category/${category_id}`)
                    .then(r => r.text())
                    .then(html => {
                        forumContent.innerHTML = html;
                        initTinyMCE();
                    });
            } else {
                showNotification(data.errors ? Object.values(data.errors).flat().join(', ') : 'Failed to post thread.', 'error');
            }
        })
        .catch(err => {
            console.error('Thread submission error:', err);
            showNotification('Failed to post thread.', 'error');
        });
    }

    // Reply form
    if (form.id === 'reply-form') {
        const formData = new FormData(form);
        const thread_id = form.dataset.thread;

        fetch(`/forums/thread/${thread_id}/reply`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid JSON response: ' + text.substring(0, 100));
            }
        })
        .then(data => {
            if (data.success) {
                form.reset();
                if (typeof tinymce !== 'undefined') {
                    tinymce.get('reply-body').setContent('');
                }
                showNotification(data.message || 'Reply posted successfully!', 'success');
                
                // Reload the thread view
                fetch(`/forums/thread/${thread_id}`)
                    .then(r => r.text())
                    .then(html => {
                        forumContent.innerHTML = html;
                        initTinyMCE();
                    });
            } else {
                showNotification(data.errors ? Object.values(data.errors).flat().join(', ') : 'Failed to post reply.', 'error');
            }
        })
        .catch(err => {
            console.error('Reply submission error:', err);
            showNotification('Failed to post reply.', 'error');
        });
    }
});

    // Initialize editors for first load
    initTinyMCE();
});

// 🔔 Notification helper
function showNotification(message, type = 'info') {
    const n = document.createElement('div');
    n.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    n.textContent = message;
    document.body.appendChild(n);
    setTimeout(() => n.remove(), 3000);
}


// New thread form
if (form.id === 'new-thread-form') {
    const formData = new FormData(form);
    const category_id = form.dataset.category;

    console.log('Submitting thread to:', `/forums/category/${category_id}/thread`);
    
    fetch(`/forums/category/${category_id}/thread`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async res => {
        console.log('Response status:', res.status);
        console.log('Response URL:', res.url);
        console.log('Response headers:', Object.fromEntries(res.headers.entries()));
        
        const text = await res.text();
        console.log('Raw response:', text.substring(0, 200)); // First 200 chars
        
        try {
            return JSON.parse(text);
        } catch (e) {
            // If it's HTML and contains error messages, handle accordingly
            if (text.includes('error') || text.includes('Error') || res.status !== 200) {
                throw new Error('Server returned HTML error page instead of JSON');
            }
            throw new Error('Invalid JSON response: ' + text.substring(0, 100));
        }
    })
    .then(data => {
        // ... rest of your success handling
    })
    .catch(err => {
        console.error('Thread submission error:', err);
        showNotification('Failed to post thread. Please check console for details.', 'error');
    });
}
</script>
