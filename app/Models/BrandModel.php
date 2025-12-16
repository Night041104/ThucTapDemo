<?php
require_once 'app/Models/BaseModel.php';

class BrandModel extends BaseModel {
    public function create($name, $cateIds) {
        $stmt = $this->conn->prepare("INSERT INTO brands (name) VALUES (?)");
        $stmt->execute([$name]);
        $brandId = $this->conn->lastInsertId();

        if (!empty($cateIds)) {
            $stmtLink = $this->conn->prepare("INSERT INTO category_brand (brand_id, category_id) VALUES (?, ?)");
            foreach ($cateIds as $cateId) {
                $stmtLink->execute([$brandId, $cateId]);
            }
        }
        return $brandId;
    }
}
?>