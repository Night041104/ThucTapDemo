<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
    <style>
        /* CSS dùng chung với các trang Admin khác của bạn */
        body { font-family: sans-serif; padding: 20px; background: #f4f6f8; }
        .table-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #e3f2fd; color: #0d47a1; }
        
        .badge { padding: 5px 10px; border-radius: 4px; color: white; font-size: 12px; font-weight: bold; }
        .st-1 { background: #ffc107; color: black; } /* Chờ xác nhận */
        .st-2 { background: #17a2b8; } /* Đã xác nhận */
        .st-3 { background: #007bff; } /* Đang giao */
        .st-4 { background: #28a745; } /* Hoàn thành */
        .st-5 { background: #dc3545; } /* Đã hủy */

        .btn-view { background: #e3f2fd; color: #1565c0; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; }
    </style>
</head>
<body>

    <h1 style="color:#1565c0">📦 QUẢN LÝ ĐƠN HÀNG</h1>

    <div class="table-box">
        <div style="margin-bottom: 20px; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <form action="index.php" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
        
        <input type="hidden" name="module" value="admin">
        <input type="hidden" name="controller" value="order">
        <input type="hidden" name="action" value="index">

        <div style="flex: 2; min-width: 250px;">
            <input type="text" name="keyword" 
                   value="<?= isset($keyword) ? htmlspecialchars($keyword) : '' ?>" 
                   placeholder="Nhập mã đơn, tên khách, số điện thoại..." 
                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="flex: 1; min-width: 150px;">
            <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="1" <?= (isset($status) && $status == '1') ? 'selected' : '' ?>>Chờ xác nhận</option>
                <option value="2" <?= (isset($status) && $status == '2') ? 'selected' : '' ?>>Đã thanh toán / Xác nhận</option>
                <option value="3" <?= (isset($status) && $status == '3') ? 'selected' : '' ?>>Đang giao hàng</option>
                <option value="4" <?= (isset($status) && $status == '4') ? 'selected' : '' ?>>Hoàn thành</option>
                <option value="5" <?= (isset($status) && $status == '5') ? 'selected' : '' ?>>Đã hủy</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 150px;">
            <select name="payment" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">
                <option value="">-- Tất cả PTTT --</option>
                <option value="COD" <?= (isset($payment) && $payment == 'COD') ? 'selected' : '' ?>>Tiền mặt (COD)</option>
                <option value="VNPAY" <?= (isset($payment) && $payment == 'VNPAY') ? 'selected' : '' ?>>Ví điện tử (VNPAY)</option>
            </select>
        </div>

        <div>
            <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                <i class="fa fa-filter"></i> Lọc đơn
            </button>

            <?php if (!empty($keyword) || $status !== '' || !empty($payment)): ?>
                <a href="index.php?module=admin&controller=order&action=index" 
                   style="padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; margin-left: 5px;">
                   Xóa lọc
                </a>
            <?php endif; ?>
        </div>

    </form>
</div>
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th> <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $row): ?>
                    <tr>
                        <td><strong><?= $row['order_code'] ?></strong></td>
                        <td>
                            <?= htmlspecialchars($row['fullname']) ?><br>
                            <small style="color:#666"><?= $row['phone'] ?></small>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td style="color:#d32f2f; font-weight:bold;">
                            <?= number_format($row['total_money'], 0, ',', '.') ?>₫
                        </td>
                        
                        <td>
                            <?php if ($row['payment_method'] == 'VNPAY'): ?>
                                <span style="color: #6610f2; font-weight: bold;">💳 VNPAY</span>
                            <?php else: ?>
                                <span style="color: #333;">💵 COD</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php
                                $s = $row['status'];
                                // Logic hiển thị trạng thái
                                $label = '';
                                $class = '';
                                
                                switch($s) {
                                    case 1: 
                                        $label = 'Chờ xác nhận'; 
                                        $class = 'st-1'; 
                                        break;
                                    case 2: 
                                        // Nếu là VNPAY mà status=2 thì là Đã thanh toán
                                        $label = ($row['payment_method'] == 'VNPAY') ? 'Đã thanh toán' : 'Đã xác nhận'; 
                                        $class = 'st-2'; 
                                        break;
                                    case 3: 
                                        $label = 'Đang giao'; 
                                        $class = 'st-3'; 
                                        break;
                                    case 4: 
                                        $label = 'Hoàn thành'; 
                                        $class = 'st-4'; 
                                        break;
                                    case 5: 
                                        $label = 'Đã hủy'; 
                                        $class = 'st-5'; 
                                        break;
                                    default: 
                                        $label = 'Không rõ';
                                }
                            ?>
                            <span class="badge <?= $class ?>"><?= $label ?></span>
                        </td>
                        <td>
                            <a href="index.php?module=admin&controller=order&action=detail&id=<?= $row['id'] ?>" class="btn-view">
                                Xem ➝
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>