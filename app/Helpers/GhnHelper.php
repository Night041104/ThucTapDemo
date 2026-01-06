<?php
class GhnHelper {
    // Thông tin cấu hình
    private $token = '9e958cee-c146-11f0-a621-f2a9392e54c8'; 
    private $shopId = '198125'; 
    
    // URL API
    private $urlCreateOrder = 'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/create';
    private $urlGetService  = 'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services';

    /**
     * Hàm chính: Tạo đơn hàng
     */
    public function createShippingOrder($orderData, $items) {
        
        // 1. Chuẩn bị Items
        $ghnItems = [];
        $totalWeight = 0;
        foreach ($items as $item) {
            $ghnItems[] = [
                "name"     => $item['product_name'],
                "code"     => (string)$item['product_id'],
                "quantity" => (int)$item['quantity'],
                "price"    => (int)$item['price'],
                "length"   => 10, 
                "width"    => 10,
                "height"   => 10,
                "weight"   => 200 
            ];
            $totalWeight += 200 * $item['quantity'];
        }

        // 2. Lấy Service ID phù hợp (Tự động tính toán dựa trên tuyến đường)
        // ShopID sẽ tự định nghĩa nơi đi, $to_district_id là nơi đến
        $toDistrictId = (int)$orderData['district_id'];
        $serviceId = $this->getServiceId($toDistrictId);

        // Nếu không lấy được service (do lỗi mạng hoặc sai tuyến), fallback về gói chuẩn (thường là 2)
        // Nhưng tốt nhất là nên handle lỗi ở đây.
        if (!$serviceId) {
             // Fallback hoặc return lỗi tùy bạn
             $serviceId = 53320; // Giá trị mặc định cũ của bạn (chỉ dùng chống cháy)
        }

        // 3. Chuẩn bị dữ liệu Body
        $data = [
            "payment_type_id" => 1, // 1: Người bán trả ship, 2: Người mua trả
            "note" => $orderData['note'] ?? "Cho xem hàng",
            "required_note" => "CHOXEMHANGKHONGTHU",
            
            // --- [QUAN TRỌNG] ĐÃ BỎ CÁC TRƯỜNG from_name, from_address... ---
            // GHN sẽ tự lấy địa chỉ kho dựa trên Header ShopId
            
            "to_name" => $orderData['fullname'],
            "to_phone" => $orderData['phone'],
            "to_address" => $orderData['address'],
            "to_ward_code" => (string)$orderData['ward_code'],
            "to_district_id" => (int)$orderData['district_id'],
            
            "cod_amount" => ($orderData['payment_method'] == 'COD') ? (int)$orderData['total_money'] : 0,
            "content" => "Đơn hàng #" . $orderData['order_code'],
            "weight" => $totalWeight,
            "length" => 20,
            "width" => 20,
            "height" => 20,
            
            // Sử dụng Service ID động vừa lấy được
            "service_id" => $serviceId, 
            "service_type_id" => 2, // 2 là E-commerce Delivery
            
            "items" => $ghnItems
        ];

        // 4. Gửi Request tạo đơn
        return $this->sendRequest($this->urlCreateOrder, $data);
    }

    /**
     * Hàm phụ: Lấy Service ID khả dụng cho tuyến đường
     * (Để tránh lỗi khi hardcode ID 53320)
     */
    private function getServiceId($toDistrict) {
        $data = [
            "shop_id" => (int)$this->shopId,
            "from_district" => 1452, // ID Quận kho hàng của bạn (Q4). 
            // Lưu ý: Nếu muốn động hoàn toàn thì nên lưu ID Quận kho trong config code luôn.
            "to_district" => $toDistrict
        ];

        $response = $this->sendRequest($this->urlGetService, $data);
        
        if (isset($response['code']) && $response['code'] == 200 && !empty($response['data'])) {
            // Lấy service đầu tiên (thường là gói chuẩn hoặc tiết kiệm phù hợp nhất)
            return $response['data'][0]['service_id'];
        }
        return null;
    }

    /**
     * Hàm phụ: Gửi cURL chung
     */
    private function sendRequest($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'ShopId: ' . $this->shopId,
            'Token: ' . $this->token
        ]);

        $result = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($result, true);
    }
}
?>