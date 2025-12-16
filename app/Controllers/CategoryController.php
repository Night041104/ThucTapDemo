<?php
class CategoryController extends BaseController {
    private $model;
    private $attrModel;

    public function __construct() {
        $this->model = new CategoryModel();
        $this->attrModel = new AttributeModel();
    }

    public function index() {
        $current = [];
        if (isset($_GET['edit'])) {
            $current = $this->model->getById($_GET['edit']);
            $current['template'] = json_decode($current['spec_template'], true);
        }
        
        $list = $this->model->getAll();
        $attrs = $this->attrModel->getAll(); // Để fill vào dropdown template builder

        $this->view('category/index', [
            'list' => $list, 
            'current' => $current,
            'attrs' => $attrs
        ]);
    }

    public function store() {
        // Logic xử lý Template Builder từ Form
        $template = [];
        if (isset($_POST['groups'])) {
            foreach ($_POST['groups'] as $gIndex => $groupName) {
                if (!$groupName) continue;
                $items = [];
                if (isset($_POST['items'][$gIndex]['name'])) {
                    foreach ($_POST['items'][$gIndex]['name'] as $iIndex => $itemName) {
                        if (!$itemName) continue;
                        $type = $_POST['items'][$gIndex]['type'][$iIndex];
                        $itemData = ['name' => $itemName, 'type' => $type];
                        if ($type == 'attribute') {
                            $itemData['attribute_id'] = $_POST['items'][$gIndex]['attr_id'][$iIndex];
                        }
                        $items[] = $itemData;
                    }
                }
                $template[] = ['group_name' => $groupName, 'items' => $items];
            }
        }

        $this->model->save([
            'id' => $_POST['id'] ?? null,
            'name' => $_POST['name'],
            'slug' => $_POST['slug'],
            'spec_template' => json_encode($template, JSON_UNESCAPED_UNICODE)
        ]);
        $this->redirect('index.php?act=category_list');
    }

    public function delete() {
        if (isset($_GET['id'])) $this->model->delete($_GET['id']);
        $this->redirect('index.php?act=category_list');
    }
}
?>