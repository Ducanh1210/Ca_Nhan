<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #ffffff);
            min-height: 100vh;
        }
        .card {
            border-radius: 16px;
            border: none;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-success {
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-lg-5">
        <div class="card shadow-lg p-4">
            <h3 class="text-center mb-4 text-success"><i class="bi bi-person-plus-fill"></i> Đăng ký tài khoản</h3>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">👤 Họ tên</label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập họ tên..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label">📧 Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Nhập email..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label">🔒 Mật khẩu</label>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Tạo tài khoản</button>
                    <a href="?act=login" class="btn btn-outline-secondary">Đã có tài khoản? Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
