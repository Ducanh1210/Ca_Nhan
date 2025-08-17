<?php
$pageTitle = 'Sửa mã giảm giá';
$currentPage = 'vouchers';
ob_start();
?>

<div class="container mt-4">
    <h3 class="mb-4 text-success">Sửa mã giảm giá</h3>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="w-50">
        <div class="mb-3">
            <label class="form-label">Mã voucher</label>
            <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($voucher['code'] ?? '') ?>"
                required>
        </div>
        <div class="mb-3">
            <label class="form-label">Giảm giá (%)</label>
            <input type="number" name="discount_percent" class="form-control" min="1" max="100"
                value="<?= htmlspecialchars($voucher['discount_percent'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Ngày hết hạn</label>
            <input type="date" name="expiry_date" class="form-control"
                value="<?= htmlspecialchars($voucher['expiry_date'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-control" required>
                <option value="active" <?= ($voucher['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                <option value="inactive" <?= ($voucher['status'] ?? 'active') == 'inactive' ? 'selected' : '' ?>>Không hoạt động</option>
                <option value="expired" <?= ($voucher['status'] ?? 'active') == 'expired' ? 'selected' : '' ?>>Hết hạn</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="?act=vouchers" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

<?php
$mainContent = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>