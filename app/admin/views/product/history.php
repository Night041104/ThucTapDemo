<?php
// --- HELPER: DỊCH & FORMAT DỮ LIỆU ---
function translateField($field) {
    if (strpos($field, 'Thông số:') === 0) return $field; 

    $map = [
        'name'         => 'Tên sản phẩm',
        'slug'         => 'Slug (URL)',
        'price'        => 'Giá bán',
        'market_price' => 'Giá niêm yết',
        'quantity'     => 'Tồn kho',
        'thumbnail'    => 'Ảnh đại diện',
        'gallery'      => 'Album ảnh',
        'description'  => 'Mô tả',
        'status'       => 'Trạng thái',
        'brand_id'     => 'Thương hiệu',
        'category_id'  => 'Danh mục',
        'specs_json'   => 'Thông số kỹ thuật'
    ];
    return $map[$field] ?? $field;
}

function formatValue($field, $val, $maps = []) {
    if ($val === null || $val === '') return '<span class="text-muted small"><em>(Rỗng)</em></span>';

    switch ($field) {
        case 'status':
            if ($val == 1) return '<span class="badge bg-success">Đang bán</span>';
            if ($val == 0) return '<span class="badge bg-secondary">Tạm ẩn</span>';
            if ($val == -1) return '<span class="badge bg-dark">Ngừng KD</span>';
            break;
            
        case 'price':
        case 'market_price':
            return '<span class="fw-bold text-dark">' . number_format((float)$val) . ' ₫</span>';
            
        case 'brand_id':
            if (isset($maps['brands'][$val])) {
                return '<strong class="text-primary">' . htmlspecialchars($maps['brands'][$val]) . '</strong>';
            }
            return "ID: " . $val;

        case 'category_id':
            if (isset($maps['cates'][$val])) {
                return '<strong>' . htmlspecialchars($maps['cates'][$val]) . '</strong>';
            }
            return "ID: " . $val;

        case 'thumbnail':
            return '<img src="'.$val.'" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">';
            
        default:
            return htmlspecialchars($val);
    }
    return htmlspecialchars($val);
}

// Nhúng Header Layout
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary text-white rounded p-3 me-3 shadow-sm">
            <i class="fa fa-history fa-lg"></i>
        </div>
        <div>
            <div class="text-uppercase text-muted fw-bold small ls-1">Nhật ký hoạt động (Log)</div>
            <h4 class="fw-bold text-dark mb-0">
                <?= htmlspecialchars($masterProd['name'] ?? 'Sản phẩm') ?>
                <span class="badge bg-light text-primary border ms-2 align-middle" style="font-size: 0.7rem;">FAMILY LOG</span>
            </h4>
        </div>
    </div>
    <a href="index.php?module=admin&controller=product&action=index" class="btn btn-outline-secondary">
        <i class="fa fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3" width="250">Người thực hiện / Thời gian</th>
                        <th width="300">Phạm vi tác động</th>
                        <th>Chi tiết thay đổi (Trước -> Sau)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($logs)): ?>
                        <?php foreach($logs as $log): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <?= strtoupper(substr($log['admin_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($log['admin_name']) ?></div>
                                            <div class="small text-muted">
                                                <i class="fa fa-clock me-1"></i> 
                                                <?= date('H:i - d/m/Y', strtotime($log['created_at'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <?php if($log['action_scope'] == 'SHARED'): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 mb-1">
                                            <i class="fa fa-globe me-1"></i> TOÀN DÒNG SP
                                        </span>
                                        <div class="small text-muted mt-1">Cập nhật thông tin chung</div>
                                    <?php else: ?>
                                        <div class="mb-2">
                                            <span class="badge bg-purple text-purple bg-opacity-10 border border-purple-light">
                                                <i class="fa fa-tag me-1"></i> BIẾN THỂ (SKU)
                                            </span>
                                        </div>

                                        <?php if (!empty($log['product_name'])): ?>
                                            <div class="border-start border-3 ps-2">
                                                <div class="fw-bold text-dark small"><?= htmlspecialchars($log['product_name']) ?></div>
                                                <div class="text-muted small">SKU: <?= $log['product_sku'] ?></div>

                                                <?php 
                                                    $specs = json_decode($log['product_specs'], true) ?? [];
                                                    $variantsFound = [];
                                                    if (!empty($specs) && !empty($variantIds)) {
                                                        foreach ($specs as $group) {
                                                            if(isset($group['items'])) {
                                                                foreach ($group['items'] as $item) {
                                                                    if (isset($item['attr_id']) && in_array($item['attr_id'], $variantIds) && !empty($item['value'])) {
                                                                        $variantsFound[] = htmlspecialchars($item['name']) . ': ' . htmlspecialchars($item['value']);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                ?>
                                                
                                                <?php if(!empty($variantsFound)): ?>
                                                    <div class="mt-1">
                                                        <?php foreach($variantsFound as $vText): ?>
                                                            <span class="badge bg-light text-dark border"><?= $vText ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic small">
                                                (Sản phẩm đã bị xóa - ID: <?= $log['product_id'] ?>)
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="mb-2 fw-bold text-secondary small text-uppercase">
                                        <?= translateField($log['field_name']) ?>
                                    </div>
                                    
                                    <div class="d-flex align-items-stretch border rounded overflow-hidden">
                                        <div class="flex-fill p-2 bg-danger bg-opacity-10 text-danger border-end">
                                            <?= formatValue(
                                                $log['field_name'], 
                                                $log['old_value'], 
                                                ['brands' => $brandsMap ?? [], 'cates' => $catesMap ?? []]
                                            ) ?>
                                        </div>
                                        
                                        <div class="bg-light d-flex align-items-center justify-content-center px-2 text-muted">
                                            <i class="fa fa-arrow-right"></i>
                                        </div>
                                        
                                        <div class="flex-fill p-2 bg-success bg-opacity-10 text-success fw-bold">
                                            <?= formatValue(
                                                $log['field_name'], 
                                                $log['new_value'], 
                                                ['brands' => $brandsMap ?? [], 'cates' => $catesMap ?? []]
                                            ) ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fa fa-clipboard-list fa-3x opacity-25"></i></div>
                                <p>Chưa có lịch sử thay đổi nào được ghi lại.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-purple { background-color: #f3e5f5 !important; }
    .text-purple { color: #7b1fa2 !important; }
    .border-purple-light { border-color: #e1bee7 !important; }
    .ls-1 { letter-spacing: 1px; }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>