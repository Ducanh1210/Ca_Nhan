<?php
// Widget thống kê tổng quan
function renderOverviewWidgets($categories, $products, $users, $orders)
{
    $totalCategories = count($categories ?? []);
    $totalProducts = count($products ?? []);
    $totalUsers = count($users ?? []);
    $totalOrders = count($orders ?? []);
    ?>

    <!-- Tổng số danh mục -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Danh mục
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalCategories ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-tags fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tổng số sản phẩm -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Sản phẩm
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalProducts ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tổng số người dùng -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Người dùng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalUsers ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tổng số đơn hàng -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Đơn hàng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalOrders ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Widget thống kê sản phẩm theo danh mục với số lượng thực tế
function renderCategoryProductWidget($categories, $products)
{
    // Tính số lượng sản phẩm cho mỗi danh mục
    $categoryProductCounts = [];

    if (!empty($categories) && !empty($products)) {
        foreach ($categories as $category) {
            $categoryId = $category['id'];
            $count = 0;

            foreach ($products as $product) {
                if (isset($product['category_id']) && $product['category_id'] == $categoryId) {
                    $count++;
                }
            }

            $categoryProductCounts[$categoryId] = $count;
        }
    }

    ?>
    <div class="col-xl-6 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-pie-chart me-2"></i>
                    Phân bố sản phẩm theo danh mục
                </h6>
                <span class="badge bg-primary"><?= array_sum($categoryProductCounts) ?> sản phẩm</span>
            </div>
            <div class="card-body">
                <?php if (!empty($categories)): ?>
                    <div class="chart-pie pt-4 pb-2">
                        <div class="row">
                            <?php foreach (array_slice($categories, 0, 6) as $category): ?>
                                <?php
                                $productCount = $categoryProductCounts[$category['id']] ?? 0;
                                $percentage = array_sum($categoryProductCounts) > 0 ? round(($productCount / array_sum($categoryProductCounts)) * 100, 1) : 0;
                                ?>
                                <div class="col-md-6 mb-3">
                                    <div
                                        class="d-flex align-items-center p-3 rounded bg-light border-start border-4 border-primary category-card">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-tag text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold text-gray-800"><?= htmlspecialchars($category['name'] ?? '') ?>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-primary me-2 badge-category-count"><?= $productCount ?> sản
                                                    phẩm</span>
                                                <small class="text-muted">(<?= $percentage ?>%)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($categories) > 6): ?>
                            <div class="mt-4 text-center">
                                <small class="text-muted">
                                    Và <?= count($categories) - 6 ?> danh mục khác...
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-tags text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">Chưa có danh mục nào</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

// Widget hành động nhanh cho danh mục
function renderCategoryQuickActions()
{
    ?>
    <div class="col-xl-3 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-lightning me-2"></i>
                    Hành động nhanh
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="?act=category-add" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-2"></i>
                        Thêm danh mục mới
                    </a>
                    <a href="?act=category-list" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-list-ul me-2"></i>
                        Xem tất cả danh mục
                    </a>
                    <a href="?act=p-add" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-box me-2"></i>
                        Thêm sản phẩm mới
                    </a>
                    <a href="?act=p-list" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-list-ul me-2"></i>
                        Quản lý sản phẩm
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Widget chi tiết danh mục với số lượng sản phẩm
function renderCategoryDetailWidget($categories, $products)
{
    // Tính số lượng sản phẩm cho mỗi danh mục
    $categoryProductCounts = [];

    if (!empty($categories) && !empty($products)) {
        foreach ($categories as $category) {
            $categoryId = $category['id'];
            $count = 0;

            foreach ($products as $product) {
                if (isset($product['category_id']) && $product['category_id'] == $categoryId) {
                    $count++;
                }
            }

            $categoryProductCounts[$categoryId] = $count;
        }
    }

    ?>
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-bar-chart me-2"></i>
                    Chi tiết sản phẩm theo danh mục
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($categories)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Danh mục</th>
                                    <th class="text-center">Số lượng sản phẩm</th>
                                    <th class="text-center">Tỷ lệ</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <?php
                                    $productCount = $categoryProductCounts[$category['id']] ?? 0;
                                    $percentage = array_sum($categoryProductCounts) > 0 ? round(($productCount / array_sum($categoryProductCounts)) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="bi bi-tag text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?= htmlspecialchars($category['name'] ?? '') ?></h6>
                                                    <small class="text-muted">ID: #<?= $category['id'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary fs-6"><?= $productCount ?></span>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-primary" role="progressbar"
                                                    style="width: <?= $percentage ?>%" aria-valuenow="<?= $percentage ?>"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    <?= $percentage ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="?act=category-edit&id=<?= $category['id'] ?>"
                                                    class="btn btn-outline-warning btn-sm btn-action">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="?act=p-list&category=<?= $category['id'] ?>"
                                                    class="btn btn-outline-info btn-sm btn-action">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-tags text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">Chưa có danh mục nào</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}
?>