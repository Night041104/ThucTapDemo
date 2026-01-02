<style>
    /* CSS RIÊNG CHO FOOTER */
    .site-footer {
        background-color: #fff;
        padding-top: 50px;
        padding-bottom: 20px;
        font-family: 'Roboto', sans-serif;
        border-top: 3px solid #cd1818; /* Viền đỏ thương hiệu trên cùng */
        margin-top: 50px;
    }

    .footer-heading {
        font-size: 15px;
        font-weight: 700;
        text-transform: uppercase;
        color: #333;
        margin-bottom: 20px;
        position: relative;
    }

    /* Link liên kết */
    .footer-list {
        padding: 0;
        list-style: none;
    }
    
    .footer-list li {
        margin-bottom: 10px;
    }

    .footer-link {
        color: #555;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.3s ease;
        display: block;
    }

    .footer-link:hover {
        color: #cd1818; /* Màu đỏ khi hover */
        transform: translateX(5px); /* Hiệu ứng dịch sang phải */
    }

    /* Thông tin liên hệ */
    .contact-info p {
        font-size: 13px;
        color: #555;
        margin-bottom: 10px;
        line-height: 1.6;
    }
    
    /* Social Icons Styles */
    .social-btn {
        width: 38px;
        height: 38px;
        background-color: #f0f0f0;
        color: #555;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-btn:hover {
        background-color: #cd1818;
        color: #fff !important;
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(205, 24, 24, 0.3);
    }

    /* Payment Icons (Giả lập) */
    .payment-icon {
        font-size: 24px;
        color: #888;
        margin-right: 10px;
    }

    /* Copyright Bar */
    .copyright-bar {
        background-color: #f8f9fa;
        padding: 15px 0;
        margin-top: 30px;
        border-top: 1px solid #eee;
        font-size: 12px;
        color: #666;
    }
</style>

<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="footer-heading">Về chúng tôi</h5>
                <div class="contact-info">
                    <p>FPT Shop Clone System là dự án demo hệ thống bán lẻ kỹ thuật số, mong muốn mang lại trải nghiệm lập trình thực tế nhất.</p>
                    <p><i class="fa fa-map-marker-alt text-danger me-2" style="width:15px"></i> 261 Khánh Hội, P2, Q4, TP.HCM</p>
                    <p><i class="fa fa-phone text-danger me-2" style="width:15px"></i> 1800 6601 (Miễn phí)</p>
                    <p><i class="fa fa-envelope text-danger me-2" style="width:15px"></i> cskh@fptshop.com.vn</p>
                    <p><i class="fa-solid fa-comment-dots text-danger me-2" style="width:15px"></i> Góp ý, khiếu nại và tiếp nhận cảnh báo vi phạm 1800 6601 (8h00 - 22h00)</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="footer-heading">Hỗ trợ khách hàng</h5>
                <ul class="footer-list">
                    <li><a href="index.php?module=client&controller=about&action=guide" class="footer-link">Hướng dẫn mua hàng online</a></li>
                    <li><a href="index.php?module=client&controller=about&action=warranty" class="footer-link">Chính sách bảo hành & Đổi trả</a></li>
                    <li><a href="index.php?module=client&controller=about&action=payment" class="footer-link">Phương thức thanh toán</a></li>
                    <li><a href="index.php?controller=order&action=history" class="footer-link">Tra cứu đơn hàng</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-12 mb-4">
                <h5 class="footer-heading">Kết nối với chúng tôi</h5>
                <div class="d-flex gap-2 mb-4">
                    <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                </div>

                <h5 class="footer-heading mb-3">Chấp nhận thanh toán</h5>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <i class="fab fa-cc-visa payment-icon" title="Visa"></i>
                    <i class="fab fa-cc-mastercard payment-icon" title="Mastercard"></i>
                    <i class="fab fa-cc-jcb payment-icon" title="JCB"></i>
                    <i class="fa-solid fa-money-bill-wave payment-icon" title="Tiền mặt"></i>
                    <i class="fa-solid fa-qrcode payment-icon" title="QR Code"></i>
                </div>
                <div class="mt-3">
                   <img src="http://online.gov.vn/Content/EndUser/LogoCCDVSaleNoti/logoSaleNoti.png" alt="Đã thông báo bộ công thương" style="width: 120px; filter: grayscale(100%); opacity: 0.7;">
                </div>
            </div>
        </div>
    </div>

    <div class="copyright-bar">
        <div class="container text-center">
            &copy; 2025 FPT Shop Clone System. Design by YourName. All rights reserved.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Ví dụ: Hiệu ứng tooltip hoặc script nhỏ
    $(document).ready(function(){
        // Code JS global nếu cần
    });
</script>

</body>
</html>