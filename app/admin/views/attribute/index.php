<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php 
    $totalRecords = isset($totalRecords) ? $totalRecords : 0;
    $totalPages   = isset($totalPages) ? $totalPages : 0;
    $page         = isset($page) ? $page : 1;
    $keyword      = isset($keyword) ? $keyword : '';
    
    // Biến thống kê từ Controller
    $totalStat    = isset($totalStat) ? $totalStat : 0;
    $variantCount = isset($variantCount) ? $variantCount : 0;
    $customCount  = isset($customCount) ? $customCount : 0;
?>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #4e73df !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-primary small mb-1">Tổng thuộc tính</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $totalStat ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f6c23e !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-warning small mb-1">Dùng làm biến thể</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $variantCount ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #e74a3b !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-danger small mb-1">Cho phép tùy chỉnh</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $customCount ?></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Cấu hình Thuộc tính</h4>
        <p class="text-muted small mb-0">Quản lý các đặc tính sản phẩm (Màu sắc, Size, RAM...)</p>
    </div>
    <a href="admin/attribute/create" class="btn btn-primary shadow-sm px-3">
        <i class="fa fa-plus-circle me-2"></i>Thêm mới
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fa fa-check-circle me-2"></i>
        <?php 
            if ($_GET['msg'] == 'created') echo "Tạo thuộc tính mới thành công!";
            elseif ($_GET['msg'] == 'updated') echo "Cập nhật thành công!";
            elseif ($_GET['msg'] == 'deleted') echo "Đã xóa thuộc tính.";
            else echo htmlspecialchars($_GET['msg']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom-0">
        <form id="filterForm" class="d-flex align-items-center" onsubmit="return false;">
            <input type="hidden" name="page" id="pageInput" value="<?= $page ?>">

            <div class="input-group" style="max-width: 400px;">
                <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
                <input type="text" name="q" id="keyword" 
                       value="<?= htmlspecialchars($keyword) ?>"
                       class="form-control bg-light border-start-0" 
                       placeholder="Tìm nhanh thuộc tính...">
            </div>
            
            <div id="loadingSpinner" class="spinner-border spinner-border-sm text-primary ms-2 d-none" role="status"></div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3" width="50">ID</th>
                        <th width="150">Mã (Code)</th>
                        <th width="250">Tên hiển thị</th>
                        <th>Phân loại</th>
                        <th>Giá trị mẫu</th>
                        <th class="text-end pe-4" width="150">Hành động</th>
                    </tr>
                </thead>
                <tbody id="attrTableBody">
                    <?php if(!empty($listAttrs)): ?>
                        <?php foreach($listAttrs as $row): ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?= $row['id'] ?></td>
                                <td>
                                    <code class="bg-light text-primary px-2 py-1 rounded border border-light fw-bold">
                                        <?= htmlspecialchars($row['code']) ?>
                                    </code>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($row['name']) ?></span>
                                </td>
                                <td>
                                    <?php if(!empty($row['is_variant'])): ?>
                                        <span class="badge bg-purple text-purple bg-opacity-10 border border-purple-light me-1">
                                            <i class="fa fa-tags me-1"></i>Biến thể
                                        </span>
                                    <?php endif; ?>

                                    <?php if(!empty($row['is_customizable'])): ?>
                                        <span class="badge bg-info text-info bg-opacity-10 border border-info-light">
                                            <i class="fa fa-pen me-1"></i>Custom
                                        </span>
                                    <?php endif; ?>

                                    <?php if(empty($row['is_variant']) && empty($row['is_customizable'])): ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border">Thông số thường</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small text-truncate" style="max-width: 300px;">
                                    <i class="fa fa-list-ul me-1 text-muted"></i>
                                    <?= htmlspecialchars($row['opts_list'] ?? '(Chưa có giá trị)') ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="admin/attribute/edit?id=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary border-0 rounded-circle" title="Sửa">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    <a href="admin/attribute/delete?id=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger border-0 rounded-circle ms-1" 
                                       onclick="return confirm('⚠️ Cảnh báo: Xóa thuộc tính này có thể ảnh hưởng đến các sản phẩm đang sử dụng nó.\n\nBạn có chắc chắn muốn xóa?')" title="Xóa">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Không tìm thấy thuộc tính nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer bg-white py-3">
        <div class="d-flex justify-content-between align-items-center" id="pagination-container">
            <div class="small text-muted">
                Hiển thị <strong><?= count($listAttrs) ?></strong> / <strong><?= $totalRecords ?></strong> kết quả
            </div>
            <?php require __DIR__ . '/../layouts/pagination.php'; ?>
        </div>
    </div>
</div>

<style>
    .bg-purple { background-color: #f3e5f5 !important; }
    .text-purple { color: #7b1fa2 !important; }
    .border-purple-light { border-color: #e1bee7 !important; }
    .bg-info-light { background-color: #e3f2fd !important; }
    .border-info-light { border-color: #bbdefb !important; }
</style>

<script>
    // [CẤU HÌNH] URL API cho trang Attribute
    const API_URL = '<?= $this->baseUrl ?>admin/attribute'; 
    const TABLE_BODY_ID = 'attrTableBody';

    function changePage(newPage) {
        event.preventDefault();
        const pageInput = document.getElementById('pageInput');
        if(pageInput) {
            pageInput.value = newPage;
            fetchData();
        }
    }

    function fetchData() {
        const form = document.getElementById('filterForm');
        const spinner = document.getElementById('loadingSpinner');
        const tableBody = document.getElementById(TABLE_BODY_ID);
        const paginationContainer = document.getElementById('pagination-container');

        spinner.classList.remove('d-none');
        
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        
        const newUrl = API_URL + '?' + params.toString();
        window.history.pushState({path: newUrl}, '', newUrl);

        fetch(newUrl)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newTbody = doc.getElementById(TABLE_BODY_ID);
                if(newTbody && tableBody) {
                    tableBody.innerHTML = newTbody.innerHTML;
                }

                const newPagination = doc.getElementById('pagination-container');
                if(newPagination && paginationContainer) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                }
            })
            .catch(err => console.error(err))
            .finally(() => spinner.classList.add('d-none'));
    }

    document.addEventListener("DOMContentLoaded", function() {
        const keywordInput = document.getElementById('keyword');
        let timeout = null;

        if (keywordInput) {
            keywordInput.addEventListener('input', () => {
                const pInput = document.getElementById('pageInput');
                if(pInput) pInput.value = 1; 
                
                clearTimeout(timeout);
                timeout = setTimeout(fetchData, 400); 
            });
        }
        
        window.addEventListener('popstate', function() {
            location.reload(); 
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>