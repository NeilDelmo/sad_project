<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Add Rental Product - Admin - SeaLedger</title>
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-family: "Koulen", sans-serif;
            font-size: 36px;
            color: #1B5E88;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #666;
            font-size: 16px;
        }

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #1B5E88;
            outline: none;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: #1B5E88;
            color: white;
        }

        .btn-primary:hover {
            background: #154a6b;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #e2e3e5;
            color: #383d41;
        }

        .btn-secondary:hover {
            background: #d6d8db;
            transform: translateY(-2px);
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    @include('admin.partials.nav')

    <div class="container">
        <div class="page-header">
            <div>
                <div class="page-title">Add Rental Product</div>
                <div class="page-subtitle">Create new equipment available for rent</div>
            </div>
            <a href="{{ route('rentals.admin.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>

        @if($errors->any())
            <div class="alert-error">
                <ul style="list-style: none;">
                    @foreach($errors->all() as $error)
                        <li><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-card">
            <form action="{{ route('rentals.admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="e.g., Professional Fishing Rod">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" required placeholder="Describe the equipment...">{{ old('description') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Rental Price (Per Day)</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 12px; color: #666;">₱</span>
                            <input type="number" name="rental_price_per_day" class="form-control" style="padding-left: 30px;" required min="0" step="0.01" value="{{ old('rental_price_per_day') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Initial Stock</label>
                        <input type="number" name="rental_stock" class="form-control" required min="0" value="{{ old('rental_stock') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                    <p style="font-size: 12px; color: #666; margin-top: 5px;">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB.</p>
                    
                    <div id="image-preview-container" style="margin-top: 10px; display: none;">
                        <p style="font-size: 14px; color: #666;">Image Preview:</p>
                        <img id="image-preview" src="#" alt="Image Preview" style="max-width: 200px; border-radius: 8px; border: 1px solid #ddd;">
                    </div>
                </div>

                <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const container = document.getElementById('image-preview-container');
            const preview = document.getElementById('image-preview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                container.style.display = 'none';
            }
        }
    </script>
</body>
</html>
