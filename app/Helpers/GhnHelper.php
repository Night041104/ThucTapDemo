<?php
class GhnHelper {
    // Thông tin lấy từ trang https://khachhang.ghn.vn/
    private $token = '9e958cee-c146-11f0-a621-f2a9392e54c8'; 
    private $shopId = '198125'; // Int
    private $apiUrl = 'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/create'; // Dùng link PROD khi chạy thật

    public function createShippingOrder($orderData, $items) {
        // 1. Chuẩn bị danh sách sản phẩm theo format GHN
        $ghnItems = [];
        $totalWeight = 0;
        foreach ($items as $item) {
            $ghnItems[] = [
                "name"     => $item['product_name'],
                "code"     => (string)$item['product_id'],
                "quantity" => (int)$item['quantity'],
                "price"    => (int)$item['price'],
                "length"   => 10, // Giả sử kích thước, nên lấy từ DB products
                "width"    => 10,
                "height"   => 10,
                "weight"   => 200 // Gram
            ];
            $totalWeight += 200 * $item['quantity'];
        }

        // 2. Chuẩn bị dữ liệu Body
        $data = [
            "payment_type_id" => 1, // 1: Người bán trả phí, 2: Người mua trả
            "note" => $orderData['note'] ?? "Cho xem hàng",
            "required_note" => "CHOXEMHANGKHONGTHU",
            "from_name" => "FPT Shop Clone",
            "from_phone" => "0909999999",
            "from_address" => "261 Khánh Hội, P2, Q4, HCM",
            "from_ward_code" => "20704", // Mã phường kho hàng (Ví dụ P2 Q4)
            "from_district_id" => 1452, // ID Quận kho hàng (Ví dụ Q4)
            
            "to_name" => $orderData['fullname'],
            "to_phone" => $orderData['phone'],
            "to_address" => $orderData['address'],
            "to_ward_code" => (string)$orderData['ward_code'], // Bắt buộc
            "to_district_id" => (int)$orderData['district_id'], // Bắt buộc
            
            "cod_amount" => ($orderData['payment_method'] == 'COD') ? (int)$orderData['total_money'] : 0,
            "content" => "Đơn hàng #" . $orderData['order_code'],
            "weight" => $totalWeight,
            "length" => 20,
            "width" => 20,
            "height" => 20,
            "service_id" => 53320, // Gói chuẩn (Có thể dùng API get service để lấy động)
            "service_type_id" => 2,
            "items" => $ghnItems
        ];

        // 3. Gửi cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'ShopId: ' . $this->shopId,
            'Token: ' . $this->token
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}