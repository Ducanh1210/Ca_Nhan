<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ?act=login');
    exit;
}

$pageTitle = 'Chỉnh sửa Danh mục';
$currentPage = 'categories';

// Nội dung chính
ob_start();
?>

<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800">
                <i class="bi bi-pencil-square text-warning me-2"></i>
                Chỉnh sửa Danh mục
            </h2>
            <p class="text-muted mb-0">Cập nhật thông tin danh mục sản phẩm</p>
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
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="bi bi-tag text-warning"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 text-gray-800">
                                Chỉnh sửa: <?= htmlspecialchars($category['name'] ?? '') ?>
                            </h5>
                            <small class="text-muted">ID: #<?= (int)($category['id'] ?? 0) ?></small>
                        </div>
                    </div>
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
                               value="<?= htmlspecialchars($category['name'] ?? '') ?>" 
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
                        <button class="btn btn-warning btn-lg px-4" type="submit">
                            <i class="bi bi-save me-2"></i>
                            Lưu thay đổi
                        </button>
                        <a href="?act=category-list" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-x-circle me-2"></i>
                            Hủy bỏ
                        </a>
                    </div>
                </form>
            </div>

            <!-- Thông tin bổ sung -->
            <div class="card mt-4 border-warning">
                <div class="card-header bg-warning bg-opacity-10 border-warning">
                    <h6 class="mb-0 text-warning">
                        <i class="bi bi-info-circle me-2"></i>
                        Thông tin bổ sung
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar text-muted me-2"></i>
                                <span class="text-muted">Ngày tạo:</span>
                            </div>
                            <small class="text-muted">Chưa có thông tin</small>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-box text-muted me-2"></i>
                                <span class="text-muted">Sản phẩm:</span>
                            </div>
                            <small class="text-muted">Chưa có thông tin</small>
                        </div>
                    </div>
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