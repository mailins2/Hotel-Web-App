<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả thanh toán - Peach Valley</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, sans-serif;
            display: flex; justify-content: center; align-items: center;
            min-height: 100vh; background: #f5f5f5;
        }
        .card {
            background: white; border-radius: 16px; padding: 40px 30px;
            text-align: center; max-width: 400px; width: 90%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .icon { font-size: 60px; margin-bottom: 16px; }
        .success { color: #4CAF50; }
        .failed { color: #E53935; }
        h2 { color: #49120F; margin-bottom: 8px; }
        p { color: #666; margin-bottom: 20px; font-size: 14px; }
        .btn {
            display: block; margin: 8px 0; padding: 14px;
            border-radius: 10px; text-decoration: none; font-weight: bold;
            font-size: 15px;
        }
        .btn-primary { background: #C97A3E; color: white; }
        .btn-outline { border: 2px solid #C97A3E; color: #C97A3E; }
        .loading { color: #999; font-size: 13px; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="card">
        @if($status == 'success')
            <div class="icon success">✅</div>
            <h2>Thanh toán thành công!</h2>
            <p>Mã giao dịch: {{ $txnRef }}</p>
        @else
            <div class="icon failed">❌</div>
            <h2>Thanh toán thất bại</h2>
            <p>Vui lòng thử lại hoặc liên hệ hỗ trợ.</p>
        @endif

        <a href="{{ $deepLink }}" class="btn btn-primary">
            📱 Mở App Peach Valley
        </a>
        <a href="{{ $redirectUrl }}" class="btn btn-outline">
            🌐 Xem trên Website
        </a>
        <div class="loading">Đang tự động mở app...</div>
    </div>

    <script>
        // Tự động mở app
        setTimeout(function() {
            window.location.href = "{{ $deepLink }}";
        }, 800);

        // Nếu không mở được app trong 3s → ở lại web
        setTimeout(function() {
            document.querySelector('.loading').textContent = 'Nếu app không mở, hãy nhấn nút bên trên.';
        }, 3000);
    </script>
</body>
</html>