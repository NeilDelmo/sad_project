<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('bootstrap5/css/bootstrap.min.css') }}" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger - Edit Product</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

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

        .container-main {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
        }

        .page-title {
            font-family: "Koulen", sans-serif;
            font-size: 42px;
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .page-subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }

        .form-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            font-weight: bold;
            color: #1B5E88;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .form-label.required::after {
            content: " *";
            color: #dc3545;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #0075B5;
            box-shadow: 0 0 0 3px rgba(0,117,181,0.1);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }

        select.form-control {
            cursor: pointer;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .form-help {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 2px solid #eee;
        }

        .btn-submit {
            flex: 1;
            background: #0075B5;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background: #1B5E88;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,117,181,0.3);
        }

        .btn-cancel {
            background: white;
            color: #666;
            padding: 15px 30px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            background: #f8f9fa;
            border-color: #999;
            color: #333;
        }

        .input-group {
            display: flex;
            align-items: center;
        }

        .input-addon {
            background: #E7FAFE;
            border: 2px solid #ddd;
            border-right: none;
            padding: 12px 15px;
            border-radius: 8px 0 0 8px;
            font-weight: bold;
            color: #0075B5;
        }

        .input-group .form-control {
            border-radius: 0 8px 8px 0;
        }

        .freshness-options {
            display: flex;
            gap: 15px;
        }

        .freshness-option {
            flex: 1;
        }

        .freshness-option input[type="radio"] {
            display: none;
        }

        .freshness-label {
            display: block;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
            color: #666;
        }

        .freshness-option input[type="radio"]:checked + .freshness-label {
            border-color: #28a745;
            background: #d4edda;
            color: #28a745;
        }

        .freshness-label:hover {
            border-color: #0075B5;
            background: #E7FAFE;
        }

        .product-meta {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 14px;
            color: #666;
        }

        .product-meta i {
            color: #0075B5;
            margin-right: 8px;
        }
    </style>
</head>
<body>

    @include('fisherman.partials.nav')

    <div class="container-main">
        <h1 class="page-title">Edit Product</h1>
        <p class="page-subtitle">Update your product information</p>

        <div class="form-card">
            <!-- Product Meta Info -->
            <div class="product-meta">
                <div>
                    <i class="fa-solid fa-clock"></i>
                    <strong>Created:</strong> {{ $product->created_at->format('F d, Y') }} ({{ $product->created_at->diffForHumans() }})
                </div>
                @if($product->updated_at != $product->created_at)
                <div style="margin-top: 5px;">
                    <i class="fa-solid fa-pencil"></i>
                    <strong>Last Updated:</strong> {{ $product->updated_at->format('F d, Y') }} ({{ $product->updated_at->diffForHumans() }})
                </div>
                @endif
            </div>

            <form action="{{ route('fisherman.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Product Name -->
                <div class="form-group">
                    <label for="name" class="form-label required">Product Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           placeholder="e.g., Fresh Tilapia, Red Snapper"
                           value="{{ old('name', $product->name) }}"
                           required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label for="category_id" class="form-label required">Category</label>
                    <select id="category_id" 
                            name="category_id" 
                            class="form-control @error('category_id') is-invalid @enderror"
                            required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fish Type (for decay rate) -->
                <div class="form-group">
                    <label for="fish_type" class="form-label">Fish Type <small>(affects freshness duration)</small></label>
                    <select id="fish_type" 
                            name="fish_type" 
                            class="form-control @error('fish_type') is-invalid @enderror">
                        <option value="White Fish" {{ old('fish_type', $product->fish_type) == 'White Fish' ? 'selected' : '' }}>White Fish (normal)</option>
                        <option value="Oily Fish" {{ old('fish_type', $product->fish_type) == 'Oily Fish' ? 'selected' : '' }}>Oily Fish (spoils medium-fast)</option>
                        <option value="Shellfish" {{ old('fish_type', $product->fish_type) == 'Shellfish' ? 'selected' : '' }}>Shellfish (spoils faster)</option>
                    </select>
                    @error('fish_type')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-help">Select fish type for accurate freshness tracking</small>
                </div>

                <!-- Current Image -->
                @if($product->image_path)
                <div class="form-group">
                    <label class="form-label">Current Image</label>
                    <div style="margin-bottom: 15px;">
                        <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" style="max-width: 100%; max-height: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    </div>
                </div>
                @endif

                <!-- Product Image Upload -->
                <div class="form-group">
                    <label for="image" class="form-label">{{ $product->image_path ? 'Change Image' : 'Add Product Image' }}</label>
                    <input type="file" 
                           id="image" 
                           name="image" 
                           class="form-control @error('image') is-invalid @enderror" 
                           accept="image/jpeg,image/jpg,image/png,image/gif"
                           onchange="previewImage(this)">
                    @error('image')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-help">Upload a new image to replace the current one (JPEG, PNG, GIF - max 2MB)</small>
                    <div id="imagePreview" style="margin-top: 15px; display: none;">
                        <img id="preview" src="" alt="Image preview" style="max-width: 100%; max-height: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    </div>
                </div>

                <!-- Price -->
                <div class="form-group">
                    <label for="unit_price" class="form-label required">Price per Kilogram</label>
                    <div class="input-group">
                        <span class="input-addon">₱</span>
                        <input type="number" 
                               id="unit_price" 
                               name="unit_price" 
                               class="form-control @error('unit_price') is-invalid @enderror" 
                               placeholder="0.00"
                               step="0.01"
                               min="0"
                               value="{{ old('unit_price', $product->unit_price) }}"
                               required>
                    </div>
                    @error('unit_price')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-help">Enter the price per kilogram</small>
                </div>

                <!-- Available Quantity -->
                <div class="form-group">
                    <label for="available_quantity" class="form-label required">Available Quantity (kg)</label>
                    <input type="number" 
                           id="available_quantity" 
                           name="available_quantity" 
                           class="form-control @error('available_quantity') is-invalid @enderror" 
                           placeholder="0"
                           step="0.1"
                           min="0"
                           value="{{ old('available_quantity', $product->available_quantity) }}"
                           required>
                    @error('available_quantity')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Freshness Level -->
                <div class="form-group">
                    <label class="form-label required">Freshness Level</label>
                    <div class="freshness-options">
                        <div class="freshness-option">
                            <input type="radio" 
                                   id="freshness_very" 
                                   name="freshness_metric" 
                                   value="Very Fresh"
                                   {{ old('freshness_metric', $product->freshness_metric) == 'Very Fresh' ? 'checked' : '' }}
                                   required>
                            <label for="freshness_very" class="freshness-label">
                                🌟🌟🌟<br>Very Fresh
                            </label>
                        </div>
                        <div class="freshness-option">
                            <input type="radio" 
                                   id="freshness_fresh" 
                                   name="freshness_metric" 
                                   value="Fresh"
                                   {{ old('freshness_metric', $product->freshness_metric) == 'Fresh' ? 'checked' : '' }}
                                   required>
                            <label for="freshness_fresh" class="freshness-label">
                                🌟🌟<br>Fresh
                            </label>
                        </div>
                        <div class="freshness-option">
                            <input type="radio" 
                                   id="freshness_good" 
                                   name="freshness_metric" 
                                   value="Good"
                                   {{ old('freshness_metric', $product->freshness_metric) == 'Good' ? 'checked' : '' }}
                                   required>
                            <label for="freshness_good" class="freshness-label">
                                🌟<br>Good
                            </label>
                        </div>
                    </div>
                    @error('freshness_metric')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-control @error('description') is-invalid @enderror" 
                              placeholder="Add details about your catch (optional)">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-help">Tell buyers more about your product</small>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-check"></i> Update Product
                    </button>
                    <a href="{{ route('fisherman.products.index') }}" class="btn-cancel">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.style.display = 'none';
            }
        }
    </script>

    @include('partials.message-notification')
</body>
</html>
