<?php
// FILE: config/vnpay_config.php
date_default_timezone_set('Asia/Ho_Chi_Minh');

// File: app/config/vnpay_config.php
$vnp_TmnCode = "8EOPMK6Z"; 
$vnp_HashSecret = "Z85MR1ITKD09QS5CZSNI807LNNE0DJC3";
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
// Lưu ý: Sửa lại đường dẫn localhost nếu cần thiết
$vnp_Returnurl = "http://localhost/THUCTAPDEMO/index.php?controller=checkout&action=vnpay_return"; 
$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
?>