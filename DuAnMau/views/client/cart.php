<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng của bạn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-success text-white d-flex align-items-center">
                        <i class="bi bi-cart3 fs-3 me-2"></i>
                        <h4 class="mb-0">Giỏ hàng của bạn</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($cart)): ?>
                            <form action="?act=checkout" method="post">
                                <div class="table-responsive mb-4">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th width="140">Số lượng</th>
                                                <th width="140">Thành tiền</th>
                                                <th width="90">Xóa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total = 0;
                                            foreach ($cart as $index => $item):
                                                $itemTotal = $item['price'] * $item['quantity'];
                                                $total += $itemTotal;
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?= $item['img'] ?>" alt="<?= $item['name'] ?>"
                                                                class="rounded shadow-sm me-3"
                                                                style="width:60px; height:60px; object-fit:cover;">
                                                            <div>
                                                                <div class="fw-bold fs-5 text-dark mb-1">
                                                                    <?= htmlspecialchars($item['name']) ?>
                                                                </div>
                                                                <div class="text-muted small">Giá:
                                                                    <?= number_format($item['price']) ?>đ
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="items[<?= $index ?>][id]"
                                                            value="<?= $item['id'] ?>">
                                                        <input type="hidden" name="items[<?= $index ?>][name]"
                                                            value="<?= htmlspecialchars($item['name']) ?>">
                                                        <input type="hidden" name="items[<?= $index ?>][price]"
                                                            value="<?= $item['price'] ?>">
                                                        <input type="hidden" name="items[<?= $index ?>][img]"
                                                            value="<?= $item['img'] ?>">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="number" name="items[<?= $index ?>][quantity]"
                                                            value="<?= $item['quantity'] ?>" min="1"
                                                            class="form-control form-control-sm text-center quantity-input"
                                                            data-price="<?= $item['price'] ?>" data-index="<?= $index ?>"
                                                            style="width:80px;">
                                                    </td>
                                                    <td class="text-end text-danger fw-bold fs-5 item-total"
                                                        id="item-total-<?= $index ?>">
                                                        <?= number_format($itemTotal) ?>đ
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="?act=cart-remove&id=<?= $item['id'] ?>"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold">Tổng tiền:</span>
                                    <span class="text-success fs-4" id="grand-total"><?= number_format($total) ?>đ</span>
                                </div>
                                <div class="d-flex gap-3">
                                    <button type="submit" formaction="?act=cart-update"
                                        class="btn btn-outline-primary w-50 py-3 fs-5">
                                        <i class="bi bi-arrow-repeat me-2"></i> Cập nhật
                                    </button>
                                    <button type="submit" formaction="?act=checkout" class="btn btn-success w-50 py-3 fs-5">
                                        <i class="bi bi-credit-card me-2"></i> Thanh toán
                                    </button>
                                </div>

                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning text-center py-5 fs-4"><i
                                    class="bi bi-exclamation-triangle me-2"></i>Giỏ hàng của bạn đang trống!</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cập nhật giá theo số lượng thay đổi -->
    <script>
        function formatNumber(num) {
            return new Intl.NumberFormat('vi-VN').format(num) + 'đ';
        }

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('input', function () {
                const price = parseFloat(this.dataset.price);
                const index = this.dataset.index;
                let quantity = parseInt(this.value);

                if (isNaN(quantity) || quantity < 1) quantity = 1;
                this.value = quantity;

                const itemTotal = price * quantity;
                document.getElementById('item-total-' + index).innerText = formatNumber(itemTotal);

                let total = 0;
                document.querySelectorAll('.quantity-input').forEach(inp => {
                    const p = parseFloat(inp.dataset.price);
                    const q = parseInt(inp.value);
                    total += p * q;
                });

                document.getElementById('grand-total').innerText = formatNumber(total);
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>