<?php
require_once 'app/Models/BaseModel.php';

class CategoryModel extends BaseModel {
    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM categories ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save($data) {
        if (!empty($data['id'])) {
            $stmt = $this->conn->prepare("UPDATE categories SET name=?, slug=?, spec_template=? WHERE id=?");
            $stmt->execute([$data['name'], $data['slug'], $data['spec_template'], $data['id']]);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO categories (name, slug, spec_template) VALUES (?, ?, ?)");
            $stmt->execute([$data['name'], $data['slug'], $data['spec_template']]);
        }
    }

    public function delete($id) {
        $this->conn->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    }
}
?>