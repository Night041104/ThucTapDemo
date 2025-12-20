<?php
require_once __DIR__ . '/BaseModel.php';

class CategoryModel extends BaseModel {
    
    public function getAll() {
        $result = $this->_query("SELECT * FROM categories ORDER BY id DESC");
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    public function getById($id) {
        $id = $this->escape($id);
        $result = $this->_query("SELECT * FROM categories WHERE id = '$id'");
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    // [MỚI] Hàm kiểm tra trùng tên danh mục
    public function checkNameExists($name, $excludeId = 0) {
        $name = $this->escape($name);
        $sql = "SELECT id FROM categories WHERE name = '$name'";
        if ($excludeId > 0) {
            $sql .= " AND id != '$excludeId'";
        }
        $result = $this->_query($sql);
        return mysqli_num_rows($result) > 0;
    }

    public function create($name, $jsonTemplate) {
        $name = $this->escape($name);
        // Tự tạo slug từ tên (hàm có sẵn trong BaseModel)
        $slug = $this->escape($this->createSlug($name)); 
        $jsonTemplate = $this->escape($jsonTemplate);
        
        $sql = "INSERT INTO categories (name, slug, spec_template) VALUES ('$name', '$slug', '$jsonTemplate')";
        return $this->_query($sql);
    }

    public function update($id, $name, $slug, $jsonTemplate) {
        $id = $this->escape($id);
        $name = $this->escape($name);
        // Nếu user nhập slug thì lấy, nếu không thì tự generate lại từ name
        $slug = $slug ? $this->escape($slug) : $this->escape($this->createSlug($name));
        $jsonTemplate = $this->escape($jsonTemplate);

        $sql = "UPDATE categories SET name='$name', slug='$slug', spec_template='$jsonTemplate' WHERE id='$id'";
        return $this->_query($sql);
    }

    // [BỔ SUNG] Đếm sản phẩm trước khi xóa (An toàn)
    public function countProducts($cateId) {
        $cateId = $this->escape($cateId);
        $result = $this->_query("SELECT COUNT(*) as total FROM products WHERE category_id = '$cateId'");
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    public function delete($id) {
        $id = $this->escape($id);
        return $this->_query("DELETE FROM categories WHERE id = '$id'");
    }
}
?>