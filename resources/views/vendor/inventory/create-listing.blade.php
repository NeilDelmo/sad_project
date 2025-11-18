<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger - Create Listing</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
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

        .content-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
        }

        .card h2 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1B5E88;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-item label {
            font-size: 13px;
            color: #666;
            display: block;
            margin-bottom: 5px;
        }

        .info-item p {
            font-size: 16px;
            font-weight: 500;
            color: #333;
            margin: 0;
        }

        .pricing-card {
            background: linear-gradient(135deg, #e3f2fd 0%, #e8eaf6 100%);
            border: 2px solid #0075B5;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .pricing-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .pricing-header svg {
            width: 24px;
            height: 24px;
            margin-right: 10px;
            color: #0075B5;
        }

        .pricing-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #1B5E88;
            margin: 0;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .metric-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .metric-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .metric-value {
            font-size: 24px;
            font-weight: 700;
        }

        .breakdown-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .breakdown-row:last-child {
            border-bottom: none;
            padding-top: 15px;
        }

        .breakdown-label {
            color: #666;
        }

        .breakdown-value {
            font-weight: 600;
        }

        .profit-box {
            background: #f1f8f4;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .profit-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .profit-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

        .profit-fill {
            height: 100%;
            background: #16a34a;
            border-radius: 4px;
        }

        .btn-primary-custom {
            background: #0075B5;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            background: #1B5E88;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,117,181,0.3);
        }

        .btn-secondary-custom {
            background: white;
            color: #0075B5;
            padding: 12px 30px;
            border: 2px solid #0075B5;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-secondary-custom:hover {
            background: #E7FAFE;
            color: #0075B5;
            transform: translateY(-2px);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
    </style>
</head>
<body>
    @include('vendor.partials.nav')
    
    <div class="content-container">
        <!-- Header -->
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('vendor.inventory.show', $inventory) }}" style="color: #0075B5; text-decoration: none; display: inline-block; margin-bottom: 1rem;">
                ← Back to Inventory
            </a>
            <h1 style="font-size: 1.875rem; font-weight: 700; color: #1B5E88;">Create Marketplace Listing</h1>
            <p style="color: #666; margin-top: 0.5rem;">AI-powered dynamic pricing for optimal market performance</p>
        </div>

        <!-- Product Info Card -->
        <div class="card">
            <h2>Product Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Product</label>
                    <p>{{ $inventory->product->name }}</p>
                </div>
                <div class="info-item">
                    <label>Category</label>
                    <p>{{ $inventory->product->category->name }}</p>
                </div>
                <div class="info-item">
                    <label>Available Quantity</label>
                    <p>{{ $inventory->quantity }} kg</p>
                </div>
                <div class="info-item">
                    <label>Your Purchase Cost</label>
                    <p>₱{{ number_format($baseCost, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- ML Pricing Analysis Card -->
        <div class="pricing-card">
            <div class="pricing-header">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h2>AI Dynamic Pricing Analysis</h2>
            </div>

            <!-- Market Conditions -->
            <div class="metrics-grid">
                <div class="metric-box">
                    <p class="metric-label">Freshness Score</p>
                    <p class="metric-value" style="color: #16a34a;">{{ round($pricingResult['features']['freshness_score']) }}/100</p>
                </div>
                <div class="metric-box">
                    <p class="metric-label">Market Demand</p>
                    <p class="metric-value" style="color: #f97316;">{{ $pricingResult['features']['demand_factor'] }}x</p>
                </div>
                <div class="metric-box">
                    <p class="metric-label">ML Confidence</p>
                    <p class="metric-value" style="color: #0075B5;">{{ round($mlConfidence * 100, 1) }}%</p>
                </div>
            </div>

            <!-- Pricing Breakdown -->
            <div style="background: white; border-radius: 8px; padding: 20px;">
                <h3 style="font-weight: 600; color: #333; margin-bottom: 20px;">Pricing Breakdown</h3>
                
                <div class="breakdown-row">
                    <span class="breakdown-label">Your Purchase Cost</span>
                    <span class="breakdown-value">₱{{ number_format($baseCost, 2) }}</span>
                </div>
                
                <div class="breakdown-row">
                    <span class="breakdown-label">
                        AI Multiplier
                        <span style="margin-left: 8px; padding: 4px 8px; background: #e3f2fd; color: #0075B5; font-size: 11px; border-radius: 12px;">{{ $mlMultiplier }}x</span>
                    </span>
                    <span class="breakdown-value" style="color: #0075B5;">₱{{ number_format($dynamicPrice, 2) }}</span>
                </div>
                
                <div class="breakdown-row">
                    <span class="breakdown-label">
                        Platform Fee
                        <span style="margin-left: 8px; font-size: 11px; color: #999;">(10%)</span>
                    </span>
                    <span class="breakdown-value" style="color: #dc2626;">-₱{{ number_format($platformFee, 2) }}</span>
                </div>
                
                <div class="breakdown-row" style="border-top: 2px solid #e0e0e0;">
                    <span style="font-weight: 700; color: #333;">Your Profit</span>
                    <span style="font-size: 24px; font-weight: 700; color: #16a34a;">₱{{ number_format($vendorProfit, 2) }}</span>
                </div>

                <!-- Profit Margin Indicator -->
                <div class="profit-box">
                    <div class="profit-header">
                        <span style="color: #333;">Profit Margin:</span>
                        <span style="font-size: 18px; font-weight: 700; color: #16a34a;">
                            {{ round(($vendorProfit / $baseCost) * 100, 1) }}%
                        </span>
                    </div>
                    <div class="profit-bar">
                        <div class="profit-fill" style="width: {{ min(($vendorProfit / $baseCost) * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listing Form -->
        <form action="{{ route('vendor.inventory.store-listing', $inventory) }}" method="POST" class="card">
            @csrf
            
            <input type="hidden" name="quantity" value="{{ $inventory->quantity }}">
            
            <div style="margin-bottom: 25px;">
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #333;">Final Listing Price</h3>
                <div style="display: flex; align-items: baseline;">
                    <span style="font-size: 2.5rem; font-weight: 700; color: #16a34a;">₱{{ number_format($dynamicPrice, 2) }}</span>
                    <span style="margin-left: 10px; color: #666;">per kg</span>
                </div>
                <p style="font-size: 13px; color: #666; margin-top: 10px;">
                    This AI-optimized price maximizes your profit while remaining competitive in the market.
                </p>
            </div>

            @if($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom" style="flex: 1;">
                    Create Listing
                </button>
                <a href="{{ route('vendor.inventory.show', $inventory) }}" class="btn-secondary-custom" style="flex: 1; text-align: center;">
                    Cancel
                </a>
            </div>
        </form>

        <!-- Market Insights -->
        <div style="margin-top: 20px; background: #e3f2fd; border-left: 4px solid #0075B5; padding: 20px; border-radius: 8px;">
            <div style="display: flex;">
                <div style="flex-shrink: 0;">
                    <svg style="height: 20px; width: 20px; color: #0075B5;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div style="margin-left: 15px;">
                    <p style="font-size: 13px; color: #1B5E88;">
                        <strong>Market Insight:</strong> Our AI model analyzed current market conditions including freshness ({{ round($pricingResult['features']['freshness_score']) }}/100), demand ({{ $pricingResult['features']['demand_factor'] }}x), and seasonality to recommend this optimal price{{ $mlConfidence > 0 ? ' with ' . round($mlConfidence * 100, 1) . '% confidence' : '' }}.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@include('partials.message-notification')
</body>
</html>
