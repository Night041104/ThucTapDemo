<style>
    /* Card chứa nội dung */
    .success-container {
        max-width: 600px;
        margin: 50px auto;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        text-align: center;
        border-top: 5px solid #28a745; /* Viền xanh báo thành công */
    }

    /* Hiệu ứng Checkmark động */
    .checkmark-wrapper {
        width: 80px; height: 80px;
        margin: 0 auto 20px;
        background: #e8f5e9;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        position: relative;
    }
    .checkmark-icon {
        font-size: 40px; color: #28a745;
        animation: scaleUp 0.5s ease-out forwards;
    }

    @keyframes scaleUp {
        0% { transform: scale(0); opacity: 0; }
        80% { transform: scale(1.2); }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Typography */
    h1.success-title {
        color: #28a745;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 10px;
        font-size: 24px;
    }
    
    p.success-desc { color: #555; margin-bottom: 25px; line-height: 1.6; }

    /* Box Mã đơn hàng */
    .order-info-box {
        background: #f8f9fa;
        border: 1px dashed #ced4da;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 30px;
    }
    .order-label { font-size: 13px; color: #777; text-transform: uppercase; letter-spacing: 1px; }
    .order-code { font-size: 22px; font-weight: 800; color: #333; letter-spacing: 1px; margin-top: 5px; }

    /* Buttons */
    .btn-group-custom { display: flex; gap: 15px; justify-content: center; margin-top: 20px; }
    
    .btn-home {
        padding: 12px 30px;
        border-radius: 6px;
        background: white;
        border: 1px solid #ddd;
        color: #333;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-home:hover { background: #f1f1f1; border-color: #ccc; }

    .btn-track {
        padding: 12px 30px;
        border-radius: 6px;
        background: #cd1818;
        color: white;
        font-weight: 600;
        border: none;
        transition: 0.3s;
        box-shadow: 0 4px 10px rgba(205, 24, 24, 0.2);
    }
    .btn-track:hover { background: #b01414; color: white; transform: translateY(-2px); }

    /* Support text */
    .support-text { margin-top: 30px; font-size: 13px; color: #999; }
    .support-text a { color: #007bff; text-decoration: none; }
</style>

<div class="container">
    <div class="success-container">
        
        <div class="checkmark-wrapper">
            <i class="fa fa-check checkmark-icon"></i>
        </div>

        <h1 class="success-title">Đặt hàng thành công!</h1>
        <p class="success-desc">
            Cảm ơn bạn đã tin tưởng và mua sắm tại FPT Shop.<br>
            Đơn hàng của bạn đang được hệ thống xử lý và sẽ sớm được giao đi.
        </p>

        <div class="order-info-box">
            <div class="order-label">Mã đơn hàng của bạn</div>
            <div class="order-code">#<?= htmlspecialchars($code) ?></div>
            <div class="small text-muted mt-2">
                (Vui lòng lưu lại mã này để tra cứu tình trạng đơn hàng)
            </div>
        </div>
        <div class="btn-group-custom">
            <a href="trang-chu" class="btn-home">
                <i class="fa fa-arrow-left me-2"></i> Tiếp tục mua sắm
            </a>
            <a href="lich-su-don" class="btn-track">
                Theo dõi đơn hàng <i class="fa fa-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="support-text">
            Mọi thắc mắc xin vui lòng liên hệ hotline: <strong>1800 6601</strong><br>
            hoặc email: <a href="mailto:cskh@fptshop.com.vn">cskh@fptshop.com.vn</a>
        </div>

    </div>
</div>