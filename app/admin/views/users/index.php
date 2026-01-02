<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php 
    $totalUsers = count($users);
    $adminCount = 0;
    $activeCount = 0;
    $blockedCount = 0;

    foreach($users as $u) {
        if($u['role_id'] == 1) $adminCount++;
        if($u['is_verified'] == 1) $activeCount++;
        else $blockedCount++;
    }
?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #4e73df !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-primary small mb-1">Tổng thành viên</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $totalUsers ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #e74a3b !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-danger small mb-1">Quản trị viên (Admin)</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $adminCount ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #1cc88a !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-success small mb-1">Đang hoạt động</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $activeCount ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f6c23e !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-warning small mb-1">Chưa kích hoạt</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $blockedCount ?></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Quản lý Thành viên</h4>
        <p class="text-muted small mb-0">Danh sách tài khoản và phân quyền hệ thống</p>
    </div>
    </div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom-0">
        <form id="filterForm" class="row g-2 align-items-center" onsubmit="return false;">
            <input type="hidden" name="module" value="admin">
            <input type="hidden" name="controller" value="user">
            <input type="hidden" name="action" value="index">

            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" name="keyword" id="keyword" 
                           class="form-control bg-light border-start-0" 
                           placeholder="Tìm tên, email...">
                </div>
            </div>

            <div class="col-md-3">
                <select name="role" id="role" class="form-select bg-light">
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="1">👑 Admin</option>
                    <option value="0">👤 Khách hàng</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="status" id="status" class="form-select bg-light">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="1">✅ Active</option>
                    <option value="0">⛔ Pending</option>
                </select>
            </div>

            <div class="col-md-auto d-flex align-items-center gap-2">
                <div id="loadingSpinner" class="spinner-border spinner-border-sm text-primary d-none" role="status"></div>
                <button type="button" class="btn btn-light text-danger fw-bold" onclick="resetFilter()" title="Xóa lọc">
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
                        <th class="ps-4 py-3">Thành viên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <?php if(!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <?php 
                                            $defaultAvt = 'uploads/default/default_avt.png';
                                            $avt = !empty($u['avatar']) ? $u['avatar'] : $defaultAvt;
                                        ?>
                                        <div class="position-relative">
                                            <img src="<?= htmlspecialchars($avt) ?>" 
                                                 class="rounded-circle border" 
                                                 style="width: 45px; height: 45px; object-fit: cover;"
                                                 onerror="this.src='<?= $defaultAvt ?>'">
                                            <?php if($u['role_id'] == 1): ?>
                                                <span class="position-absolute bottom-0 end-0 bg-danger border border-white rounded-circle p-1" style="width:15px; height:15px; display:block;"></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($u['lname'] . ' ' . $u['fname']) ?></div>
                                            <div class="small text-muted" style="font-size: 0.75rem;">ID: <?= substr($u['id'], 0, 8) ?>...</div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <?php if($u['role_id'] == 1): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3">
                                            <i class="fa fa-crown me-1"></i> Admin
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle rounded-pill px-3">
                                            User
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($u['is_verified'] == 1): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success"><i class="fa fa-check-circle me-1"></i> Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning"><i class="fa fa-clock me-1"></i> Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                            <i class="fa fa-ellipsis-v text-muted"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                            <li>
                                                <a class="dropdown-item" href="index.php?module=admin&controller=user&action=edit&id=<?= $u['id'] ?>">
                                                    <i class="fa fa-user-shield text-primary me-2"></i> Phân quyền
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="index.php?module=admin&controller=user&action=delete&id=<?= $u['id'] ?>" 
                                                   onclick="return confirm('⚠️ CẢNH BÁO: Xóa user này sẽ mất toàn bộ dữ liệu đơn hàng liên quan.\n\nBạn có chắc chắn muốn xóa?')">
                                                    <i class="fa fa-trash me-2"></i> Xóa tài khoản
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Không tìm thấy thành viên nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('filterForm');
        const inputs = form.querySelectorAll('input, select');
        const spinner = document.getElementById('loadingSpinner');
        const tableBody = document.getElementById('userTableBody');
        let timeout = null;

        function fetchUsers() {
            spinner.classList.remove('d-none');
            
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            fetch('index.php?' + params.toString())
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTbody = doc.getElementById('userTableBody');
                    
                    if(newTbody) {
                        tableBody.innerHTML = newTbody.innerHTML;
                    }
                })
                .catch(err => console.error(err))
                .finally(() => {
                    spinner.classList.add('d-none');
                });
        }

        inputs.forEach(input => {
            if (input.type === 'text') {
                input.addEventListener('input', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(fetchUsers, 400); 
                });
            }
            if (input.tagName === 'SELECT') {
                input.addEventListener('change', fetchUsers);
            }
        });
        
        window.resetFilter = function() {
            form.reset();
            fetchUsers();
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>