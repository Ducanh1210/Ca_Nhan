<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng & Thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        :root {
            --brand: #198754;
            --brand-2: #20c997;
            --soft-bg: #f4f8f6;
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title i {
            font-size: 1.4rem;
            color: #198754;
        }

        .cart-item-img {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 8px;
        }

        .qty-input {
            width: 84px;
            text-align: center;
        }

        .card-elevated {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            background: #fff;
        }

        .card-elevated .card-header {
            border: 0;
            background: #fff;
            padding: 14px 18px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: .5rem;
        }

        .summary-total {
            font-size: 1.25rem;
            color: #198754;
            font-weight: 700;
        }

        .divider {
            border-top: 1px dashed #dee2e6;
            margin: .5rem 0 1rem;
        }

        .muted {
            color: #6c757d;
        }

        .sticky-summary {
            position: sticky;
            top: 20px;
        }

        .soft-card {
            background: linear-gradient(180deg, #ffffff, var(--soft-bg));
        }

        .btn-checkout {
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            border: none;
        }

        .btn-checkout:hover {
            filter: brightness(1.05);
        }

        .table thead th {
            border-bottom: none;
        }

        .table tbody tr+tr td {
            border-top-color: #eef2f0;
        }

        .variant-select {
            min-width: 180px;
        }
    </style>
    <?php $discount_percent = $voucher['discount_percent'] ?? 0; ?>
    <?php $total = 0;
    foreach (($cart ?? []) as $it) {
        $total += $it['price'] * $it['quantity'];
    } ?>
</head>

<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="page-title">
                <i class="bi bi-bag-check-fill"></i>
                <h4 class="mb-0">Giỏ hàng & Thanh toán</h4>
            </div>
            <a href="?act=home-client" class="btn btn-outline-success btn-sm">
                <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
            </a>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success text-center fs-6 py-2 mb-3"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger text-center fs-6 py-2 mb-3"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <?php if (!empty($voucher_message)): ?>
            <div class="alert alert-info py-2 mb-3"><i class="bi bi-tag"></i> <?= htmlspecialchars($voucher_message) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($cart)): ?>
            <div class="card card-elevated p-4 text-center">
                <div class="mb-2"><i class="bi bi-cart-x fs-1 text-muted"></i></div>
                <h5 class="mb-2">Giỏ hàng trống</h5>
                <p class="text-muted mb-3">Hãy chọn thêm sản phẩm để bắt đầu thanh toán.</p>
                <a href="?act=home-client" class="btn btn-success">Về trang chủ</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="card card-elevated">
                            <div class="card-header bg-white border-0">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-cart3 text-success"></i>
                                    <strong>Giỏ hàng</strong>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th class="text-center" width="220">Biến thể</th>
                                                <th class="text-center" width="110">Số lượng</th>
                                                <th class="text-end" width="150">Thành tiền</th>
                                                <th class="text-center" width="60">Xóa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($cart as $index => $item):
                                                $itemTotal = $item['price'] * $item['quantity']; ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?= htmlspecialchars($item['img']) ?>"
                                                                alt="<?= htmlspecialchars($item['name']) ?>"
                                                                class="cart-item-img me-3">
                                                            <div>
                                                                <div class="fw-semibold"><?= htmlspecialchars($item['name']) ?>
                                                                </div>
                                                                <div class="muted small">Giá:
                                                                    <?= number_format($item['price']) ?>đ
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="items[<?= $index ?>][id]"
                                                            value="<?= $item['id'] ?>">
                                                    </td>
                                                    <td class="text-center">
                                                        <select name="items[<?= $index ?>][variant_id]"
                                                            class="form-select form-select-sm" style="min-width: 180px;">
                                                            <?php
                                                            $variantOptions = (new Variant())->getByProductId($item['id']);
                                                            $currentVariant = $item['variant_id'] ?? 0;
                                                            if (empty($variantOptions)) {
                                                                echo '<option value="0">Mặc định - ' . number_format($item['price']) . 'đ</option>';
                                                            } else {
                                                                foreach ($variantOptions as $v) {
                                                                    $selected = ((int) $currentVariant === (int) $v['id']) ? 'selected' : '';
                                                                    echo '<option value="' . $v['id'] . '" ' . $selected . ' data-price="' . $v['price'] . '">' . htmlspecialchars($v['name']) . ' - ' . number_format($v['price']) . 'đ</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="number" min="1"
                                                            class="form-control form-control-sm qty-input quantity-input"
                                                            name="items[<?= $index ?>][quantity]"
                                                            value="<?= (int) $item['quantity'] ?>"
                                                            data-price="<?= (float) $item['price'] ?>"
                                                            data-index="<?= $index ?>">
                                                    </td>
                                                    <td class="text-end fw-semibold text-danger item-total"
                                                        id="item-total-<?= $index ?>"><?= number_format($itemTotal) ?>đ</td>
                                                    <td class="text-center">
                                                        <a href="?act=cart-remove&id=<?= $item['id'] ?>"
                                                            class="btn btn-sm btn-outline-danger" title="Xóa"
                                                            onclick="return confirm('Xóa sản phẩm này?')"><i
                                                                class="bi bi-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" formaction="?act=cart-update" formmethod="post" formnovalidate
                                        class="btn btn-outline-success">
                                        <i class="bi bi-arrow-repeat"></i> Cập nhật
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="card card-elevated mt-4">
                            <div class="card-header bg-white border-0">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-vcard text-success"></i>
                                    <strong>Thông tin nhận hàng</strong>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name"
                                            value="<?= htmlspecialchars($_POST['name'] ?? ($_SESSION['checkout_form']['name'] ?? ($_SESSION['user']['name'] ?? ''))) ?>"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="phone"
                                            value="<?= htmlspecialchars($_POST['phone'] ?? ($_SESSION['checkout_form']['phone'] ?? ($_SESSION['user']['phone'] ?? ''))) ?>"
                                            required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Địa chỉ nhận hàng <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="address"
                                            value="<?= htmlspecialchars($_POST['address'] ?? ($_SESSION['checkout_form']['address'] ?? ($_SESSION['user']['address'] ?? ''))) ?>"
                                            required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Ghi chú</label>
                                        <textarea class="form-control" name="note" rows="2"
                                            placeholder="Ghi chú về đơn hàng (không bắt buộc)"><?= htmlspecialchars($_POST['note'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="sticky-summary">
                            <div class="card card-elevated soft-card mb-3">
                                <div
                                    class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-receipt text-success"></i>
                                        <strong>Tóm tắt đơn hàng</strong>
                                    </div>
                                    <span class="badge bg-success-subtle text-success"><?= count($cart) ?> sản phẩm</span>
                                </div>
                                <div class="card-body">
                                    <div class="summary-row">
                                        <span>Tạm tính</span>
                                        <span class="fw-semibold" id="subtotal-text"><?= number_format($total) ?>đ</span>
                                    </div>
                                    <?php $discount = $total * ($discount_percent / 100); ?>
                                    <div class="summary-row <?= $discount_percent ? '' : 'd-none' ?>" id="discount-row">
                                        <span>Giảm giá</span>
                                        <span class="text-danger fw-semibold"
                                            id="discount-text">-<?= number_format($discount) ?>đ
                                            (<?= (int) $discount_percent ?>%)</span>
                                    </div>
                                    <div class="divider"></div>
                                    <div class="summary-row">
                                        <span class="summary-total">Tổng thanh toán</span>
                                        <span class="summary-total"
                                            id="grand-total-text"><?= number_format($total - $discount) ?>đ</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-elevated mb-3">
                                <div class="card-header bg-white border-0 d-flex align-items-center gap-2"><i
                                        class="bi bi-tag text-success"></i><strong>Mã giảm giá</strong></div>
                                <div class="card-body">
                                    <div class="input-group">
                                        <input type="text" name="voucher" class="form-control" placeholder="Nhập mã voucher"
                                            value="<?= htmlspecialchars($_POST['voucher'] ?? '') ?>">
                                        <button type="submit" name="apply_voucher" formnovalidate
                                            class="btn btn-outline-success"><i class="bi bi-tag"></i> Áp dụng</button>
                                    </div>
                                    <?php if (isset($voucher) && !empty($voucher)): ?>
                                        <small class="text-success d-block mt-2"><i class="bi bi-check-circle"></i> Đã áp dụng:
                                            <?= htmlspecialchars($voucher['code']) ?> (Giảm
                                            <?= (int) $discount_percent ?>%)</small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <button type="submit" name="pay" class="btn btn-checkout w-100 btn-lg text-white">
                                <i class="bi bi-credit-card"></i> Thanh toán ngay
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Đã tắt cập nhật tổng tiền realtime. Tổng sẽ cập nhật sau khi bấm "Cập nhật giỏ" (POST -> cart-update -> reload). -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>