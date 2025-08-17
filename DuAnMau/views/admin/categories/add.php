<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ?act=login');
    exit;
}

$pageTitle = 'Thêm Danh mục mới';
$currentPage = 'categories';

// Nội dung chính
ob_start();
?>

<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800">
                <i class="bi bi-plus-circle text-success me-2"></i>
                Thêm Danh mục mới
            </h2>
            <p class="text-muted mb-0">Tạo danh mục sản phẩm mới cho hệ thống</p>
        </div>
        <a href="?act=category-list" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Quay lại danh sách
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-gray-800">
                        <i class="bi bi-tag me-2"></i>
                        Thông tin danh mục
                    </h5>
                </div>
                
                <form method="post" class="card-body">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-tag text-primary me-2"></i>
                            Tên danh mục
                        </label>
                        <input type="text" 
                               name="name" 
                               class="form-control form-control-lg" 
                               placeholder="VD: Trà sữa, Cà phê, Bánh ngọt..."
                               required
                               autofocus>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Tên danh mục sẽ hiển thị cho khách hàng
                        </div>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <div><?= htmlspecialchars($error) ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex gap-3 pt-3">
                        <button class="btn btn-success btn-lg px-4" type="submit">
                            <i class="bi bi-check-circle me-2"></i>
                            Lưu danh mục
                        </button>
                        <a href="?act=category-list" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-x-circle me-2"></i>
                            Hủy bỏ
                        </a>
                    </div>
                </form>
            </div>

            <!-- Hướng dẫn -->
            <div class="card mt-4 border-info">
                <div class="card-header bg-info bg-opacity-10 border-info">
                    <h6 class="mb-0 text-info">
                        <i class="bi bi-lightbulb me-2"></i>
                        Mẹo tạo danh mục hiệu quả
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Đặt tên ngắn gọn, dễ hiểu
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Sử dụng từ khóa phổ biến
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Tránh tên quá dài hoặc khó hiểu
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$mainContent = ob_get_clean();

// Sử dụng layout admin
require_once './views/admin/layout/layout.php';
?>