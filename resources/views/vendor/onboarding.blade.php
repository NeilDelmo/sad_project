<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger - Vendor Preferences</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        
        .back-button {
            position: fixed;
            top: 30px;
            left: 30px;
            background: white;
            color: #1B5E88;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-button:hover {
            background: #1B5E88;
            color: white;
            transform: translateX(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        .container-custom {
            max-width: 750px;
            width: 100%;
            padding: 50px 60px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        
        .page-title {
            font-family: "Koulen", sans-serif;
            font-size: 42px;
            color: #1B5E88;
            margin-bottom: 8px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        
        .page-subtitle {
            color: #6c757d;
            margin-bottom: 45px;
            text-align: center;
            font-size: 15px;
            line-height: 1.6;
        }
        
        .form-section {
            margin-bottom: 40px;
        }
        
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 12px;
            font-size: 15px;
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-top: 12px;
        }
        
        .form-check {
            padding: 12px 16px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px solid transparent;
            transition: all 0.2s ease;
        }
        
        .form-check:hover {
            background: #e9ecef;
            border-color: #0075B5;
        }
        
        .form-check-input:checked ~ .form-check-label {
            color: #0075B5;
            font-weight: 500;
        }
        
        .form-check-input {
            cursor: pointer;
            margin-top: 0.15em;
        }
        
        .form-check-label {
            cursor: pointer;
            user-select: none;
        }
        
        .input-group-custom {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 12px;
        }
        
        .input-wrapper {
            display: flex;
            flex-direction: column;
        }
        
        .form-control, .form-select {
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s ease;
            margin-top: 8px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #0075B5;
            box-shadow: 0 0 0 3px rgba(0, 117, 181, 0.1);
            outline: none;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #0075B5 0%, #1B5E88 100%);
            color: white;
            padding: 14px 40px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 117, 181, 0.3);
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 117, 181, 0.4);
        }
        
        .btn-primary-custom:active {
            transform: translateY(0);
        }
        
        .alert {
            border-radius: 8px;
            margin-bottom: 30px;
            border: none;
        }
        
        .form-actions {
            display: flex;
            justify-content: center;
            margin-top: 45px;
        }
        
        @media (max-width: 768px) {
            .container-custom {
                padding: 35px 30px;
            }
            
            .categories-grid {
                grid-template-columns: 1fr;
            }
            
            .input-group-custom {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .page-title {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <a href="{{ route('vendor.dashboard') }}" class="back-button">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
    
    <div class="container-custom">
        <div class="page-title">Vendor Preferences</div>
        <p class="page-subtitle">Set your preferences so we can notify you when fishermen list matching catches.</p>
        
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form method="POST" action="{{ route('vendor.onboarding.store') }}">
            @csrf
            <!-- Preferred Categories Section -->
            <div class="form-section">
                <label class="form-label">Preferred Categories</label>
                <div class="categories-grid">
                    @foreach($categories as $cat)
                    <div class="form-check">
                        <input type="checkbox" name="preferred_categories[]" value="{{ $cat->id }}" class="form-check-input" id="cat{{ $cat->id }}"
                            @if(!empty($prefs?->preferred_categories) && in_array($cat->id, $prefs->preferred_categories)) checked @endif>
                        <label class="form-check-label" for="cat{{ $cat->id }}">{{ $cat->name }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Quantity and Price Section -->
            <div class="form-section">
                <div class="input-group-custom">
                    <div class="input-wrapper">
                        <label class="form-label">Minimum Quantity (kg)</label>
                        <input type="number" name="min_quantity" value="{{ old('min_quantity', $prefs->min_quantity ?? '') }}" class="form-control" placeholder="e.g. 50">
                    </div>
                    <div class="input-wrapper">
                        <label class="form-label">Max Unit Price</label>
                        <input type="number" step="0.01" name="max_unit_price" value="{{ old('max_unit_price', $prefs->max_unit_price ?? '') }}" class="form-control" placeholder="e.g. 150.00">
                    </div>
                </div>
            </div>
            
            <!-- Notification Preferences Section -->
            <div class="form-section">
                <label class="form-label">Notify Me</label>
                <select name="notify_on" class="form-select">
                    <option value="matching" @selected(($prefs->notify_on ?? 'matching')==='matching')>Only listings matching my preferences</option>
                    <option value="all" @selected(($prefs->notify_on ?? '')==='all')>All new listings</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Save Preferences</button>
            </div>
        </form>
    </div>
@include('partials.message-notification')
@include('partials.toast-notifications')
</body>
</html>