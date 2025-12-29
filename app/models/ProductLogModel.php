<?php
require_once __DIR__ . '/BaseModel.php';

class ProductLogModel extends BaseModel {

    // Lấy danh sách ID thuộc tính biến thể (để phân biệt Chung/Riêng)
    private function getVariantAttributeIds() {
        $sql = "SELECT id FROM attributes WHERE is_variant = 1";
        $result = $this->_query($sql);
        $ids = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $ids[] = $row['id'];
            }
        }
        return $ids;
    }

    // Ghi lịch sử (Hàm chính được gọi từ Controller)
    public function logHistory($prodId, $oldData, $newData) {
        $adminId = $_SESSION['user']['id'] ?? 'unknown'; 
        // Lấy tên Admin từ session (hoặc query DB nếu cần thiết)
        $adminName = isset($_SESSION['user']) ? ($_SESSION['user']['lname'] . ' ' . $_SESSION['user']['fname']) : 'Unknown User';
        
        // Xác định Master ID (để gom nhóm lịch sử theo dòng sản phẩm)
        $masterId = ($oldData['parent_id'] > 0) ? $oldData['parent_id'] : $oldData['id'];

        // Lấy danh sách ID biến thể (Màu, ROM, RAM...)
        $variantIds = $this->getVariantAttributeIds();

        // 1. SO SÁNH CÁC TRƯỜNG CHUNG (SHARED) CƠ BẢN
        // (Loại bỏ 'specs_json' ở đây để xử lý riêng)
        $sharedFields = ['name', 'brand_id', 'status', 'slug', 'category_id', 'description']; 
        
        foreach ($sharedFields as $field) {
            if (isset($newData[$field]) && $newData[$field] != $oldData[$field]) {
                $this->insertLog($masterId, $prodId, $adminId, $adminName, 'SHARED', $field, $oldData[$field], $newData[$field]);
            }
        }

        // 2. SO SÁNH CÁC TRƯỜNG RIÊNG (VARIANT) CƠ BẢN
        $variantFields = ['price', 'market_price', 'quantity', 'thumbnail'];
        
        foreach ($variantFields as $field) {
            if (isset($newData[$field]) && $newData[$field] != $oldData[$field]) {
                $this->insertLog($masterId, $prodId, $adminId, $adminName, 'VARIANT', $field, $oldData[$field], $newData[$field]);
            }
        }

        // 3. [QUAN TRỌNG] SO SÁNH CHI TIẾT SPECS JSON
        // Tách nhỏ JSON ra để xem cụ thể thông số nào thay đổi
        if (isset($newData['specs_json']) && $newData['specs_json'] != $oldData['specs_json']) {
            $this->compareAndLogSpecs($masterId, $prodId, $adminId, $adminName, $oldData['specs_json'], $newData['specs_json'], $variantIds);
        }
    }

    // Hàm logic: So sánh từng dòng thông số
    private function compareAndLogSpecs($masterId, $prodId, $adminId, $adminName, $jsonOld, $jsonNew, $variantIds) {
        // Chuyển JSON thành mảng phẳng [Key => Data]
        $flatOld = $this->flattenSpecs(json_decode($jsonOld, true) ?? []);
        $flatNew = $this->flattenSpecs(json_decode($jsonNew, true) ?? []);

        // Duyệt qua mảng Mới để xem có gì khác mảng Cũ
        foreach ($flatNew as $key => $item) {
            $oldVal = isset($flatOld[$key]) ? $flatOld[$key]['value'] : '';
            $newVal = $item['value'];

            // Nếu giá trị có sự thay đổi
            if ($oldVal !== $newVal) {
                $attrId = $item['attr_id'];
                $specName = $item['name']; // VD: "Màu sắc", "Camera"

                // Mặc định là SHARED
                $scope = 'SHARED';
                
                // Nếu Attribute ID này nằm trong danh sách biến thể -> Đánh dấu là VARIANT
                if (in_array($attrId, $variantIds)) {
                    $scope = 'VARIANT'; 
                }

                // Ghi log chi tiết: "Thông số: Màu sắc"
                $fieldName = "Thông số: " . $specName;
                
                $this->insertLog($masterId, $prodId, $adminId, $adminName, $scope, $fieldName, $oldVal, $newVal);
            }
        }
    }

    // Hàm Helper: Chuyển cấu trúc Group -> Items thành mảng phẳng
    private function flattenSpecs($specsArray) {
        $result = [];
        if (!is_array($specsArray)) return [];

        foreach ($specsArray as $group) {
            if (isset($group['items']) && is_array($group['items'])) {
                foreach ($group['items'] as $item) {
                    $attrId = isset($item['attr_id']) ? $item['attr_id'] : 0;
                    $name   = isset($item['name']) ? trim($item['name']) : 'Unknown';
                    $val    = isset($item['value']) ? trim($item['value']) : '';

                    // Tạo key duy nhất để so sánh (ID + Tên)
                    $uniqueKey = $attrId . '_' . $name;

                    $result[$uniqueKey] = [
                        'attr_id' => $attrId,
                        'name'    => $name,
                        'value'   => $val
                    ];
                }
            }
        }
        return $result;
    }

    // Insert vào DB
    private function insertLog($masterId, $prodId, $adminId, $adminName, $scope, $field, $old, $new) {
        $masterId = (int)$masterId;
        $prodId   = (int)$prodId;
        $adminId  = $this->escape($adminId);
        $adminName= $this->escape($adminName);
        $scope    = $this->escape($scope);
        $field    = $this->escape($field);
        $old      = $this->escape($old);
        $new      = $this->escape($new);

        $sql = "INSERT INTO product_logs (family_master_id, product_id, admin_id, admin_name, action_scope, field_name, old_value, new_value) 
                VALUES ('$masterId', '$prodId', '$adminId', '$adminName', '$scope', '$field', '$old', '$new')";
        $this->_query($sql);
    }

    public function getLogsByFamily($masterId) {
        $masterId = (int)$masterId;
        
        // [CẬP NHẬT] JOIN với bảng products để lấy Tên, SKU và Specs của sản phẩm bị sửa
        $sql = "SELECT l.*, 
                       p.name as product_name, 
                       p.sku as product_sku, 
                       p.specs_json as product_specs
                FROM product_logs l
                LEFT JOIN products p ON l.product_id = p.id
                WHERE l.family_master_id = '$masterId' 
                ORDER BY l.created_at DESC";
                
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }
}
?>