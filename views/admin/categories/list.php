<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ?act=login');
    exit;
}

$pageTitle = 'Quản lý Danh mục';
$currentPage = 'categories';

// Nội dung chính
ob_start();
?>

<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800">
                <i class="bi bi-tags text-primary me-2"></i>
                Quản lý Danh mục
            </h2>
            <p class="text-muted mb-0">Quản lý các danh mục sản phẩm trong hệ thống</p>
        </div>
        <a href="?act=category-add" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Thêm danh mục mới
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0 text-gray-800">
                        <i class="bi bi-list-ul me-2"></i>
                        Danh sách danh mục
                    </h5>
                </div>
                <div class="col-auto">
                    <span class="badge bg-primary fs-6"><?= count($categories ?? []) ?> danh mục</span>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <?php if (!empty($categories)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0" style="width: 80px">
                                    <i class="bi bi-hash text-muted"></i> ID
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-tag text-muted me-2"></i>Tên danh mục
                                </th>
                                <th class="border-0" style="width: 200px" class="text-center">
                                    <i class="bi bi-gear text-muted me-2"></i>Hành động
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $c): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary fs-6">#<?= (int)$c['id'] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="bi bi-tag text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-gray-800"><?= htmlspecialchars($c['name'] ?? '') ?></h6>
                                                <small class="text-muted">Danh mục sản phẩm</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="?act=category-edit&id=<?= (int)$c['id'] ?>" 
                                               class="btn btn-outline-warning btn-sm" 
                                               data-bs-toggle="tooltip" 
                                               title="Chỉnh sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?act=category-delete&id=<?= (int)$c['id'] ?>" 
                                               class="btn btn-outline-danger btn-sm" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')"
                                               data-bs-toggle="tooltip" 
                                               title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-tags text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-muted">Chưa có danh mục nào</h5>
                    <p class="text-muted mb-4">Bắt đầu bằng cách tạo danh mục đầu tiên</p>
                    <a href="?act=category-add" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Tạo danh mục đầu tiên
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$mainContent = ob_get_clean();

// Sử dụng layout admin
require_once './views/admin/layout/layout.php';
?>


