<?php
require_once __DIR__ . '/BaseModel.php';

class CategoryModel extends BaseModel {
    public function getAll() {
        // Thực hiện truy vấn
        $result = mysqli_query($this->conn, "SELECT * FROM categories ORDER BY id DESC");
        
        // SỬA: Kiểm tra kỹ, nếu lỗi hoặc null thì trả về mảng rỗng [] ngay
        if (!$result) {
            return [];
        }

        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        // Đảm bảo kết quả là mảng
        return is_array($data) ? $data : [];
    }

    public function getById($id) {
        $id = $this->escape($id);
        $result = mysqli_query($this->conn, "SELECT * FROM categories WHERE id = '$id'");
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    public function create($name, $jsonTemplate) {
        $name = $this->escape($name);
        $slug = $this->escape($this->createSlug($name)); 
        $jsonTemplate = $this->escape($jsonTemplate);
        
        $sql = "INSERT INTO categories (name, slug, spec_template) VALUES ('$name', '$slug', '$jsonTemplate')";
        return mysqli_query($this->conn, $sql);
    }

    public function update($id, $name, $slug, $jsonTemplate) {
        $id = $this->escape($id);
        $name = $this->escape($name);
        $slug = $slug ? $this->escape($slug) : $this->escape($this->createSlug($name));
        $jsonTemplate = $this->escape($jsonTemplate);

        $sql = "UPDATE categories SET name='$name', slug='$slug', spec_template='$jsonTemplate' WHERE id='$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function delete($id) {
        $id = $this->escape($id);
        return mysqli_query($this->conn, "DELETE FROM categories WHERE id = '$id'");
    }
}
?>