<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled</title>
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
            text-align: center;
        }
        .cancel-icon {
            width: 80px;
            height: 80px;
            background: #ffd93d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        .cancel-icon svg { width: 40px; height: 40px; color: white; }
        h1 { color: #333; margin-bottom: 10px; font-size: 28px; }
        .message { color: #666; margin-bottom: 30px; font-size: 16px; line-height: 1.6; }
        .btn-group { display: flex; gap: 15px; flex-wrap: wrap; justify-content: center; }
        .btn {
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            display: inline-block;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); }
    </style>
</head>
<body>
    <div class="container">
        <div class="cancel-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>

        <h1>Payment Cancelled</h1>
        <p class="message">Your payment was cancelled. No charges have been made to your account. You can try again when you're ready.</p>

        <div class="btn-group">
            <a href="{{ route('dodopayments.checkout') }}" class="btn btn-primary">Try Again</a>
            <a href="{{ url('/') }}" class="btn btn-secondary">Return to Home</a>
        </div>
    </div>
</body>
</html>
