<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu người dùng đã đăng nhập → chuyển về trang chủ client (hoặc tùy chỉnh)
if (isset($_SESSION['user'])) {
    header('Location: ?act=home-client');
    exit;
}

// Nếu là admin → cũng chuyển luôn
if (isset($_SESSION['admin'])) {
    header('Location: ?act=dashboard');
    exit;
}
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #fceabb, #f8b500);
            min-height: 100vh;
        }

        .card {
            border-radius: 16px;
            border: none;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-lg-5">
            <div class="card shadow-lg p-4 bg-white">
                <h3 class="text-center mb-4 text-primary"><i class="bi bi-box-arrow-in-right"></i> Đăng nhập</h3>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">📧 Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Nhập email..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">🔒 Mật khẩu</label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..."
                            required>
                    </div>
                    <?php if (isset($_GET['message']) && $_GET['message'] === 'not_logged_in'): ?>
                        <div class="alert alert-warning text-center">
                            ⚠️ Bạn cần đăng nhập để tiếp tục thanh toán.
                        </div>
                    <?php endif; ?>


                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Đăng nhập</button>
                        <a href="?act=register" class="btn btn-outline-secondary">Chưa có tài khoản? Đăng ký</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>