<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap5/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>FishOrg Market</title>
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: #1B5E88;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .nav-tabs {
            display: flex;
            gap: 30px;
        }

        .nav-tab {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .nav-tab.active {
            background: #0075B5;
        }

        .nav-tab:hover {
            background: #0075B5;
        }

        .products-grid {
            display: flex;
            gap: 20px;
            padding: 20px;
            overflow-x: auto;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            min-width: 250px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .product-image {
            width: 100%;
            height: 160px;
            background: #E7FAFE;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .product-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #1B5E88;
        }

        .product-price {
            font-size: 18px;
            font-weight: bold;
            color: #B12704;
            margin-bottom: 8px;
        }

        .product-info {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .contact-info {
            font-size: 13px;
            color: #0075B5;
            background: #E7FAFE;
            padding: 6px 10px;
            border-radius: 4px;
            margin-bottom: 8px;
            cursor: pointer;
            border: 1px solid #0075B5;
            transition: all 0.3s;
        }

        .contact-info:hover {
            background: #0075B5;
            color: white;
        }

        .contact-info.copied {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }

        .section-title {
            font-size: 22px;
            color: #1B5E88;
            margin: 20px 0 10px 20px;
            font-weight: bold;
            border-bottom: 2px solid #1B5E88;
            padding-bottom: 5px;
        }

        .gear-contact {
            text-align: center;
            color: #666;
            font-style: italic;
            margin: 10px 20px;
            background: #E7FAFE;
            padding: 10px;
            border-radius: 6px;
        }

        .contact-btn {
            width: 100%;
            padding: 8px 12px;
            background: #1B5E88;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            margin-top: 8px;
            transition: background 0.2s;
        }

        .contact-btn:hover {
            background: #0075B5;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>

    <!-- Simple Navbar -->
    <nav class="navbar">
        <div class="nav-brand">FishOrg</div>
        <div class="nav-tabs">
            <a href="#" class="nav-tab active">Marketplace</a>
            <a href="#" class="nav-tab">Organization</a>
        </div>
    </nav>

    <!-- Fresh Fish Section -->
    <div class="section-title">Fresh Fish</div>
    <div class="products-grid">
        <div class="product-card">
            <div class="product-image">
                <i class="fa-solid fa-fish fa-2x" style="color: #0075B5;"></i>
            </div>
            <div class="product-title">Fresh Tuna</div>
            <div class="product-price">₱450/kg</div>
            <div class="product-info">Caught this morning</div>
            <div class="contact-info" onclick="copyContact(this)" data-contact="0917-123-4567">
                📞 0917-123-4567 (Click to copy)
            </div>
            @auth
                <button class="contact-btn" onclick="window.location.href='{{ route('marketplace.message') }}'">Message Seller</button>
            @else
                <button class="contact-btn" onclick="showLoginPrompt()">Message Seller</button>
            @endauth
        </div>

        <div class="product-card">
            <div class="product-image">
                <i class="fa-solid fa-fish fa-2x" style="color: #0075B5;"></i>
            </div>
            <div class="product-title">Bangus</div>
            <div class="product-price">₱220/kg</div>
            <div class="product-info">Medium size, fresh</div>
            <div class="contact-info" onclick="copyContact(this)" data-contact="0922-987-6543">
                📞 0922-987-6543 (Click to copy)
            </div>
            @auth
                <button class="contact-btn" onclick="window.location.href='{{ route('marketplace.message') }}'">Message Seller</button>
            @else
                <button class="contact-btn" onclick="showLoginPrompt()">Message Seller</button>
            @endauth
        </div>

        <div class="product-card">
            <div class="product-image">
                <i class="fa-solid fa-fish fa-2x" style="color: #0075B5;"></i>
            </div>
            <div class="product-title">Tilapia</div>
            <div class="product-price">₱150/kg</div>
            <div class="product-info">From local ponds</div>
            <div class="contact-info" onclick="copyContact(this)" data-contact="0918-555-7890">
                📞 0918-555-7890 (Click to copy)
            </div>
            @auth
                <button class="contact-btn" onclick="window.location.href='{{ route('marketplace.message') }}'">Message Seller</button>
            @else
                <button class="contact-btn" onclick="showLoginPrompt()">Message Seller</button>
            @endauth
        </div>
    </div>

    <!-- Fishing Gear Section -->
    <div class="section-title">Fishing Gear & Equipment</div>
    <div class="gear-contact">
        Contact Equipment Manager: 📞 0916-777-8888 (Click to copy)
    </div>
    <div class="products-grid">
        <div class="product-card">
            <div class="product-image">
                <i class="fa-solid fa-rod-async fa-2x" style="color: #0075B5;"></i>
            </div>
            <div class="product-title">Fishing Rods</div>
            <div class="product-price">Organization</div>
            <div class="product-info">Various sizes available</div>
        </div>

        <div class="product-card">
            <div class="product-image">
                <i class="fa-solid fa-network-wired fa-2x" style="color: #0075B5;"></i>
            </div>
            <div class="product-title">Fishing Nets</div>
            <div class="product-price">Organization</div>
            <div class="product-info">Different mesh sizes</div>
        </div>

        <div class="product-card">
            <div class="product-image">
                <i class="fa-solid fa-toolbox fa-2x" style="color: #0075B5;"></i>
            </div>
            <div class="product-title">Repair Tools</div>
            <div class="product-price">Organization</div>
            <div class="product-info">Net repair & maintenance</div>
        </div>

        <div class="product-card">
            <div class="product-image">
                <i class="fa-solid fa-vest fa-2x" style="color: #0075B5;"></i>
            </div>
            <div class="product-title">Safety Equipment</div>
            <div class="product-price">Organization</div>
            <div class="product-info">Vests, first aid kits</div>
        </div>
    </div>

    <!-- Login Prompt Modal -->
    <div id="loginModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 12px; max-width: 400px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
            <h2 style="color: #1B5E88; margin-bottom: 15px; font-size: 24px;">Login Required</h2>
            <p style="color: #666; margin-bottom: 25px;">Please login or create an account to message sellers.</p>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button onclick="window.location.href='{{ route('login') }}'" style="background: #0075B5; color: white; padding: 12px 30px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 16px;">
                    Login
                </button>
                <button onclick="window.location.href='{{ route('register') }}'" style="background: #1B5E88; color: white; padding: 12px 30px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 16px;">
                    Sign Up
                </button>
            </div>
            <button onclick="closeLoginPrompt()" style="background: transparent; color: #999; padding: 10px; border: none; cursor: pointer; margin-top: 15px; text-decoration: underline;">
                Cancel
            </button>
        </div>
    </div>

    <script>
        function copyContact(element) {
            const contact = element.textContent.match(/\d{4}-\d{3}-\d{4}/)[0];
            navigator.clipboard.writeText(contact).then(() => {
                const originalText = element.innerHTML;
                element.innerHTML = '✅ Copied!';
                element.classList.add('copied');

                setTimeout(() => {
                    element.innerHTML = originalText;
                    element.classList.remove('copied');
                }, 1500);
            });
        }

        // Also make gear contact clickable
        document.querySelector('.gear-contact').addEventListener('click', function () {
            const contact = this.textContent.match(/\d{4}-\d{3}-\d{4}/)[0];
            navigator.clipboard.writeText(contact).then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '✅ Contact copied to clipboard!';
                this.style.background = '#d4edda';

                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.background = '#E7FAFE';
                }, 1500);
            });
        });

        // Login prompt functions
        function showLoginPrompt() {
            document.getElementById('loginModal').style.display = 'flex';
        }

        function closeLoginPrompt() {
            document.getElementById('loginModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginPrompt();
            }
        });
    </script>

</body>

</html>