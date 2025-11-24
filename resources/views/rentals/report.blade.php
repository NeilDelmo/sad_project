<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Report an Issue - SeaLedger</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .navbar { background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%); padding: 15px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-brand { color:#fff; font-size:28px; font-weight:bold; text-decoration:none; display: flex; align-items: center; gap: 10px; }
        .nav-logo { height: 40px; width: auto; }
        .nav-link { color: rgba(255,255,255,0.9); text-decoration:none; padding:10px 16px; border-radius:8px; transition: all .2s; }
        .nav-link:hover { color:#fff; background: rgba(255,255,255,0.15); }
        .container { max-width: 900px; margin: 24px auto; }
        .card { border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .card-header { background: #fff; border-bottom: 2px solid #f0f0f0; }
        .btn-primary { background: #1B5E88; border-color: #1B5E88; }
        .btn-primary:hover { background: #0075B5; border-color: #0075B5; }
        .required::after { content:' *'; color:#dc3545; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="nav-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="SeaLedger Logo" class="nav-logo">
                SeaLedger
            </a>
            <div class="d-flex align-items-center" style="gap:8px;">
                <a href="{{ route('rentals.myrentals') }}" class="nav-link"><i class="fa-solid fa-clipboard-list"></i> My Rentals</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header p-4">
                <h3 class="m-0"><i class="fa-solid fa-flag"></i> Report an Issue for Rental #{{ $rental->id }}</h3>
                <small class="text-muted">Describe what went wrong so an admin can help.</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('rentals.report.submit', $rental) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label required">Issue Type</label>
                            <select name="issue_type" class="form-select" required>
                                <option value="pre_existing">Pre-existing damage</option>
                                <option value="accidental">Accidental damage</option>
                                <option value="lost">Item lost</option>
                                <option value="other" selected>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Severity</label>
                            <select name="severity" class="form-select">
                                <option value="">Select…</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Title (optional)</label>
                            <input type="text" name="title" class="form-control" maxlength="120" placeholder="Short summary (e.g., Broken handle on net)">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label required">Description</label>
                            <textarea name="description" class="form-control" rows="5" maxlength="2000" required placeholder="Provide details about the issue, when you noticed it, and any context."></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Photos (up to 5)</label>
                            <input type="file" name="photos[]" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp" multiple>
                            <small class="text-muted">Max 5MB each. JPEG, PNG, or WebP.</small>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Submit Report</button>
                        <a href="{{ route('rentals.myrentals') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script data-collect-dnt="true" async src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
</body>
</html>
