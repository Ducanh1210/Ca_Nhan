<?php
$pageTitle = 'Chi tiết sản phẩm';
$currentPage = 'products';
ob_start();
?>

<div class="content mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">
            <i class="bi bi-eye"></i> Chi tiết sản phẩm
        </h3>
        <div>
            <a href="?act=p-edit&id=<?= $product['id'] ?>" class="btn btn-warning me-2">
                <i class="bi bi-pencil-square"></i> Chỉnh sửa
            </a>
            <a href="?act=p-list" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin cơ bản -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Thông tin cơ bản
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">ID sản phẩm:</label>
                                <p class="mb-0">#<?= $product['id'] ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Tên sản phẩm:</label>
                                <p class="mb-0"><?= htmlspecialchars($product['name']) ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Giá gốc:</label>
                                <p class="mb-0 text-success fw-bold fs-5"><?= number_format($product['price']) ?> đ</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">Danh mục:</label>
                                <p class="mb-0">
                                    <?php if ($category): ?>
                                        <span class="badge bg-primary"><?= htmlspecialchars($category['name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa phân loại</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Ngày tạo:</label>
                                <p class="mb-0"><?= date('d/m/Y H:i', strtotime($product['created_at'] ?? 'now')) ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Trạng thái:</label>
                                <p class="mb-0">
                                    <span class="badge bg-success">Đang bán</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Mô tả:</label>
                        <div class="border rounded p-3 bg-light">
                            <?= nl2br(htmlspecialchars($product['description'] ?? 'Không có mô tả')) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hình ảnh sản phẩm -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-image"></i> Hình ảnh
                    </h5>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($product['img'])): ?>
                        <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                            class="img-fluid rounded" style="max-width: 100%; height: auto;">
                        <div class="mt-2">
                            <small class="text-muted">Đường dẫn: <?= htmlspecialchars($product['img']) ?></small>
                        </div>
                    <?php else: ?>
                        <div class="text-muted py-4">
                            <i class="bi bi-image" style="font-size: 3rem;"></i>
                            <p class="mb-0">Không có hình ảnh</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Biến thể sản phẩm -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-tags"></i> Biến thể sản phẩm
                    </h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVariantModal">
                        <i class="bi bi-plus-circle"></i> Thêm biến thể
                    </button>
                </div>
                <div class="card-body">
                    <?php if (!empty($variants)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên biến thể</th>
                                        <th>Giá</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($variants as $variant): ?>
                                        <tr>
                                            <td><?= $variant['id'] ?></td>
                                            <td><?= htmlspecialchars($variant['name']) ?></td>
                                            <td class="text-success fw-bold"><?= number_format($variant['price']) ?> đ</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteVariant(<?= $variant['id'] ?>, '<?= htmlspecialchars($variant['name']) ?>')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-tags" style="font-size: 2rem;"></i>
                            <p class="mb-0">Chưa có biến thể nào</p>
                            <small>Nhấn "Thêm biến thể" để tạo biến thể đầu tiên</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal thêm biến thể -->
<div class="modal fade" id="addVariantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="post" action="?act=variant-add&product_id=<?= $product['id'] ?>">
            <div class="modal-header">
                <h5 class="modal-title">Thêm biến thể cho: <?= htmlspecialchars($product['name']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="variant-form-list">
                    <div class="row g-2 mb-2 align-items-center">
                        <div class="col-7">
                            <input type="text" name="variant_name[]" class="form-control" placeholder="Tên biến thể"
                                required />
                        </div>
                        <div class="col-5">
                            <input type="number" name="variant_price[]" class="form-control" placeholder="Giá"
                                required />
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addVariantRow()">
                    <i class="bi bi-plus"></i> Thêm dòng
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-success">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
    function addVariantRow() {
        const list = document.querySelector('#addVariantModal #variant-form-list');
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 align-items-center';
        row.innerHTML = `
        <div class="col-7">
            <input type="text" name="variant_name[]" class="form-control" placeholder="Tên biến thể" required />
        </div>
        <div class="col-5">
            <div class="d-flex gap-2">
                <input type="number" name="variant_price[]" class="form-control" placeholder="Giá" required />
                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.row').remove()">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
        list.appendChild(row);
    }

    function deleteVariant(variantId, variantName) {
        if (confirm(`Bạn có chắc muốn xóa biến thể "${variantName}"?`)) {
            // Có thể thêm AJAX call để xóa biến thể ở đây
            alert('Chức năng xóa biến thể sẽ được thêm sau');
        }
    }
</script>

<?php
$mainContent = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>