<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
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
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out;
        }
        @keyframes scaleIn { from { transform: scale(0); } to { transform: scale(1); } }
        .success-icon svg { width: 40px; height: 40px; color: white; }
        h1 { color: #333; margin-bottom: 10px; font-size: 28px; }
        .message { color: #666; margin-bottom: 30px; font-size: 16px; line-height: 1.6; }
        .details { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: left; }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e1e8ed;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #666; font-size: 14px; }
        .detail-value { color: #333; font-weight: 600; font-size: 14px; word-break: break-all; }
        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1>Payment Successful!</h1>
        <p class="message">Thank you for your purchase. Your payment has been processed successfully.</p>

        <div class="details">
            @if(isset($payment_id))
            <div class="detail-row">
                <span class="detail-label">Payment ID:</span>
                <span class="detail-value">{{ $payment_id }}</span>
            </div>
            @endif

            @if(isset($session_id))
            <div class="detail-row">
                <span class="detail-label">Session ID:</span>
                <span class="detail-value">{{ $session_id }}</span>
            </div>
            @endif

            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ now()->format('M d, Y h:i A') }}</span>
            </div>
        </div>

        <a href="{{ url('/') }}" class="btn">Return to Home</a>
    </div>
</body>
</html>
