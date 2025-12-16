<?php
require_once 'app/Models/BaseModel.php';

class ProductModel extends BaseModel {
    // ... (Giữ nguyên các hàm cũ: getParents, createProduct, checkDuplicateChild) ...

    public function getParents() {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE parent_id IS NULL ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProduct($data) {
        $sql = "INSERT INTO products (parent_id, name, sku, slug, category_id, brand_id, price, quantity, specs_json, status) 
                VALUES (:parent_id, :name, :sku, :slug, :category_id, :brand_id, :price, :quantity, :specs_json, 1)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);
        return $this->conn->lastInsertId();
    }

    public function checkDuplicateChild($parentId, $newOptIds) {
        // ... (Code cũ giữ nguyên) ...
        $stmt = $this->conn->prepare("SELECT id FROM products WHERE parent_id = ?");
        $stmt->execute([$parentId]);
        $children = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($children as $childId) {
            $stmtOpt = $this->conn->prepare("SELECT option_id FROM product_attribute_values WHERE product_id = ? AND option_id IS NOT NULL");
            $stmtOpt->execute([$childId]);
            $existingOpts = $stmtOpt->fetchAll(PDO::FETCH_COLUMN);
            $diff1 = array_diff($existingOpts, $newOptIds);
            $diff2 = array_diff($newOptIds, $existingOpts);
            if (empty($diff1) && empty($diff2) && count($existingOpts) == count($newOptIds)) return true;
        }
        return false;
    }

    public function insertEAV($productId, $attrId, $optId, $valueCustom) {
        $stmt = $this->conn->prepare("INSERT INTO product_attribute_values (product_id, attribute_id, option_id, value_custom) VALUES (?, ?, ?, ?)");
        $stmt->execute([$productId, $attrId, $optId, $valueCustom]);
    }

    // --- CÁC HÀM MỚI BỔ SUNG ---

    // Insert Hình ảnh
    public function insertImage($productId, $url) {
        $stmt = $this->conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
        $stmt->execute([$productId, $url]);
    }

    // Lấy ảnh của sản phẩm
    public function getImages($productId) {
        $stmt = $this->conn->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Lấy danh sách anh em (Siblings) để tạo Matrix biến thể
    public function getSiblings($parentId) {
        $sql = "SELECT p.id as p_id, p.price, pav.attribute_id, pav.option_id, pav.value_custom, a.name as attr_name 
                FROM products p
                JOIN product_attribute_values pav ON p.id = pav.product_id
                JOIN attributes a ON pav.attribute_id = a.id
                WHERE p.parent_id = ? OR p.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$parentId, $parentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>