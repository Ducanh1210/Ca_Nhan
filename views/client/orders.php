<?php
/**
 * FILE: views/client/orders.php
 * CHỨC NĂNG: Hiển thị trang lịch sử đơn hàng của khách hàng
 * LUỒNG CHẠY: 
 * - VÀO: Từ controller OrderController hoặc UserController khi user đăng nhập và truy cập trang orders
 * - RA: Hiển thị danh sách đơn hàng với các thông tin chi tiết và chức năng hủy đơn
 * - NGƯỢC LẠI: User có thể hủy đơn hàng (pending) hoặc quay về trang chủ để tiếp tục mua sắm
 */
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi</title>
    <!-- LIÊN KẾT CSS BOOTSTRAP VÀ ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* CSS TÙY CHỈNH CHO TRANG ORDERS */
        .order-status {
            font-size: 0.9rem;
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-block;
            font-weight: 500;
        }

        .cancel-btn {
            margin-top: 6px;
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .order-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .order-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }

        .order-body {
            padding: 20px;
        }

        .price-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }

        .product-summary {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <!-- HEADER CỦA TRANG - HIỂN THỊ TIÊU ĐỀ VÀ NÚT QUAY LẠI -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-success mb-0">
                <i class="bi bi-receipt"></i> Lịch sử đơn hàng của tôi
            </h3>
            <!-- NÚT QUAY VỀ TRANG CHỦ - LUỒNG RA: ?act=home-client -->
            <a href="?act=home-client" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
            </a>
        </div>

        <!-- HIỂN THỊ THÔNG BÁO THÀNH CÔNG KHI ĐẶT HÀNG - TỪ SESSION -->
        <?php if (isset($_SESSION['order_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_SESSION['order_success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['order_success']); ?>
        <?php endif; ?>

        <!-- HIỂN THỊ THÔNG BÁO THÀNH CÔNG KHI HỦY ĐƠN HÀNG - TỪ SESSION -->
        <?php if (isset($_SESSION['cancel_success'])): ?>
            <div class="alert alert-info alert-dismissible fade show">
                <i class="bi bi-info-circle"></i> <?= htmlspecialchars($_SESSION['cancel_success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['cancel_success']); ?>
        <?php endif; ?>

        <!-- HIỂN THỊ THÔNG BÁO LỖI KHI HỦY ĐƠN HÀNG THẤT BẠI - TỪ SESSION -->
        <?php if (isset($_SESSION['cancel_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['cancel_error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['cancel_error']); ?>
        <?php endif; ?>

        <!-- PHẦN CHÍNH: HIỂN THỊ DANH SÁCH ĐƠN HÀNG -->
        <?php if (!empty($orders)): ?>
            <!-- LẶP QUA TỪNG ĐƠN HÀNG TRONG MẢNG $orders -->
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <!-- HEADER CỦA ĐƠN HÀNG - HIỂN THỊ ID, NGÀY TẠO VÀ TRẠNG THÁI -->
                    <div class="order-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <!-- HIỂN THỊ ID ĐƠN HÀNG VÀ NGÀY TẠO -->
                                <h6 class="mb-0">
                                    <i class="bi bi-hash"></i> Đơn hàng #<?= htmlspecialchars($order['id']) ?>
                                </h6>
                                <small class="opacity-75">
                                    <i class="bi bi-calendar3"></i>
                                    <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                </small>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <!-- HIỂN THỊ TRẠNG THÁI ĐƠN HÀNG VỚI MÀU SẮC VÀ ICON TƯƠNG ỨNG -->
                                <?php
                                $status = $order['status'] ?? 'pending';
                                switch ($status) {
                                    case 'pending':
                                        echo '<span class="order-status bg-warning text-dark"><i class="bi bi-clock"></i> Đang xử lý</span>';
                                        break;
                                    case 'confirmed':
                                        echo '<span class="order-status bg-primary text-white"><i class="bi bi-check2"></i> Đã xử lý</span>';
                                        break;
                                    case 'shipped':
                                        echo '<span class="order-status bg-info text-dark"><i class="bi bi-truck"></i> Đang giao hàng</span>';
                                        break;
                                    case 'delivered':
                                        echo '<span class="order-status bg-success text-white"><i class="bi bi-box-seam"></i> Đã giao</span>';
                                        break;
                                    case 'cancelled':
                                        echo '<span class="order-status bg-danger text-white"><i class="bi bi-x-circle"></i> Đã hủy</span>';
                                        break;
                                    default:
                                        echo '<span class="order-status bg-secondary text-white"><i class="bi bi-question"></i> Không rõ</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- BODY CỦA ĐƠN HÀNG - HIỂN THỊ CHI TIẾT SẢN PHẨM VÀ THÔNG TIN GIAO HÀNG -->
                    <div class="order-body">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- PHẦN TÓM TẮT SẢN PHẨM ĐÃ ĐẶT -->
                                <div class="product-summary">
                                    <h6 class="mb-3">
                                        <i class="bi bi-box"></i> Sản phẩm đã đặt
                                    </h6>
                                    <p class="mb-0">
                                        <strong><?= htmlspecialchars($order['products_summary'] ?? 'Không có thông tin sản phẩm') ?></strong>
                                    </p>
                                    <small class="text-muted">
                                        Tổng cộng: <?= $order['total_items'] ?? 0 ?> sản phẩm
                                    </small>
                                </div>

                                <!-- PHẦN THÔNG TIN GIAO HÀNG -->
                                <div class="mt-3">
                                    <h6 class="mb-2">
                                        <i class="bi bi-geo-alt"></i> Thông tin giao hàng
                                    </h6>
                                    <p class="mb-1">
                                        <strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address'] ?? 'Không có') ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Điện thoại:</strong> <?= htmlspecialchars($order['phone'] ?? 'Không có') ?>
                                    </p>
                                    <?php if (!empty($order['note'])): ?>
                                        <p class="mb-0">
                                            <strong>Ghi chú:</strong> <?= htmlspecialchars($order['note']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- PHẦN THÔNG TIN THANH TOÁN VÀ GIÁ TIỀN -->
                                <div class="price-info">
                                    <h6 class="mb-3">
                                        <i class="bi bi-calculator"></i> Thông tin thanh toán
                                    </h6>

                                    <!-- HIỂN THỊ TẠM TÍNH -->
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tạm tính:</span>
                                        <span class="fw-bold"><?= number_format($order['total_price'] ?? 0) ?>đ</span>
                                    </div>

                                    <!-- HIỂN THỊ GIẢM GIÁ NẾU CÓ -->
                                    <?php if (($order['discount_percent'] ?? 0) > 0): ?>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Giảm giá:</span>
                                            <span class="fw-bold text-danger">
                                                -<?= number_format(($order['total_price'] ?? 0) * ($order['discount_percent'] ?? 0) / 100) ?>đ
                                                (<?= $order['discount_percent'] ?>%)
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <hr>
                                    <!-- HIỂN THỊ TỔNG TIỀN CUỐI CÙNG -->
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Tổng tiền:</span>
                                        <span
                                            class="fw-bold text-success fs-6"><?= number_format($order['total'] ?? 0) ?>đ</span>
                                    </div>
                                </div>

                                <!-- NÚT HỦY ĐƠN HÀNG - CHỈ HIỂN THỊ KHI ĐƠN HÀNG ĐANG Ở TRẠNG THÁI PENDING -->
                                <?php if (($order['status'] ?? '') === 'pending'): ?>
                                    <div class="text-center mt-3">
                                        <!-- LUỒNG RA: GỬI REQUEST HỦY ĐƠN HÀNG VỚI ID ĐƠN HÀNG -->
                                        <a href="?act=cancel-order&id=<?= $order['id'] ?>" class="btn btn-outline-danger cancel-btn"
                                            onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                            <i class="bi bi-x-circle"></i> Hủy đơn hàng
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- HIỂN THỊ THÔNG BÁO KHI KHÔNG CÓ ĐƠN HÀNG NÀO -->
            <div class="text-center py-5">
                <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Bạn chưa có đơn hàng nào!</h5>
                <p class="text-muted">Hãy bắt đầu mua sắm để tạo đơn hàng đầu tiên.</p>
                <!-- LUỒNG RA: CHUYỂN ĐẾN TRANG SẢN PHẨM ĐỂ MUA SẮM -->
                <a href="?act=sp-clients" class="btn btn-primary">
                    <i class="bi bi-shop"></i> Mua sắm ngay
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- LIÊN KẾT JAVASCRIPT BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>