<?php
// 1. Load Models
require_once 'app/Models/BaseModel.php';
require_once 'app/Models/ProductModel.php';
require_once 'app/Models/AttributeModel.php';
require_once 'app/Models/CategoryModel.php';
require_once 'app/Models/BrandModel.php'; // Mới

// 2. Load Controllers
require_once 'app/Controllers/BaseController.php';
require_once 'app/Controllers/HomeController.php';
require_once 'app/Controllers/ProductController.php';
require_once 'app/Controllers/AttributeController.php'; // Mới
require_once 'app/Controllers/CategoryController.php'; // Mới
require_once 'app/Controllers/BrandController.php'; // Mới

$action = $_GET['act'] ?? 'home';

switch ($action) {
    case 'home':
        (new HomeController())->index();
        break;

    // --- PRODUCT ---
    case 'create_product':
        (new ProductController())->create();
        break;
    case 'store_product':
        (new ProductController())->store();
        break;
    case 'generate_variants':
        (new ProductController())->showGenerator();
        break;
    case 'store_variants':
        (new ProductController())->generateVariants();
        break;
    case 'product_detail':
        (new ProductController())->detail();
        break;

    // --- ATTRIBUTE ---
    case 'attributes':
        (new AttributeController())->index();
        break;
    case 'store_attribute':
        (new AttributeController())->store();
        break;
    case 'delete_attribute':
        (new AttributeController())->delete();
        break;

    // --- CATEGORY ---
    case 'category_list':
        (new CategoryController())->index();
        break;
    case 'store_category':
        (new CategoryController())->store();
        break;
    case 'delete_category':
        (new CategoryController())->delete();
        break;

    // --- BRAND ---
    case 'brand_setup':
        (new BrandController())->index();
        break;
    case 'store_brand':
        (new BrandController())->store();
        break;

    default:
        echo "404 Not Found";
}
?>