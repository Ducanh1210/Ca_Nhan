<?php if (!isset($_SESSION))
    session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ?act=login');
    exit;
} ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sửa người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
</head>

<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Sửa người dùng</h5>
                        <a href="?act=users" class="btn btn-outline-secondary btn-sm">Quay lại</a>
                    </div>
                    <form method="post" class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Tên</label>
                            <input type="text" name="name" class="form-control"
                                value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vai trò</label>
                            <select name="role" class="form-select">
                                <option value="user" <?= (($user['role'] ?? 'user') === 'user') ? 'selected' : '' ?>>User
                                </option>
                                <option value="admin" <?= (($user['role'] ?? 'user') === 'admin') ? 'selected' : '' ?>>
                                    Admin</option>
                            </select>
                        </div>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger py-2"><?= $error ?></div><?php endif; ?>
                        <div class="d-flex gap-2">
                            <button class="btn btn-warning" type="submit"><i class="bi bi-save"></i> Lưu thay
                                đổi</button>
                            <a href="?act=users" class="btn btn-light">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

