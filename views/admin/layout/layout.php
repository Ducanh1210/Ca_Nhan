<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'ANHSEO - Admin' ?> - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .sidebar {
      min-height: 100vh;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .sidebar .nav-link {
      color: #adb5bd;
      transition: all 0.3s;
    }

    .sidebar .nav-link:hover {
      color: #fff;
      background-color: rgba(255, 255, 255, 0.1);
    }

    .sidebar .nav-link.active {
      color: #fff;
      background-color: rgba(255, 255, 255, 0.2);
    }

    .main-content {
      background-color: #f8f9fa;
      min-height: 100vh;
    }

    .navbar-brand {
      font-weight: bold;
    }

    .content-wrapper {
      padding: 20px;
    }

    .card {
      border: none;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .table th {
      border-top: none;
      font-weight: 600;
    }

    .btn-group-sm>.btn,
    .btn-sm {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
    }

    /* Notification styles */
    .nav-link.position-relative {
      transition: all 0.3s ease;
    }

    .nav-link.position-relative:hover {
      transform: translateY(-2px);
    }

    .badge {
      font-size: 0.65rem;
      font-weight: 600;
    }

    .nav-link .bi {
      transition: color 0.3s ease;
    }

    .nav-link:hover .bi {
      color: #0d6efd;
    }

    /* Tooltip styles */
    .tooltip {
      font-size: 0.875rem;
    }
  </style>
  <?php if (isset($extraCSS)): ?>
    <?= $extraCSS ?>
  <?php endif; ?>
</head>

<body>
  <?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
  } ?>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
        <div class="position-sticky pt-3">
          <!-- Logo/Brand -->
          <div class="text-center mb-4">
            <h4 class="text-white">
              <i class="bi bi-shield-check"></i> Admin Panel
            </h4>
          </div>

          <!-- Navigation Menu -->
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link <?= ($currentPage === 'dashboard') ? 'active' : '' ?>" href="?act=dashboard">
                <i class="bi bi-house-door me-2"></i> Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($currentPage === 'products') ? 'active' : '' ?>" href="?act=p-list">
                <i class="bi bi-box me-2"></i> Quản lý Sản phẩm
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($currentPage === 'categories') ? 'active' : '' ?>" href="?act=category-list">
                <i class="bi bi-tags me-2"></i> Quản lý Danh mục
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link <?= ($currentPage === 'users') ? 'active' : '' ?>" href="?act=users">
                <i class="bi bi-people me-2"></i> Quản lý Người dùng
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($currentPage === 'orders') ? 'active' : '' ?>" href="?act=orders">
                <i class="bi bi-receipt me-2"></i> Quản lý Đơn hàng
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($currentPage === 'vouchers') ? 'active' : '' ?>" href="?act=vouchers">
                <i class="bi bi-ticket-perforated me-2"></i> Quản lý Voucher
              </a>
            </li>

            <li class="nav-item mt-3">
              <hr class="text-white-50">
            </li>
            <li class="nav-item">
              <a class="nav-link text-warning" href="?act=settings">
                <i class="bi bi-gear me-2"></i> Cài đặt
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-danger" href="?act=logout">
                <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
              </a>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Main Content Area -->
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
        <!-- Top Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
          <div class="container-fluid">
            <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target=".sidebar">
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-nav ms-auto">
              <!-- Notifications -->
              <div class="nav-item me-3">
                <a class="nav-link position-relative" href="#" data-bs-toggle="tooltip" title="Thông báo">
                  <i class="bi bi-bell fs-5"></i>
                  <?php $notif = $_SESSION['flash'] ?? null; ?>
                  <span
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $notif ? 1 : 0 ?></span>
                </a>
              </div>

              <!-- Messages -->
              <div class="nav-item me-3">
                <a class="nav-link position-relative" href="#" data-bs-toggle="tooltip" title="Tin nhắn">
                  <i class="bi bi-envelope fs-5"></i>
                  <span
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">5</span>
                </a>
              </div>

              <!-- User Dropdown -->
              <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                  <i class="bi bi-person-circle me-1"></i>
                  <?= htmlspecialchars($_SESSION['admin']['name'] ?? 'Admin') ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="?act=profile">
                      <i class="bi bi-person me-2"></i> Hồ sơ
                    </a></li>
                  <li><a class="dropdown-item" href="?act=change-password">
                      <i class="bi bi-key me-2"></i> Đổi mật khẩu
                    </a></li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li><a class="dropdown-item text-danger" href="?act=logout">
                      <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                    </a></li>
                </ul>
              </div>
            </div>
          </div>
        </nav>



        <!-- Main Content -->
        <?php if (!empty($_SESSION['flash'])):
          $flash = $_SESSION['flash'];
          unset($_SESSION['flash']); ?>
          <div class="alert alert-<?= htmlspecialchars($flash['type'] ?? 'success') ?> alert-dismissible fade show"
            role="alert">
            <?= htmlspecialchars($flash['message'] ?? '') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?= $mainContent ?>
    </div>
    </main>
  </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Common Admin Scripts -->
  <script>
    // Toggle sidebar on mobile
    document.addEventListener('DOMContentLoaded', function () {
      const sidebarToggle = document.querySelector('.navbar-toggler');
      const sidebar = document.querySelector('.sidebar');

      if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
          sidebar.classList.toggle('show');
        });
      }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function () {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(function (alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      });
    }, 5000);

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  </script>

  <?php if (isset($extraJS)): ?>
    <?= $extraJS ?>
  <?php endif; ?>
</body>

</html>