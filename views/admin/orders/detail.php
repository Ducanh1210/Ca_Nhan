<?php
/**
 * FILE: views/admin/orders/detail.php
 * CHỨC NĂNG: Trang chi tiết đơn hàng cho admin - hiển thị thông tin chi tiết của một đơn hàng cụ thể
 * LUỒNG CHẠY: 
 * - VÀO: Nhận dữ liệu đơn hàng và items từ OrderController->show() 
 * - RA: Hiển thị thông tin chi tiết đơn hàng, thông tin khách hàng và danh sách sản phẩm
 * - NGƯỢC LẠI: Admin có thể quay lại danh sách đơn hàng hoặc debug thông tin
 */

// THIẾT LẬP THÔNG TIN TRANG
$pageTitle = 'Chi tiết đơn hàng';       // Tiêu đề trang
$currentPage = 'orders';                 // Trang hiện tại để highlight menu

// BẮT ĐẦU BUFFER OUTPUT ĐỂ TRUYỀN VÀO LAYOUT
ob_start();

// HÀM TIỆN ÍCH HTML ESCAPE - BẢO MẬT CHỐNG XSS
function h($v)
{
    return htmlspecialchars((string) ($v ?? ''));
}
?>

<div class="content mt-4">
    <!-- HIỂN THỊ FLASH MESSAGE - THÔNG BÁO TỪ SESSION -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show mb-3">
            <i
                class="bi bi-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle' : ($_SESSION['flash']['type'] === 'danger' ? 'exclamation-triangle' : 'info-circle') ?>"></i>
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- HEADER CỦA TRANG - TIÊU ĐỀ VÀ NÚT QUAY LẠI -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Đơn hàng #<?= h($order['id']) ?></h4>
        <a href="?act=orders" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>

    <!-- DEBUG: HIỂN THỊ DỮ LIỆU RAW (CHỈ HIỂN THỊ KHI CẦN DEBUG) -->
    <?php if (isset($_GET['debug']) && $_SESSION['admin']): ?>
        <div class="alert alert-info mb-3">
            <h6>Debug Info:</h6>
            <pre><?php print_r($order); ?></pre>
            <h6>Items:</h6>
            <pre><?php print_r($items); ?></pre>
        </div>
    <?php endif; ?>

    <!-- ROW THÔNG TIN CHÍNH -->
    <div class="row g-3">
        <!-- CỘT 1: THÔNG TIN KHÁCH HÀNG -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header fw-semibold">Thông tin khách hàng</div>
                <div class="card-body">
                    <!-- CÁC THÔNG TIN CÁ NHÂN CỦA KHÁCH HÀNG -->
                    <div class="mb-2"><span class="text-muted">Tên:</span> <?= h($order['user_name'] ?? 'N/A') ?></div>
                    <div class="mb-2"><span class="text-muted">Email:</span> <?= h($order['user_email'] ?? 'N/A') ?>
                    </div>
                    <div class="mb-2"><span class="text-muted">SĐT:</span> <?= h($order['phone'] ?? 'N/A') ?></div>
                    <div class="mb-2"><span class="text-muted">Địa chỉ:</span>
                        <?= h($order['shipping_address'] ?? 'N/A') ?></div>
                    <div class="mb-2"><span class="text-muted">Ghi chú:</span> <?= h($order['note'] ?? 'Không có') ?>
                    </div>
                    <!-- TRẠNG THÁI VÀ NGÀY TẠO ĐƠN HÀNG -->
                    <div class="mb-2"><span class="text-muted">Trạng thái:</span> <span
                            class="badge bg-info"><?= h($order['status'] ?? 'pending') ?></span></div>
                    <div class="mb-2"><span class="text-muted">Ngày tạo:</span> <?= h($order['created_at'] ?? 'N/A') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- CỘT 2: TỔNG QUAN THANH TOÁN -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header fw-semibold">Tổng quan thanh toán</div>
                <div class="card-body">
                    <!-- THÔNG TIN TÀI CHÍNH CỦA ĐƠN HÀNG -->
                    <div class="mb-2"><span class="text-muted">Tổng tiền:</span>
                        <?= isset($order['total']) ? number_format((float) $order['total']) . ' đ' : 'N/A' ?></div>
                    <?php if (!empty($order['voucher_id'])): ?>
                        <div class="mb-2"><span class="text-muted">Voucher áp dụng:</span> #<?= h($order['voucher_id']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- CARD DANH SÁCH SẢN PHẨM TRONG ĐƠN HÀNG -->
    <div class="card mt-4">
        <div class="card-header fw-semibold">Sản phẩm trong đơn</div>
        <div class="card-body">
            <?php if (empty($items)): ?>
                <!-- THÔNG BÁO CẢNH BÁO KHI KHÔNG CÓ SẢN PHẨM -->
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Chú ý:</strong> Đơn hàng này không có sản phẩm nào hoặc có vấn đề với dữ liệu.
                    <br><small>Vui lòng kiểm tra lại cơ sở dữ liệu.</small>
                </div>
            <?php else: ?>
                <!-- BẢNG HIỂN THỊ DANH SÁCH SẢN PHẨM -->
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <!-- HEADER CỦA BẢNG SẢN PHẨM -->
                        <thead class="table-light">
                            <tr>
                                <th style="width:80px">#</th> <!-- STT -->
                                <th>Sản phẩm</th> <!-- Tên sản phẩm -->
                                <th style="width:80px" class="text-end">SL</th> <!-- Số lượng -->
                                <th style="width:140px" class="text-end">Giá</th> <!-- Giá sản phẩm -->
                                <th style="width:160px">Ảnh</th> <!-- Ảnh sản phẩm -->
                            </tr>
                        </thead>
                        <!-- BODY CỦA BẢNG - DỮ LIỆU SẢN PHẨM -->
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($items as $it): ?>
                                <tr>
                                    <!-- CỘT STT -->
                                    <td class="text-center"><?= $i++ ?></td>
                                    <!-- CỘT TÊN SẢN PHẨM -->
                                    <td><?= h($it['product_name'] ?? ('#' . $it['product_id'])) ?></td>
                                    <!-- CỘT SỐ LƯỢNG -->
                                    <td class="text-end"><?= h($it['quantity']) ?></td>
                                    <!-- CỘT GIÁ SẢN PHẨM -->
                                    <td class="text-end"><?= number_format((float) ($it['price'] ?? 0)) ?> đ</td>
                                    <!-- CỘT ẢNH SẢN PHẨM -->
                                    <td>
                                        <?php if (!empty($it['product_img'])): ?>
                                            <img src="<?= h($it['product_img']) ?>" alt="<?= h($it['product_name']) ?>"
                                                style="height:60px;width:60px;object-fit:cover;border-radius:6px;">
                                        <?php else: ?>
                                            <span class="text-muted">Không có ảnh</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// LẤY NỘI DUNG BUFFER VÀ TRUYỀN VÀO LAYOUT CHUNG
$mainContent = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>