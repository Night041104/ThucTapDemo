
<?php
require_once __DIR__ . '/BaseModel.php';

class CategoryModel extends BaseModel {
    public function getIdBySlug($slug) {
        $slug = $this->escape($slug);
        $result = $this->_query("SELECT id FROM categories WHERE slug = '$slug'");
        $row = mysqli_fetch_assoc($result);
        return $row ? $row['id'] : 0;
    }// [CẬP NHẬT] Hàm getAll hỗ trợ tìm kiếm và phân trang
    public function getAll($keyword = '', $page = 1, $limit = 10) {
        $where = "1=1";
        if ($keyword) {
            $kw = $this->escape($keyword);
            $where .= " AND name LIKE '%$kw%'";
        }

        // Tính offset
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM categories WHERE $where ORDER BY id DESC LIMIT $offset, $limit";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    // [THÊM MỚI] Hàm đếm tổng số bản ghi
    public function countAll($keyword = '') {
        $where = "1=1";
        if ($keyword) {
            $kw = $this->escape($keyword);
            $where .= " AND name LIKE '%$kw%'";
        }

        $sql = "SELECT COUNT(*) as total FROM categories WHERE $where";
        $result = $this->_query($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }
    public function countHasConfig() {
        $sql = "SELECT COUNT(*) as total FROM categories WHERE spec_template IS NOT NULL AND spec_template != '' AND spec_template != '[]'";
        $result = $this->_query($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
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

    public function getAttributesByCategory($cateId) {
        $cateId = $this->escape($cateId);
        // Lấy các thuộc tính thuộc về danh mục này
        $sql = "SELECT a.* FROM attributes a 
                JOIN category_attribute ca ON a.id = ca.attribute_id 
                WHERE ca.category_id = '$cateId'
                ORDER BY a.name ASC";
        $result = $this->_query($sql);
        $attributes = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

        // Với mỗi thuộc tính, lấy các giá trị (options) hiện có để khách hàng tích chọn
        foreach ($attributes as &$attr) {
            $attrId = $attr['id'];
            $sqlOption = "SELECT DISTINCT ao.* FROM attribute_options ao
                        JOIN product_attribute_values pav ON ao.id = pav.option_id
                        JOIN products p ON pav.product_id = p.id
                        WHERE ao.attribute_id = '$attrId' AND p.category_id = '$cateId'";
            $resOption = $this->_query($sqlOption);
            $attr['options'] = $resOption ? mysqli_fetch_all($resOption, MYSQLI_ASSOC) : [];
        }

        return $attributes;
    }
}
?>