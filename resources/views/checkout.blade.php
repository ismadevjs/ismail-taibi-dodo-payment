<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - DodoPayments</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }
        h1 { color: #333; margin-bottom: 10px; font-size: 28px; }
        .subtitle { color: #666; margin-bottom: 30px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 500; font-size: 14px; }
        input, select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus, select:focus { outline: none; border-color: #667eea; }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); }
        .btn:active { transform: translateY(0); }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-error { background: #fee; color: #c33; border: 1px solid #fcc; }
        .product-info { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .product-name { font-size: 18px; font-weight: 600; color: #333; margin-bottom: 8px; }
        .product-price { font-size: 24px; font-weight: 700; color: #667eea; }
        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
        .secure-badge svg { width: 16px; height: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Secure Checkout</h1>
        <p class="subtitle">Complete your purchase with DodoPayments</p>

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="product-info">
            <div class="product-name">Premium Product</div>
            <div class="product-price">$49.99</div>
        </div>

        <form action="{{ route('dodopayments.create-checkout') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ env('DODO_PRODUCT_ID', 'prod_YOUR_PRODUCT_ID') }}">

            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" required>
            </div>

            <div class="form-group">
                <label for="customer_name">Full Name</label>
                <input type="text" id="customer_name" name="customer_name" placeholder="John Doe" required>
            </div>

            <div class="form-group">
                <label for="customer_email">Email Address</label>
                <input type="email" id="customer_email" name="customer_email" placeholder="[email protected]" required>
            </div>

            <button type="submit" class="btn">Proceed to Payment</button>

            <div class="secure-badge">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                Secured by DodoPayments
            </div>
        </form>
    </div>
</body>
</html>
