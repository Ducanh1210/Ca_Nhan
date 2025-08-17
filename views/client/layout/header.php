<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANHSEO - Trà Sữa & Cà Phê</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Toastr CSS for notifications -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #28a745;
            --secondary-color: #6c757d;
            --accent-color: #ffc107;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --success-color: #198754;
            --danger-color: #dc3545;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* Header Styles */
        .main-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            box-shadow: 0 2px 20px rgba(40, 167, 69, 0.15);
        }

        .navbar-brand img {
            height: 60px;
            transition: transform 0.3s ease;
        }

        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        .search-container {
            position: relative;
            max-width: 500px;
            width: 100%;
        }

        .search-input {
            border-radius: 25px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding-left: 50px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #fff;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 1);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            z-index: 10;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: #fff !important;
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: #fff;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .user-menu {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .favorite-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .favorite-btn:hover {
            background: #fff;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .favorite-btn i {
            color: #dc3545;
            transition: all 0.3s ease;
        }

        .favorite-btn.active i {
            animation: heartBeat 0.6s ease-in-out;
        }

        @keyframes heartBeat {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.3);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Product Card Styles */
        .product-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .product-card img {
            height: 250px;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        .product-card:hover img {
            transform: scale(1.05);
        }

        .product-price {
            background: linear-gradient(45deg, var(--primary-color), var(--success-color));
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
        }

        .btn-custom {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .search-container {
                max-width: 100%;
                margin: 10px 0;
            }

            .navbar-brand img {
                height: 50px;
            }
        }
    </style>
</head>

<body>
    <?php if (!isset($_SESSION))
        session_start(); ?>

    <!-- Header -->
    <nav class="navbar navbar-expand-lg main-header sticky-top">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="<?= BASE_URL ?>">
                <img src="<?= BASE_URL ?>img/logo.png" alt="ANHSEO" class="img-fluid">
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Search Bar -->
                <div class="search-container mx-auto">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="form-control search-input" placeholder="Tìm kiếm sản phẩm...">
                </div>

                <!-- Right Side Items -->
                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- User Menu -->
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'user'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle user-menu px-3 py-2 rounded" href="#" role="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-2"></i>
                                <?= htmlspecialchars($_SESSION['user']['name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="?act=orders-client">
                                        <i class="bi bi-receipt-cutoff me-2"></i>Đơn hàng của tôi
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="?act=logout">
                                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                    </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?act=login">
                                <i class="bi bi-person me-1"></i>Đăng nhập
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Cart -->
                    <li class="nav-item position-relative me-3">
                        <a class="nav-link" href="?act=cart-view">
                            <i class="bi bi-cart3 fs-5"></i>
                            <span class="cart-badge" id="cart-count">
                                <?php $u = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                                echo min($u, defined('CART_BADGE_CAP') ? CART_BADGE_CAP : 99); ?>
                            </span>
                        </a>
                    </li>

                    <!-- Favorites -->
                    <li class="nav-item position-relative">
                        <a class="nav-link" href="#" onclick="showFavorites()">
                            <i class="bi bi-heart-fill fs-5"></i>
                            <span class="cart-badge" id="favorite-count">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Navigation Menu -->
    <div class="bg-white border-bottom">
        <div class="container">
            <ul class="nav nav-pills nav-fill">
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['act'] ?? 'home-client') == 'home-client' ? 'active bg-success' : 'text-dark' ?>"
                        href="<?= BASE_URL ?>?act=home-client">
                        <i class="bi bi-house me-1"></i>Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['act'] ?? '') == 'products' ? 'active bg-success' : 'text-dark' ?>"
                        href="<?= BASE_URL ?>?act=products">
                        <i class="bi bi-grid me-1"></i>Sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['act'] ?? '') == 'news' ? 'active bg-success' : 'text-dark' ?>"
                        href="<?= BASE_URL ?>?act=news">
                        <i class="bi bi-newspaper me-1"></i>Tin tức
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['act'] ?? '') == 'contact' ? 'active bg-success' : 'text-dark' ?>"
                        href="<?= BASE_URL ?>?act=contact">
                        <i class="bi bi-telephone me-1"></i>Liên hệ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['act'] ?? '') == 'about' ? 'active bg-success' : 'text-dark' ?>"
                        href="<?= BASE_URL ?>?act=about">
                        <i class="bi bi-info-circle me-1"></i>Giới thiệu
                    </a>
                </li>
            </ul>
        </div>
    </div>