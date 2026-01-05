<?php
require_once __DIR__ . '/BaseModel.php';

class AttributeModel extends BaseModel {
    
    // Lấy danh sách để hiển thị ra bảng (index)
    // File: models/AttributeModel.php

    // [THAY THẾ] Hàm getAll hỗ trợ Tìm kiếm & Phân trang
    public function getAll($keyword = '', $page = 1, $limit = 0) {
        $where = "1=1";
        if ($keyword) {
            $kw = $this->escape($keyword);
            $where .= " AND (a.name LIKE '%$kw%' OR a.code LIKE '%$kw%')";
        }

        $sql = "SELECT a.*, GROUP_CONCAT(o.value SEPARATOR ', ') as opts_list 
                FROM attributes a 
                LEFT JOIN attribute_options o ON a.id = o.attribute_id 
                WHERE $where
                GROUP BY a.id 
                ORDER BY a.id DESC";

        // Chỉ áp dụng phân trang nếu limit > 0
        if ($limit > 0) {
            $offset = ($page - 1) * $limit;
            $sql .= " LIMIT $offset, $limit";
        }
        
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    // [THÊM MỚI] Đếm tổng số bản ghi (cho Phân trang)
    public function countAll($keyword = '') {
        $where = "1=1";
        if ($keyword) {
            $kw = $this->escape($keyword);
            $where .= " AND (name LIKE '%$kw%' OR code LIKE '%$kw%')";
        }
        $sql = "SELECT COUNT(*) as total FROM attributes WHERE $where";
        $result = $this->_query($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    // [THÊM MỚI] Đếm số lượng biến thể (cho Thống kê đầu trang)
    public function countVariants() {
        $result = $this->_query("SELECT COUNT(*) as total FROM attributes WHERE is_variant = 1");
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    // [THÊM MỚI] Đếm số lượng Custom (cho Thống kê đầu trang)
    public function countCustomizable() {
        $result = $this->_query("SELECT COUNT(*) as total FROM attributes WHERE is_customizable = 1");
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    // Lấy chi tiết để đổ vào Form Sửa
    public function getById($id) {
        $id = $this->escape($id);
        $result = $this->_query("SELECT * FROM attributes WHERE id = '$id'");
        $data = $result ? mysqli_fetch_assoc($result) : null;
        
        if ($data) {
            // Lấy options dạng chuỗi để hiển thị vào textarea
            $rsOpt = $this->_query("SELECT value FROM attribute_options WHERE attribute_id = '$id'");
            $opts = [];
            if($rsOpt) {
                while($row = mysqli_fetch_assoc($rsOpt)) $opts[] = $row['value'];
            }
            $data['options_str'] = implode(', ', $opts);
        }
        return $data;
    }

    // Dùng cho Controller Product (Lấy options để hiển thị dropdown)
    public function getAllOptionsGrouped() {
        $sql = "SELECT * FROM attribute_options ORDER BY attribute_id ASC, id ASC";
        $result = $this->_query($sql);
        $data = [];
        if($result) {
            while($row = mysqli_fetch_assoc($result)){
                $data[$row['attribute_id']][] =  $row;
            }
        }
        return $data;
    }

    // Kiểm tra trùng mã code (Validation)
    public function checkCodeExists($code, $excludeId = 0) {
        $code = $this->escape($code);
        $sql = "SELECT id FROM attributes WHERE code = '$code'";
        if ($excludeId > 0) $sql .= " AND id != '$excludeId'";
        $rs = $this->_query($sql);
        return mysqli_num_rows($rs) > 0;
    }

    // [CREATE] Thêm mới thuộc tính
    public function create($code, $name, $isCustom, $isVariant, $options) {
        $code = $this->escape($code);
        $name = $this->escape($name);
        $isCustom = (int)$isCustom;
        $isVariant = (int)$isVariant;

        $sql = "INSERT INTO attributes (code, name, input_type, is_customizable, is_variant) 
                VALUES ('$code', '$name', 'select', '$isCustom', '$isVariant')";
        
        if ($this->_query($sql)) {
            $attrId = mysqli_insert_id($this->conn);
            $this->insertOptions($attrId, $options);
            return true;
        }
        return false;
    }

    // [UPDATE] Cập nhật thuộc tính
    public function update($id, $code, $name, $isCustom, $isVariant, $options) {
        $id = $this->escape($id);
        $code = $this->escape($code);
        $name = $this->escape($name);
        $isCustom = (int)$isCustom;
        $isVariant = (int)$isVariant;

        $sql = "UPDATE attributes SET code='$code', name='$name', is_customizable='$isCustom', is_variant='$isVariant' WHERE id='$id'";
        
        if ($this->_query($sql)) {
            $this->insertOptions($id, $options);
            return true;
        }
        return false;
    }

    // Helper: Thêm Options (Logic cũ: Chỉ thêm mới, KHÔNG xóa cũ)
    private function insertOptions($attrId, $optionsArr) {
        foreach ($optionsArr as $val) {
            $val = trim($this->escape($val));
            if ($val !== '') {
                // Check trùng lặp trong DB trước khi insert
                $checkSql = "SELECT id FROM attribute_options WHERE attribute_id='$attrId' AND value='$val'";
                $rsCheck = $this->_query($checkSql);
                if (mysqli_num_rows($rsCheck) == 0) {
                    $this->_query("INSERT INTO attribute_options (attribute_id, value) VALUES ('$attrId', '$val')");
                }
            }
        }
    }

    // Xóa (Lưu ý: Nếu thuộc tính đang dùng trong SP thì MySQL có thể chặn nếu có FK)
    public function delete($id) {
        $id = $this->escape($id);
        $this->_query("DELETE FROM attribute_options WHERE attribute_id='$id'");
        return $this->_query("DELETE FROM attributes WHERE id='$id'");
    }
    // [CLIENT] Lấy các thuộc tính dùng để lọc (Sidebar)
    // [CLIENT] Lấy các thuộc tính dùng để lọc (Sidebar)
    // [CLIENT] Lấy các thuộc tính dùng để lọc (Sidebar) - PHIÊN BẢN NÂNG CẤP
    public function getFiltersByCateForClient($cateId) {
        $cateId = $this->escape($cateId);

        // BƯỚC 1: Lấy danh sách CÁC THUỘC TÍNH có liên quan đến danh mục này
        // (Logic: Chỉ cần có ít nhất 1 sản phẩm trong danh mục dùng thuộc tính này thì sẽ hiện tiêu đề thuộc tính đó)
        // [QUAN TRỌNG]: Đã bỏ điều kiện "AND a.is_variant = 1" để lấy cả thuộc tính lọc thường
        $sqlAttrs = "SELECT DISTINCT a.id, a.name, a.code, a.input_type
                     FROM attributes a
                     JOIN product_attribute_values pav ON a.id = pav.attribute_id
                     JOIN products p ON pav.product_id = p.id
                     WHERE p.category_id = '$cateId' 
                     AND p.status = 1 
                     ORDER BY a.id ASC";
        
        $rsAttrs = $this->_query($sqlAttrs);
        $attributes = $rsAttrs ? mysqli_fetch_all($rsAttrs, MYSQLI_ASSOC) : [];

        // BƯỚC 2: Lấy TOÀN BỘ GIÁ TRỊ (Full Options) của từng thuộc tính
        foreach ($attributes as &$attr) {
            $attrId = $attr['id'];
            $options = [];

            if ($attr['input_type'] == 'select') {
                // Nếu là kiểu Select: Lấy tất cả Option định nghĩa trong bảng attribute_options
                // Bất kể sản phẩm có dùng hay không (theo yêu cầu của bạn)
                $sqlOpts = "SELECT value FROM attribute_options 
                            WHERE attribute_id = '$attrId' 
                            ORDER BY id ASC";
                $rsOpts = $this->_query($sqlOpts);
                if ($rsOpts) {
                    while($row = mysqli_fetch_assoc($rsOpts)) {
                        $options[] = $row['value'];
                    }
                }
            } else {
                // Nếu là kiểu Text (nhập tay): Vẫn phải lấy từ sản phẩm thực tế vì không có bảng options
                $sqlOpts = "SELECT DISTINCT pav.value_custom
                            FROM product_attribute_values pav
                            JOIN products p ON pav.product_id = p.id
                            WHERE p.category_id = '$cateId' 
                            AND pav.attribute_id = '$attrId'
                            AND pav.value_custom IS NOT NULL AND pav.value_custom != ''
                            ORDER BY pav.value_custom ASC";
                 $rsOpts = $this->_query($sqlOpts);
                 if ($rsOpts) {
                     while($row = mysqli_fetch_assoc($rsOpts)) {
                         $options[] = $row['value_custom'];
                     }
                 }
            }
            
            $attr['filter_options'] = $options;
        }

        return $attributes;
    }
    // public function getValuesByAttrId($attrId) {
    //     $attrId = $this->escape($attrId);
    //     $sql = "SELECT id, value FROM attribute_options WHERE attribute_id = '$attrId' ORDER BY id ASC";
    //     $result = $this->_query($sql);
    //     return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    // }
}
?>