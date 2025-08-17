<?php
$pageTitle = 'Quản lý sản phẩm';
$currentPage = 'products';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-box"></i> Danh sách sản phẩm</h4>
    <a href="<?= BASE_URL ?>?act=p-add" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Thêm sản phẩm
    </a>
</div>

<div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
        <thead class="table-light">
            <tr class="text-center">
                <th>ID</th>
                <th>Tên</th>
                <th>Giá</th>
                <th>Hình ảnh</th>
                <th>Mô tả</th>
                <th>Danh mục</th>
                <th>Biến thể</th>
                <th class="text-center" style="width:220px">Thêm biến thể</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td class="text-center"><?= $product['id'] ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td class="text-end"><?= number_format($product['price']) ?> đ</td>
                    <td class="text-center">
                        <img src="<?= $product['img'] ?>" alt="<?= $product['name'] ?>"
                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                    </td>
                    <td><?= mb_substr($product['description'], 0, 60) ?>...</td>
                    <td class="text-center"><?= $product['category_name'] ?? 'Chưa có' ?></td>

                    <!-- Biến thể tên + giá -->
                    <td class="text-start">
                        <?php if (!empty($product['variants'])): ?>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($product['variants'] as $v): ?>
                                    <li class="d-flex align-items-center justify-content-between">
                                        <span><?= htmlspecialchars($v['name']) ?> - <?= number_format($v['price']) ?> đ</span>
                                        <a href="?act=variant-delete&id=<?= $v['id'] ?>&product_id=<?= $product['id'] ?>"
                                            class="btn btn-sm btn-outline-danger ms-2"
                                            onclick="return confirm('Xóa biến thể này?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <!-- Thêm biến thể (Modal trigger) -->
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addVariantModal<?= $product['id'] ?>">
                            <i class="bi bi-plus-circle"></i> Thêm biến thể
                        </button>

                        <!-- Modal thêm biến thể -->
                        <div class="modal fade" id="addVariantModal<?= $product['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form class="modal-content" method="post"
                                    action="?act=variant-add&product_id=<?= $product['id'] ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Thêm biến thể cho:
                                            <?= htmlspecialchars($product['name']) ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="variant-form-list">
                                            <div class="row g-2 mb-2 align-items-center">
                                                <div class="col-7">
                                                    <input type="text" name="variant_name[]" class="form-control"
                                                        placeholder="Tên biến thể" required />
                                                </div>
                                                <div class="col-5">
                                                    <input type="number" name="variant_price[]" class="form-control"
                                                        placeholder="Giá" required />
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            onclick="addRow<?= $product['id'] ?>()"><i class="bi bi-plus"></i> Thêm
                                            dòng</button>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                                        <button type="submit" class="btn btn-success">Lưu</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <script>
                            function addRow<?= $product['id'] ?>() {
                                const list = document.querySelector('#addVariantModal<?= $product['id'] ?> #variant-form-list');
                                const row = document.createElement('div');
                                row.className = 'row g-2 mb-2 align-items-center';
                                row.innerHTML = `
                                                        <div class="col-7">
                                                            <input type="text" name="variant_name[]" class="form-control" placeholder="Tên biến thể" required />
                                                        </div>
                                                        <div class="col-5">
                                                            <div class="d-flex gap-2">
                                                                <input type="number" name="variant_price[]" class="form-control" placeholder="Giá" required />
                                                                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.row').remove()"><i class="bi bi-trash"></i></button>
                                                            </div>
                                                        </div>
                                                    `;
                                list.appendChild(row);
                            }
                        </script>
                    </td>

                    <!-- Hành động -->
                    <td class="text-center">
                        <a href="<?= BASE_URL ?>?act=p-edit&id=<?= $product['id'] ?>" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="<?= BASE_URL ?>?act=p-delete&id=<?= $product['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Bạn có chắc chắn muốn xoá sản phẩm này?')">
                            <i class="bi bi-trash"></i>
                        </a>
                        <a href="<?= BASE_URL ?>?act=p-show&id=<?= $product['id'] ?>" class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>
<?php
$mainContent = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>