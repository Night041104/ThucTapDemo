<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php 
 $totalRecords = isset($totalRecords) ? $totalRecords : 0;
    $totalProd = count($products);
    $activeProd = 0; $outOfStock = 0; $hiddenProd = 0;
    foreach($p2 as $p) {
        if($p['status'] == 1) $activeProd++;
        if($p['status'] == 0) $hiddenProd++;
        if($p['quantity'] <= 0) $outOfStock++;
    }
?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #4e73df !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-primary small mb-1">Tổng sản phẩm</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $totalRecords ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #1cc88a !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-success small mb-1">Đang kinh doanh</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $activeProd ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #e74a3b !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-danger small mb-1">Hết hàng (Kho)</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $outOfStock ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f6c23e !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-warning small mb-1">Đang tạm ẩn</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $hiddenProd ?></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Kho hàng tổng hợp</h4>
        <p class="text-muted small mb-0">Quản lý Master Product và các biến thể (SKU)</p>
    </div>
    <a href="admin/product/create" class="btn btn-primary shadow-sm px-3">
        <i class="fa fa-plus-circle me-2"></i>Tạo Sản Phẩm Mới
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fa fa-check-circle me-2"></i>
        <?php 
            if ($_GET['msg'] == 'created') echo "Tạo sản phẩm mới thành công!";
            elseif ($_GET['msg'] == 'updated') echo "Cập nhật sản phẩm thành công!";
            elseif ($_GET['msg'] == 'cloned') echo "Nhân bản sản phẩm thành công!";
            elseif ($_GET['msg'] == 'deleted') echo "Đã xóa sản phẩm khỏi hệ thống.";
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom-0">
       <form id="filterForm" class="row g-2 align-items-center" onsubmit="return false;">
            <input type="hidden" name="page" id="pageInput" value="<?= $page ?>">

            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" name="q" id="keyword" 
                           value="<?= htmlspecialchars($keyword ?? '') ?>" 
                           class="form-control bg-light border-start-0" 
                           placeholder="Tìm tên SP, SKU...">
                </div>
            </div>
            
            <div class="col-md-3">
                <select name="cate_id" id="cate_id" class="form-select bg-light">
                    <option value="0">-- Tất cả danh mục --</option>
                    <?php foreach($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (isset($filterCateId) && $filterCateId == $c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <select name="master_id" id="master_id" class="form-select bg-light">
                    <option value="0">-- Tất cả dòng SP --</option>
                    <?php foreach($masters as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= (isset($filterMasterId) && $filterMasterId == $m['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['name']) ?> 
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-auto d-flex align-items-center gap-2">
                <div id="loadingSpinner" class="spinner-border spinner-border-sm text-primary d-none" role="status"></div>
                
                <button type="button" id="btnClearFilter" class="btn btn-light text-danger fw-bold" title="Xóa lọc">
                    <i class="fa fa-times"></i>
                </button>
            </div>  
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3" width="60">Ảnh</th>
                        <th width="300">Tên Sản Phẩm / SKU</th>
                        <th>Biến thể (Specs)</th>
                        <th>Thông tin</th>
                        <th>Giá bán</th>
                        <th>Kho</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4" width="100">Xử lý</th>
                    </tr>
                </thead>
                
                <tbody id="productTableBody">
                    <?php if(!empty($products)): ?>
                        <?php foreach($products as $row): ?>
                            <?php 
                                $isChild = ($row['parent_id'] > 0); 
                                $bgClass = $isChild ? 'bg-light bg-opacity-50' : '';
                                $specs = json_decode($row['specs_json'], true) ?? [];
                                $variantHtml = '';
                                if (!empty($specs) && !empty($variantIds)) {
                                    foreach ($specs as $group) {
                                        if(isset($group['items'])) {
                                            foreach ($group['items'] as $item) {
                                                if (isset($item['attr_id']) && in_array($item['attr_id'], $variantIds) && !empty($item['value'])) {
                                                    $variantHtml .= '<span class="badge bg-purple text-purple border border-purple-light me-1 mb-1">' . htmlspecialchars($item['name']) . ': ' . htmlspecialchars($item['value']) . '</span>';
                                                }
                                            }
                                        }
                                    }
                                }
                            ?>
                            <tr class="<?= $bgClass ?>">
                                <td class="ps-4">
                                    <div class="position-relative d-inline-block">
                                        <img src="<?= $row['thumbnail'] ?>" class="rounded border" style="width:48px; height:48px; object-fit:contain; background:#fff; padding: 2px;">
                                        <?php if($isChild): ?>
                                            <span class="position-absolute top-0 start-0 translate-middle p-1 bg-secondary border border-light rounded-circle" title="Child">
                                                <span class="visually-hidden">Child</span>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <?php if($isChild): ?><div class="text-muted me-2" style="font-size: 1.2rem; opacity: 0.5;">↳</div><?php endif; ?>
                                        <div>
                                            <a href="admin/product/edit?id=<?= $row['id'] ?>" class="fw-bold text-dark text-decoration-none">
                                                <?= $row['name'] ?>
                                            </a>
                                            <div class="d-flex align-items-center mt-1">
                                                <span class="badge bg-light text-secondary border me-2">SKU: <?= $row['sku'] ?></span>
                                                <?php if(!$isChild): ?><span class="badge bg-success bg-opacity-10 text-success" style="font-size: 10px;">MASTER</span><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $variantHtml ? $variantHtml : '<span class="text-muted small fst-italic">-- Mặc định --</span>' ?></td>
                                <td class="small">
                                    <div class="text-muted"><i class="fa fa-folder me-1 text-warning"></i> <?= $row['cate_name'] ?></div>
                                    <div class="text-muted mt-1"><i class="fa fa-tag me-1 text-info"></i> <?= $row['brand_name'] ?></div>
                                </td>
                                <td class="fw-bold text-danger"><?= number_format($row['price']) ?>₫</td>
                                <td>
                                    <?php if($row['quantity'] > 0): ?>
                                        <span class="fw-bold text-success"><?= $row['quantity'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Hết hàng</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row['status'] == 1): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success"><i class="fa fa-check-circle"></i> Đang bán</span>
                                    <?php elseif($row['status'] == 0): ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary"><i class="fa fa-eye-slash"></i> Tạm ẩn</span>
                                    <?php else: ?>
                                        <span class="badge bg-dark"><i class="fa fa-ban"></i> Ngừng KD</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                            <i class="fa fa-ellipsis-v text-muted"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                            <?php $historyId = ($row['parent_id'] > 0) ? $row['parent_id'] : $row['id']; ?>
                                            <li><a class="dropdown-item" href="admin/product/edit?id=<?= $row['id'] ?>"><i class="fa fa-pen text-primary me-2"></i> Chỉnh sửa</a></li>
                                            <li><a class="dropdown-item" href="admin/product/history?master_id=<?= $historyId ?>"><i class="fa fa-history text-info me-2"></i> Xem lịch sử</a></li>
                                            <li><a class="dropdown-item" href="admin/product/clone?id=<?= $row['id'] ?>"><i class="fa fa-copy text-warning me-2"></i> Nhân bản</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="admin/product/delete?id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa?')"><i class="fa fa-trash me-2"></i> Xóa sản phẩm</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" class="mb-3 opacity-50">
                                <p class="text-muted">Không tìm thấy sản phẩm nào.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-footer bg-white py-3">
        <div class="d-flex justify-content-between align-items-center" id="pagination-container">
            <div class="small text-muted">
                Hiển thị <strong><?= count($products) ?></strong> / <strong><?= $totalRecords ?></strong> kết quả
            </div>
            <?php require __DIR__ . '/../layouts/pagination.php'; ?>
        </div>
    </div>
</div>

<style>
    .bg-purple { background-color: #f3e5f5 !important; }
    .text-purple { color: #7b1fa2 !important; }
    .border-purple-light { border-color: #e1bee7 !important; }
</style>

<script>
    // [CẤU HÌNH] Sử dụng biến $baseUrl từ header.php thay vì $this->baseUrl (vì $this->baseUrl là private)
    const API_URL = '<?= $baseUrl ?>admin/product';

    // Hàm chuyển trang
    function changePage(newPage) {
        event.preventDefault();
        // Cập nhật giá trị vào input ẩn
        const pageInput = document.getElementById('pageInput');
        if(pageInput) {
            pageInput.value = newPage;
            fetchData();
        } else {
            console.error("Lỗi: Không tìm thấy input id='pageInput'");
        }
    }

    // Hàm tải dữ liệu (AJAX)
    function fetchData() {
        const form = document.getElementById('filterForm');
        const spinner = document.getElementById('loadingSpinner');
        
        const tableBody = document.getElementById('productTableBody'); 
        const paginationContainer = document.getElementById('pagination-container');

        spinner.classList.remove('d-none');
        
        // FormData sẽ tự động lấy giá trị của input name="page"
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        
        // Cập nhật URL trên trình duyệt
        const newUrl = API_URL + '?' + params.toString();
        window.history.pushState({path: newUrl}, '', newUrl);

        fetch(newUrl)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // 1. Cập nhật bảng
                const newTbody = doc.getElementById('productTableBody');
                if(newTbody && tableBody) {
                    tableBody.innerHTML = newTbody.innerHTML;
                }

                // 2. Cập nhật phân trang
                const newPagination = doc.getElementById('pagination-container');
                if(newPagination && paginationContainer) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                }
            })
            .catch(err => console.error('Lỗi tải trang:', err))
            .finally(() => spinner.classList.add('d-none'));
    }

    document.addEventListener("DOMContentLoaded", function() {
        const inputs = document.querySelectorAll('#filterForm input:not([type="hidden"]), #filterForm select');
        const btnClear = document.getElementById('btnClearFilter');
        let timeout = null;

        inputs.forEach(input => {
            if (input.type === 'text') {
                input.addEventListener('input', () => {
                    const pInput = document.getElementById('pageInput');
                    if(pInput) pInput.value = 1; // Reset về trang 1 khi tìm kiếm
                    
                    clearTimeout(timeout);
                    timeout = setTimeout(fetchData, 400); 
                });
            } else {
                input.addEventListener('change', () => {
                    const pInput = document.getElementById('pageInput');
                    if(pInput) pInput.value = 1;
                    fetchData();
                });
            }
        });

        if (btnClear) {
            btnClear.addEventListener('click', function() {
                document.getElementById('filterForm').reset();
                document.querySelectorAll('#filterForm select').forEach(sel => sel.value = sel.options[0].value);
                
                const pInput = document.getElementById('pageInput');
                if(pInput) pInput.value = 1;
                
                fetchData();
            });
        }
        
        window.addEventListener('popstate', function() {
            location.reload(); 
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>