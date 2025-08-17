<?php
/**
 * FILE: views/admin/orders/list.php
 * CHỨC NĂNG: Trang danh sách đơn hàng cho admin - hiển thị tất cả đơn hàng trong hệ thống
 * LUỒNG CHẠY: 
 * - VÀO: Nhận dữ liệu đơn hàng từ OrderController->list() 
 * - RA: Hiển thị bảng danh sách đơn hàng với các chức năng quản lý
 * - NGƯỢC LẠI: Admin có thể cập nhật trạng thái đơn hàng và xem chi tiết
 */

// THIẾT LẬP THÔNG TIN TRANG
$pageTitle = 'Quản lý đơn hàng';        // Tiêu đề trang
$currentPage = 'orders';                 // Trang hiện tại để highlight menu

// HÀM TIỆN ÍCH HTML ESCAPE - BẢO MẬT CHỐNG XSS
if (!function_exists('h')) {
    function h($val)
    {
        return htmlspecialchars((string) ($val ?? ''));
    }
}

// BẮT ĐẦU BUFFER OUTPUT ĐỂ TRUYỀN VÀO LAYOUT
ob_start();
?>

<div class="content mt-4">
    <!-- HEADER CỦA TRANG - TIÊU ĐỀ VÀ CÁC NÚT CHỨC NĂNG -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Danh sách đơn hàng</h3>
        <div>
            <!-- NÚT DEBUG - CHỈ DÀNH CHO ADMIN ĐỂ KIỂM TRA DATABASE -->
            <a href="?act=order-debug" class="btn btn-outline-secondary btn-sm me-2" title="Debug database">
                <i class="bi bi-bug"></i> Debug
            </a>
        </div>
    </div>
    <!-- BẢNG DANH SÁCH ĐƠN HÀNG -->
    <table class="table table-bordered table-hover align-middle">
        <!-- HEADER CỦA BẢNG -->
        <thead class="table-light">
            <tr>
                <th>ID</th> <!-- ID đơn hàng -->
                <th>Tên sản phẩm</th> <!-- Tên sản phẩm chính -->
                <th>Ảnh sản phẩm</th> <!-- Ảnh sản phẩm -->
                <th>Tên người dùng</th> <!-- Tên khách hàng -->
                <th>Email</th> <!-- Email khách hàng -->
                <th>Địa chỉ/Ghi chú thanh toán</th> <!-- Thông tin giao hàng -->
                <th>Trạng thái</th> <!-- Trạng thái đơn hàng -->
                <th>Hành động</th> <!-- Các nút thao tác -->
            </tr>
        </thead>
        <!-- BODY CỦA BẢNG - DỮ LIỆU ĐƠN HÀNG -->
        <tbody>
            <?php if (!empty($orders)): ?>
                <!-- LẶP QUA TỪNG ĐƠN HÀNG -->
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <!-- CỘT ID ĐƠN HÀNG -->
                        <td><?= h($order['id']) ?></td>

                        <!-- CỘT TÊN SẢN PHẨM -->
                        <td>
                            <?php if (!empty($order['product_name'])): ?>
                                <?= h($order['product_name']) ?>
                                <!-- HIỂN THỊ SỐ LƯỢNG SẢN PHẨM KHÁC NẾU CÓ -->
                                <?php if (($order['total_items'] ?? 0) > 1): ?>
                                    <br><small class="text-muted">+<?= ($order['total_items'] - 1) ?> sản phẩm khác</small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Không có sản phẩm</span>
                            <?php endif; ?>
                        </td>

                        <!-- CỘT ẢNH SẢN PHẨM -->
                        <td>
                            <?php if (!empty($order['product_img'])): ?>
                                <img src="<?= h($order['product_img']) ?>" alt="<?= h($order['product_name'] ?? 'Sản phẩm') ?>"
                                    style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                            <?php else: ?>
                                <span class="text-muted">Không có ảnh</span>
                            <?php endif; ?>
                        </td>

                        <!-- CỘT TÊN NGƯỜI DÙNG -->
                        <td><?= h($order['user_name'] ?? $order['user_id']) ?></td>

                        <!-- CỘT EMAIL -->
                        <td><?= h($order['user_email'] ?? '') ?></td>

                        <!-- CỘT ĐỊA CHỈ VÀ GHI CHÚ -->
                        <td>
                            <?php if (!empty($order['address'])): ?>
                                <span class="fw-bold text-success">Địa chỉ: <?= h($order['address']) ?></span><br>
                            <?php endif; ?>
                            <?php if (!empty($order['note'])): ?>
                                <span class="text-muted">Ghi chú: <?= h($order['note']) ?></span>
                            <?php endif; ?>
                        </td>

                        <!-- CỘT TRẠNG THÁI - FORM CẬP NHẬT TRẠNG THÁI -->
                        <td>
                            <form method="POST" action="?act=order-update-status&id=<?= h($order['id']) ?>" class="d-flex">
                                <select name="status" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                    <!-- CÁC OPTION TRẠNG THÁI ĐƠN HÀNG -->
                                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Đang xử
                                        lý</option>
                                    <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Đã xử lý
                                    </option>
                                    <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Đang giao
                                        hàng</option>
                                    <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Đã giao
                                    </option>
                                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy
                                    </option>
                                </select>
                            </form>
                        </td>

                        <!-- CỘT HÀNH ĐỘNG - NÚT XEM CHI TIẾT -->
                        <td class="text-nowrap">
                            <a href="?act=order-show&id=<?= h($order['id']) ?>" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Xem chi tiết
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- HIỂN THỊ THÔNG BÁO KHI KHÔNG CÓ ĐƠN HÀNG -->
                <tr>
                    <td colspan="8" class="text-center text-muted">Chưa có đơn hàng nào!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$mainContent = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>