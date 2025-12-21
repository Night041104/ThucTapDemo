<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công</title>
    <style>
        body { font-family: sans-serif; background: #f4f6f8; display: flex; justify-content: center; padding-top: 50px; }
        .success-box { background: white; padding: 40px; border-radius: 8px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 500px; }
        .icon { font-size: 60px; color: #28a745; margin-bottom: 20px; }
        .order-code { background: #e8f5e9; color: #1b5e20; padding: 10px 20px; border-radius: 4px; font-weight: bold; font-size: 20px; margin: 20px 0; display: inline-block; border: 1px dashed #28a745; }
        .btn-home { background: #007bff; color: white; text-decoration: none; padding: 10px 25px; border-radius: 4px; font-weight: bold; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="success-box">
        <div class="icon">✅</div>
        <h1>ĐẶT HÀNG THÀNH CÔNG!</h1>
        <p>Cảm ơn bạn đã mua sắm. Đơn hàng của bạn đang được xử lý.</p>
        
        <p>Mã đơn hàng của bạn là:</p>
        <div class="order-code"><?= htmlspecialchars($code) ?></div>
        
        <br>
        <a href="index.php" class="btn-home">Tiếp tục mua hàng</a>
    </div>

</body>
</html>