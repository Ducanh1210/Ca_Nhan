<?php
if (!isset($_SESSION))
    session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ?act=login');
    exit;
}

$pageTitle = 'Dashboard - Admin Panel';
$currentPage = 'dashboard';

// Nội dung chính
ob_start();
?>

<div class="content-wrapper">
    <!-- Page Header -->
    <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2 text-gray-800">
                <i class="bi bi-speedometer2 text-primary me-2"></i>
                Dashboard
            </h1>
            <p class="text-muted mb-0">Tổng quan hệ thống và thống kê</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="#" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-download me-1"></i> Xuất báo cáo
                </a>
                <a href="#" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-share me-1"></i> Chia sẻ
                </a>
            </div>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="alert alert-info border-0" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-2 fs-4"></i>
            <div>
                <h6 class="alert-heading mb-1">Chào mừng bạn đến với trang quản trị!</h6>
                <p class="mb-0">Chọn một mục bên thanh menu trái để bắt đầu quản lý hệ thống.</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <?php
        // Sử dụng các widget từ dashboard-widgets.php
        require_once './views/admin/dashboard-widgets.php';
        renderOverviewWidgets($categories ?? [], $products ?? [], $users ?? [], $orders ?? []);
        ?>
    </div>

    <!-- Category Management Section -->
    <div class="row mb-4">
        <?php
        renderCategoryProductWidget($categories ?? [], $products ?? []);
        renderCategoryQuickActions();
        ?>
    </div>

    <!-- Category Detail Section -->
    <div class="row mb-4">
        <?php
        renderCategoryDetailWidget($categories ?? [], $products ?? []);
        ?>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-lightning me-2"></i>
                        Thao tác nhanh
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="?act=p-add"
                                class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="bi bi-plus-circle fs-1 mb-2"></i>
                                <span>Thêm sản phẩm mới</span>
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="?act=p-list"
                                class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="bi bi-list-ul fs-1 mb-2"></i>
                                <span>Quản lý sản phẩm</span>
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="?act=users"
                                class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="bi bi-people fs-1 mb-2"></i>
                                <span>Quản lý người dùng</span>
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="?act=orders"
                                class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="bi bi-receipt fs-1 mb-2"></i>
                                <span>Xem đơn hàng</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-bell me-2"></i>
                        Thông báo gần đây
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-pill"><?= (int) ($orders ?? 0) ?></span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Đơn hàng mới</h6>
                                <small class="text-muted">Có <?= (int) ($orders ?? 0) ?> đơn hàng trong hệ thống</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="flex-shrink-0">
                                <span class="badge bg-info rounded-pill"><?= (int) ($products ?? 0) ?></span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Sản phẩm</h6>
                                <small class="text-muted"><?= (int) ($products ?? 0) ?> sản phẩm trong hệ thống</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="flex-shrink-0">
                                <span class="badge bg-warning rounded-pill"><?= (int) ($users ?? 0) ?></span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Người dùng</h6>
                                <small class="text-muted"><?= (int) ($users ?? 0) ?> người dùng đã đăng ký</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-clock-history me-2"></i>
                        Hoạt động gần đây
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php if (!empty($recentProducts ?? [])): ?>
                            <?php foreach (array_slice($recentProducts, 0, 3) as $p): ?>
                                <div class="timeline-item d-flex mb-3">
                                    <div class="timeline-marker bg-success rounded-circle me-3"
                                        style="width: 12px; height: 12px;"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Sản phẩm mới được thêm</h6>
                                        <p class="text-muted mb-0"><?= htmlspecialchars($p['name'] ?? 'Unknown') ?> đã được thêm
                                        </p>
                                        <small
                                            class="text-muted"><?= isset($p['created_at']) ? date('d/m/Y H:i', strtotime($p['created_at'])) : 'Gần đây' ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($recentOrders ?? [])): ?>
                            <?php foreach (array_slice($recentOrders, 0, 3) as $o): ?>
                                <div class="timeline-item d-flex mb-3">
                                    <div class="timeline-marker bg-info rounded-circle me-3" style="width: 12px; height: 12px;">
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Đơn hàng mới</h6>
                                        <p class="text-muted mb-0">Đơn hàng #<?= (int) ($o['id'] ?? 0) ?> đã được đặt</p>
                                        <small
                                            class="text-muted"><?= isset($o['created_at']) ? date('d/m/Y H:i', strtotime($o['created_at'])) : 'Gần đây' ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (empty($recentProducts ?? []) && empty($recentOrders ?? [])): ?>
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-info-circle fs-1"></i>
                                <p class="mb-0">Chưa có hoạt động nào gần đây</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-graph-up me-2"></i>
                        Thống kê nhanh
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-end">
                                <h4 class="text-success">+15%</h4>
                                <small class="text-muted">Doanh thu tháng này</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-info">+8%</h4>
                            <small class="text-muted">Đơn hàng tăng</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">+12%</h4>
                            <small class="text-muted">Người dùng mới</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-danger">-3%</h4>
                            <small class="text-muted">Sản phẩm tồn kho</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$mainContent = ob_get_clean();

// Extra CSS dành riêng cho trang Dashboard
$extraCSS = '
<link rel="stylesheet" href="./views/admin/dashboard-styles.css">
<style>
    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }
    .border-left-danger { border-left: 4px solid #e74a3b !important; }
    .text-gray-800 { color: #5a5c69 !important; }
    .timeline-marker { margin-top: 8px; }
    .btn.w-100.h-100 { transition: all 0.3s ease; }
    .btn.w-100.h-100:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
</style>';

// Sử dụng layout admin
require_once './views/admin/layout/layout.php';
?>