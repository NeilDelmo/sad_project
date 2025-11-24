<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Edit Rental Product - Admin - SeaLedger</title>
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

        .alert-warning {
            background: #fff7ed;
            color: #9a3412;
            border: 1px solid #fdba74;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .current-image {
            margin-top: 10px;
            max-width: 200px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    @include('admin.partials.nav')

    <div class="container">
        <div class="page-header">
            <div>
                <div class="page-title">Edit Rental Product</div>
                <div class="page-subtitle">Update equipment details</div>
            </div>
            <a href="{{ route('rentals.admin.products') }}" class="btn btn-secondary">
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

        @php
            $inventoryLocked = $inventoryLocked ?? false;
        @endphp

        @if($inventoryLocked)
            <div class="alert-warning">
                <i class="fa-solid fa-lock"></i>
                Stock and equipment status are locked while this product has pending/approved/active rentals. You can still update descriptive fields and pricing for future rentals.
            </div>
        @endif

        <div class="form-card">
            <form action="{{ route('rentals.admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $product->name) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" required>{{ old('description', $product->description) }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Rental Price (Per Day)</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 12px; color: #666;">₱</span>
                            <input type="number" name="rental_price_per_day" class="form-control" style="padding-left: 30px;" required min="0" step="0.01" value="{{ old('rental_price_per_day', $product->rental_price_per_day) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Stock</label>
                        <input type="number" name="rental_stock" class="form-control" required min="0" value="{{ old('rental_stock', $product->rental_stock) }}" {{ $inventoryLocked ? 'readonly' : '' }}>
                        <p style="font-size: 12px; color: #666; margin-top: 5px;">
                            @if($inventoryLocked)
                                Locked until current rentals are completed.
                            @else
                                Note: Changing this affects available inventory.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="equipment_status" class="form-control" {{ $inventoryLocked ? 'disabled' : '' }}>
                        <option value="available" {{ old('equipment_status', $product->equipment_status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="maintenance" {{ old('equipment_status', $product->equipment_status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="retired" {{ old('equipment_status', $product->equipment_status) == 'retired' ? 'selected' : '' }}>Retired</option>
                    </select>
                    @if($inventoryLocked)
                        <input type="hidden" name="equipment_status" value="{{ old('equipment_status', $product->equipment_status) }}">
                        <p style="font-size: 12px; color: #9a3412; margin-top: 5px;">Status locked while rentals are ongoing.</p>
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label">Product Image</label>
                    <div id="current-image-container" style="margin-bottom: 10px; {{ $product->image_path ? '' : 'display: none;' }}">
                        <p style="font-size: 14px; color: #666;">Current/Preview Image:</p>
                        <img id="image-preview" src="{{ $product->image_path ? asset('storage/' . $product->image_path) : '#' }}" alt="Product Image" class="current-image">
                    </div>
                    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                    <p style="font-size: 12px; color: #666; margin-top: 5px;">Leave empty to keep current image. Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB.</p>
                </div>

                <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const container = document.getElementById('current-image-container');
            const preview = document.getElementById('image-preview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
