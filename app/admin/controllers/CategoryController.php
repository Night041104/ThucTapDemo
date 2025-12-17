<?php
// Đứng ở app/controllers -> lùi ra app -> vào models
require_once __DIR__ . '/../../models/CategoryModel.php';
require_once __DIR__ . '/../../models/AttributeModel.php'; 

class CategoryController {
    private $cateModel;
    private $attrModel;

    public function __construct() {
        $this->cateModel = new CategoryModel();
        $this->attrModel = new AttributeModel();
    }

    public function index() {
        $listCates = [];
        $data = $this->cateModel->getAll();
        if (is_array($data)) $listCates = $data;

        // Đứng ở app/controllers -> lùi ra app -> vào views
        require __DIR__ . '/../views/category/index.php';
    }

    public function create() {
        $currentData = ['id' => '', 'name' => '', 'slug' => '', 'template' => []];
        $attrs = $this->attrModel->getAll();
        
        // Gọi view form
        require __DIR__ . '/../views/category/form.php';
    }

    public function edit() {
        $id = $_GET['id'] ?? 0;
        $cate = $this->cateModel->getById($id);
        
        if ($cate) {
            $currentData = [
                'id' => $cate['id'], 
                'name' => $cate['name'], 
                'slug' => $cate['slug'],
                'template' => json_decode($cate['spec_template'], true) ?? []
            ];
            $attrs = $this->attrModel->getAll();

            require __DIR__ . '/../views/category/form.php';
        } else {
            // Redirect ra ngoài root index.php
            // Vì file index.php nằm ở root, nên đường dẫn là /THUCTAPDEMO/index.php hoặc đơn giản là index.php nếu base đúng
            header("Location: ../../index.php?module=admin&controller=category&action=index");
            exit;
        }
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? '';
            $name = trim($_POST['name']);
            $slug = trim($_POST['slug'] ?? '');
            
            // ... (Phần xử lý logic JSON giữ nguyên như cũ) ...
            $template = []; 
            if (isset($_POST['groups']) && is_array($_POST['groups'])) {
                foreach ($_POST['groups'] as $gIndex => $groupName) {
                    if (trim($groupName) === '') continue;
                    $items = [];
                    if (isset($_POST['items'][$gIndex]['name'])) {
                        foreach ($_POST['items'][$gIndex]['name'] as $iIndex => $itemName) {
                            if (trim($itemName) === '') continue;
                            $type = $_POST['items'][$gIndex]['type'][$iIndex];
                            $attrId = $_POST['items'][$gIndex]['attr_id'][$iIndex] ?? null;
                            $itemData = ['name' => $itemName, 'type' => $type];
                            if ($type == 'attribute' && $attrId) $itemData['attribute_id'] = (int)$attrId;
                            $items[] = $itemData;
                        }
                    }
                    $template[] = ['group_name' => $groupName, 'items' => $items];
                }
            }
            $jsonTemplate = json_encode($template, JSON_UNESCAPED_UNICODE);

            if ($id) $this->cateModel->update($id, $name, $slug, $jsonTemplate);
            else $this->cateModel->create($name, $jsonTemplate);

            // Redirect: Lùi 2 cấp để ra root
            header("Location: ../../index.php?module=admin&controller=category&action=index");
            exit;
        }
    }

    public function delete() {
        if (isset($_GET['id'])) $this->cateModel->delete($_GET['id']);
        header("Location: ../../index.php?module=admin&controller=category&action=index");
        exit;
    }
}
?>