<?php
$pageTitle = 'Quản lý Voucher';
$currentPage = 'vouchers';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-ticket-perforated"></i> Quản lý Voucher</h2>
    <a href="?act=voucher-add" class="btn btn-success"><i class="bi bi-plus-circle"></i> Thêm voucher</a>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr class="text-center">
                <th>ID</th>
                <th>Mã Voucher</th>
                <th>Giảm giá (%)</th>
                <th>Ngày hết hạn</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($vouchers)): ?>
                <?php foreach ($vouchers as $voucher): ?>
                    <tr>
                        <td class="text-center"><?= $voucher['id'] ?></td>
                        <td><?= htmlspecialchars($voucher['code']) ?></td>
                        <td class="text-center"><?= isset($voucher['discount_percent']) ? $voucher['discount_percent'] : 0 ?>%
                        </td>
                        <td class="text-center"><?= $voucher['expiry_date'] ?></td>
                        <td class="text-center">
                            <?php
                            $statusClass = '';
                            $statusText = '';
                            switch($voucher['status'] ?? 'active') {
                                case 'active':
                                    $statusClass = 'badge bg-success';
                                    $statusText = 'Hoạt động';
                                    break;
                                case 'inactive':
                                    $statusClass = 'badge bg-secondary';
                                    $statusText = 'Không hoạt động';
                                    break;
                                case 'expired':
                                    $statusClass = 'badge bg-danger';
                                    $statusText = 'Hết hạn';
                                    break;
                                default:
                                    $statusClass = 'badge bg-success';
                                    $statusText = 'Hoạt động';
                            }
                            ?>
                            <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
                        <td class="text-center">
                            <a href="?act=voucher-edit&id=<?= $voucher['id'] ?>" class="btn btn-warning btn-sm"><i
                                    class="bi bi-pencil-square"></i></a>
                            <a href="?act=voucher-delete&id=<?= $voucher['id'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Xóa voucher này?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Chưa có voucher nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$mainContent = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>