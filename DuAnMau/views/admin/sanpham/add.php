<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Thêm sản phẩm mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
</head>

<body data-theme="light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Thêm sản phẩm mới</h5>
                        <a href="?act=p-list" class="btn btn-outline-secondary btn-sm">Quay lại</a>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data" class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Tên sản phẩm</label>
                                    <input type="text" name="name" class="form-control" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Giá mặc định</label>
                                    <input type="number" name="price" class="form-control" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mô tả</label>
                                    <textarea name="description" class="form-control" rows="4"
                                        placeholder="Mô tả ngắn gọn về đồ uống..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Danh mục</label>
                                    <select name="category_id" class="form-select">
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Hình ảnh</label>
                                    <input type="file" name="img" class="form-control" />
                                    <div class="small text-muted mt-2">Tỉ lệ gợi ý: 1:1 hoặc 4:3</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i>
                                Lưu</button>
                            <a href="?act=p-list" class="btn btn-light">Hủy</a>
                        </div>
                        <div class="form-text mt-3">Thêm biến thể (Size, 500ml, ít đá, topping...) sẽ thao tác ở trang
                            danh sách sản phẩm.</div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>