<?php
// Biến $product và $variants được truyền từ controller ProductController->detail()
if (!isset($product) || empty($product)) {
    echo '<div class="alert alert-danger mt-5 text-center">Không tìm thấy sản phẩm.</div>';
    return;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chi Tiết Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f9;
        }

        .product-detail {
            background-color: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        }

        .product-image img {
            width: 100%;
            border-radius: 12px;
        }

        .form-select,
        .form-control {
            border-radius: 8px;
        }

        .btn {
            font-size: 1rem;
            border-radius: 8px;
        }

        .price-tag {
            font-size: 1.5rem;
            color: #e63946;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row g-5 align-items-start">
            <div class="col-lg-6">
                <div class="product-image">
                    <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="product-detail">
                    <h2 class="text-success mb-3"><?= htmlspecialchars($product['name']) ?></h2>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

                    <form id="custom-form">
                        <div class="mb-3">
                            <label class="form-label">Chọn biến thể</label>
                            <select id="variant" class="form-select" onchange="updatePrice()">
                                <?php if (!empty($variants)):
                                    foreach ($variants as $v): ?>
                                        <option value="<?= (int) $v['id'] ?>" data-price="<?= (int) $v['price'] ?>">
                                            <?= htmlspecialchars($v['name']) ?> - <?= number_format((int) $v['price']) ?> đ
                                        </option>
                                    <?php endforeach; else: ?>
                                    <option value="0" data-price="<?= (int) $product['price'] ?>">
                                        Mặc định - <?= number_format((int) $product['price']) ?> đ
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số lượng</label>
                            <input type="number" id="quantity" class="form-control" value="1" min="1"
                                onchange="updatePrice()">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Giá dự kiến</label>
                            <div id="display-price" class="price-tag">0đ</div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="button" onclick="addToCart()" class="btn btn-success flex-fill">
                                <i class="bi bi-cart-plus"></i> Thêm giỏ hàng
                            </button>
                            <a id="buy-now-btn" href="<?= BASE_URL ?>?act=buy-now&id=<?= $product['id'] ?>"
                                class="btn btn-warning btn-sm me-2">
                                <i class="bi bi-bag-check"></i> Mua ngay
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const basePrice = <?= (int) ($variants[0]['price'] ?? $product['price']) ?>;

        function updatePrice() {
            const variant = document.querySelector('#variant option:checked');
            const quantity = parseInt(document.getElementById("quantity").value || 1);

            const vprice = parseFloat(variant?.dataset.price || basePrice);
            const total = vprice * quantity;

            document.getElementById("display-price").innerText = total.toLocaleString() + 'đ';
        }



        function getSelectionData() {
            return {
                product_id: <?= $product['id'] ?>,
                variant_id: document.getElementById("variant").value,
                quantity: document.getElementById("quantity").value
            };
        }
        function addToCart() {
            const productId = <?= $product['id'] ?>;
            fetch(`?act=cart-add&id=${productId}`)
                .then(res => res.json())
                .then(resp => {
                    if (resp.cartCount !== undefined) {
                        alert("✅ Đã thêm vào giỏ hàng!");
                        // Cập nhật số lượng trên icon nếu muốn
                        // document.querySelector('.bi-cart3 + span').innerText = resp.cartCount;
                    } else {
                        alert("❌ Thêm thất bại!");
                    }
                })
                .catch(err => {
                    alert("❌ Lỗi hệ thống: " + err.message);
                });
        }

        // Cập nhật link Mua ngay kèm số lượng hiện tại
        document.getElementById('quantity').addEventListener('input', () => {
            const q = Math.max(1, parseInt(document.getElementById('quantity').value || '1', 10));
            const btn = document.getElementById('buy-now-btn');
            const url = new URL(btn.href, window.location.origin);
            url.searchParams.set('qty', q);
            btn.href = url.pathname + url.search;
        });




        window.onload = updatePrice;
    </script>
</body>

</html>