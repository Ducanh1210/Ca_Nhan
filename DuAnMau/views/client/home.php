<?php
/**
 * FILE: views/client/home.php
 * CHỨC NĂNG: View trang chủ chính của website - hiển thị giao diện người dùng
 * LUỒNG CHẠY: 
 * - VÀO: Nhận dữ liệu sản phẩm từ ProductController (client() hoặc clients())
 * - RA: Hiển thị giao diện trang chủ với danh sách sản phẩm và các chức năng tương tác
 * - NGƯỢC LẠI: User tương tác với các nút, form để chuyển đến các trang khác
 */
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ANHSEO - Trà Sữa</title>
  <!-- LIÊN KẾT CSS BOOTSTRAP VÀ ICONS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    /* CSS TÙY CHỈNH CHO TRANG CHỦ */
    body {
      font-family: "Segoe UI", sans-serif;
      background-color: #f9f9f9;
    }

    /* STYLE CHO LOGO BRAND */
    .navbar-brand img {
      height: 80px;
      max-width: 180px;
      object-fit: contain;
      transition: transform 0.2s;
    }

    .navbar-brand img:hover {
      transform: scale(1.05);
    }

    /* STYLE CHO Ô TÌM KIẾM */
    .search-box {
      width: 100%;
      max-width: 500px;
      position: relative;
      background: #fff;
      border-radius: 40px;
      box-shadow: 0 2px 12px rgba(0, 108, 76, 0.07);
      border: 2px solid #60a5fa;
    }

    .search-box input {
      padding-left: 50px;
      border: none;
      border-radius: 40px;
      height: 48px;
      font-size: 1.1rem;
      background: transparent;
      outline: none;
    }

    .search-box .bi-search {
      position: absolute;
      top: 50%;
      left: 18px;
      transform: translateY(-50%);
      color: #60a5fa;
      font-size: 1.5rem;
    }

    /* STYLE CHO MENU NAVIGATION */
    .menu-bar {
      border-top: 1px solid #eee;
      border-bottom: 1px solid #eee;
      background-color: #fff;
    }

    .menu-bar a {
      padding: 15px 20px;
      display: inline-block;
      color: #333;
      font-weight: 500;
      text-decoration: none;
    }

    .menu-bar a:hover {
      color: #006c4c;
      border-bottom: 2px solid #006c4c;
    }

    /* STYLE CHO HERO SECTION - BANNER CHÍNH */
    .hero {
      position: relative;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 14px 40px rgba(0, 0, 0, .12);
      margin-bottom: 24px;
    }

    .hero img {
      width: 100%;
      max-height: 520px;
      object-fit: cover;
      display: block;
    }

    .hero-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(180deg, rgba(0, 0, 0, .35) 0%, rgba(0, 0, 0, .55) 80%);
      display: flex;
      align-items: flex-end;
      padding: 32px;
      color: #fff;
    }

    .hero-cta {
      backdrop-filter: blur(6px);
      background: rgba(255, 255, 255, .12);
      border: 1px solid rgba(255, 255, 255, .25);
      border-radius: 14px;
      padding: 16px 20px;
      max-width: 520px;
    }

    .hero-cta h2 {
      font-weight: 800;
      letter-spacing: .2px;
    }

    .hero-cta p {
      opacity: .92;
    }

    /* STYLE CHO PHẦN SẢN PHẨM */
    .section-products {
      padding: 60px 0;
    }

    .product-card {
      border: 0;
      border-radius: 16px;
      overflow: hidden;
      transition: transform .25s ease, box-shadow .25s ease;
      box-shadow: 0 6px 24px rgba(0, 0, 0, .08);
    }

    .product-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 18px 48px rgba(0, 0, 0, .14);
    }

    .product-image-wrapper {
      position: relative;
    }

    .product-card img {
      height: 240px;
      object-fit: cover;
    }

    /* STYLE CHO CÁC NÚT TƯƠNG TÁC SẢN PHẨM */
    .product-actions {
      position: absolute;
      inset: auto 12px 12px 12px;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      opacity: 0;
      transform: translateY(10px);
      transition: all .25s ease;
    }

    .product-card:hover .product-actions {
      opacity: 1;
      transform: translateY(0);
    }

    /* STYLE CHO ĐÁNH GIÁ SAO */
    .rating-stars i {
      color: #ffc107;
    }

    /* STYLE CHO FOOTER */
    .footer {
      background-color: #1c1c1c;
      color: #bbb;
      padding: 60px 0 30px;
      font-size: 15px;
      line-height: 1.6;
    }

    .footer h5 {
      color: red;
      margin-bottom: 20px;
      font-size: 16px;
      text-transform: uppercase;
    }

    .footer a {
      color: #bbb;
      text-decoration: none;
      display: block;
      margin-bottom: 10px;
      transition: all 0.3s ease;
    }

    .footer a:hover {
      color: #fff;
      padding-left: 6px;
    }

    .footer a.active {
      color: #fff;
      font-weight: bold;
      border-left: 3px solid #28a745;
      padding-left: 10px;
    }

    .footer .social-icons a {
      color: #bbb;
      font-size: 20px;
      margin-right: 15px;
      transition: all 0.3s ease;
    }

    .footer .social-icons a:hover {
      color: #28a745;
      transform: scale(1.1);
    }

    .footer .footer-bottom {
      border-top: 1px solid #333;
      margin-top: 30px;
      padding-top: 15px;
      font-size: 14px;
      color: #888;
      text-align: center;
    }

    .footer .social-icons a {
      display: inline-block;
      margin-bottom: 8px;
      color: #bbb;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .footer .social-icons a:hover {
      color: #28a745;
      padding-left: 5px;
    }


    .menu-bar {
      background-color: #fff;
      border-bottom: 1px solid #eee;
      padding: 10px 0;
      margin-bottom: 10px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .menu-bar a {
      color: #333;
      font-weight: 500;
      padding: 10px 20px;
      text-decoration: none;
      transition: all 0.3s ease;
      border-bottom: 2px solid transparent;
    }

    .menu-bar a:hover {
      color: #006c4c;
      border-color: #006c4c;
    }

    .menu-bar a.active {
      color: #006c4c;
      border-bottom: 2px solid #006c4c;
    }

    /* Sang trọng hơn */
    :root {
      --accent: #d4af37;
      /* gold */
      --deep: #0f3d2e;
      --soft: #f4f8f6;
    }

    .section-title {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      font-weight: 800;
      color: #0b7a4b;
    }

    .section-title:after {
      content: "";
      display: inline-block;
      width: 80px;
      height: 3px;
      background: linear-gradient(90deg, var(--accent), transparent);
      border-radius: 2px;
    }

    .subtle-text {
      color: #6b7d76;
    }

    .glass {
      backdrop-filter: blur(8px);
      background: rgba(255, 255, 255, .5);
      border: 1px solid rgba(255, 255, 255, .6);
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(15, 61, 46, .08);
    }

    .feature-card {
      background: linear-gradient(180deg, #ffffff, #f7fbf9);
      border: 1px solid #e9f3ef;
      border-radius: 14px;
      padding: 18px;
      height: 100%;
    }

    .feature-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      color: #0b7a4b;
      background: #eaf7f1;
    }

    .product-price-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 12px;
      border-radius: 999px;
      background: linear-gradient(90deg, #0b7a4b, #11a36d);
      color: #fff;
      font-weight: 700;
    }
  </style>
</head>

<body>
  <!-- NAVIGATION BAR - THANH ĐIỀU HƯỚNG CHÍNH -->
  <?php /* Session đã được mở trong controller */ ?>
  <nav class="navbar navbar-expand-lg bg-white py-2 shadow-sm sticky-top">
    <div class="container align-items-center justify-content-between">
      <a class="navbar-brand" href="#">
        <img src="<?= BASE_URL ?>img/logo.png" alt="Phúc Long" />
      </a>

      <!-- FORM TÌM KIẾM SẢN PHẨM -->
      <form class="d-none d-lg-block search-box me-3 position-relative" action="" method="get">
        <input type="hidden" name="act" value="home-client">
        <span class="position-absolute top-50 start-0 translate-middle-y ps-3">
          <i class="bi bi-search"></i>
        </span>
        <input class="form-control rounded-pill ps-5" type="text" name="q"
          value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Bạn muốn mua gì...">
      </form>

      <div class="d-flex align-items-center gap-3">
        <a href="#" class="text-success fw-bold d-none d-md-inline">Chọn Phương Thức Nhận Hàng</a>
        <a href="#"><i class="bi bi-envelope fs-5 text-success"></i></a>

        <!-- PHẦN XỬ LÝ ĐĂNG NHẬP/ĐĂNG XUẤT VÀ MENU USER -->
        <?php /* Session đã được mở trong controller */ ?>

        <?php if (isset($_SESSION['user']) && (($_SESSION['user']['role'] ?? 'user') === 'user')): ?>
          <!-- USER ĐÃ ĐĂNG NHẬP - HIỂN THỊ DROPDOWN MENU -->
          <div class="dropdown">
            <a class="btn btn-light dropdown-toggle text-success fw-bold" href="#" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['user']['name']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="?act=orders-client"><i class="bi bi-receipt-cutoff me-2"></i> Đơn
                  hàng</a></li>
              <li><a class="dropdown-item" href="?act=logout"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a>
              </li>
            </ul>
          </div>
        <?php else: ?>
          <!-- USER CHƯA ĐĂNG NHẬP - HIỂN THỊ NÚT ĐĂNG NHẬP -->
          <a href="?act=login" class="btn btn-outline-success btn-sm">
            <i class="bi bi-person"></i> Đăng nhập
          </a>
        <?php endif; ?>


        <!-- NÚT GIỎ HÀNG VỚI BADGE SỐ LƯỢNG -->
        <a href="?act=cart-view" class="position-relative">
          <i class="bi bi-cart3 fs-5 text-success"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?php
            // Tính số lượng sản phẩm trong giỏ hàng và giới hạn hiển thị badge
            $u = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
            echo min($u, defined('CART_BADGE_CAP') ? CART_BADGE_CAP : 99);
            ?>
          </span>
        </a>

        <!-- NÚT YÊU THÍCH VỚI BADGE SỐ LƯỢNG -->
        <a href="#" class="position-relative" onclick="showFavorites()">
          <i class="bi bi-heart-fill fs-5 text-danger"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            id="favorite-count">0</span>
        </a>
      </div>
    </div>



  </nav>
  <!-- MENU CHÍNH - ĐIỀU HƯỚNG CÁC TRANG -->
  <div class="menu-bar text-center">
    <div class="container d-flex justify-content-center flex-wrap">
      <!-- MENU ITEMS VỚI ACTIVE STATE DỰA TRÊN ACT HIỆN TẠI -->
      <a href="<?= BASE_URL ?>?act=home-client"
        class="<?= ($_GET['act'] ?? 'home-client') == 'home-client' ? 'text-success border-bottom border-2' : '' ?>">Trang
        chủ</a>
      <a href="<?= BASE_URL ?>?act=products"
        class="<?= ($_GET['act'] ?? '') == 'products' ? 'text-success border-bottom border-2' : '' ?>">Sản phẩm</a>
      <a href="<?= BASE_URL ?>?act=news"
        class="<?= ($_GET['act'] ?? '') == 'news' ? 'text-success border-bottom border-2' : '' ?>">Tin tức</a>
      <a href="<?= BASE_URL ?>?act=contact"
        class="<?= ($_GET['act'] ?? '') == 'contact' ? 'text-success border-bottom border-2' : '' ?>">Liên hệ</a>
      <a href="<?= BASE_URL ?>?act=about"
        class="<?= ($_GET['act'] ?? '') == 'about' ? 'text-success border-bottom border-2' : '' ?>">Giới thiệu</a>
    </div>
  </div>

  <!-- HERO SECTION - BANNER CHÍNH VỚI CALL-TO-ACTION -->
  <section class="hero">
    <img src="<?= BASE_URL ?>img/banner.png" alt="Khuyến mãi nổi bật">
    <div class="hero-overlay">
      <div class="hero-cta glass">
        <h2 class="mb-2">Hương vị thượng hạng mỗi ngày</h2>
        <p class="mb-3 subtle-text">Nguyên liệu tuyển chọn, quy trình chuẩn mực. Đặt online - giao nhanh tận nơi.</p>
        <div class="d-flex gap-2">
          <!-- NÚT CALL-TO-ACTION CHÍNH -->
          <a href="?act=products" class="btn btn-success fw-bold"><i class="bi bi-bag"></i> Mua ngay</a>
          <a href="#features" class="btn btn-outline-light fw-bold"><i class="bi bi-stars"></i> Khám phá</a>
        </div>
      </div>
    </div>
  </section>

  <!-- PHẦN SẢN PHẨM CHÍNH VÀ TÍNH NĂNG NỔI BẬT -->
  <section class="section-products">
    <div class="container">
      <!-- TIÊU ĐỀ PHẦN SẢN PHẨM -->
      <div class="text-center mb-4">
        <h2 class="section-title">Sản phẩm nổi bật</h2>
        <div class="subtle-text">Chọn vị yêu thích của bạn – tươi mới, tinh tế, giá hợp lý</div>
      </div>

      <!-- ROW TÍNH NĂNG NỔI BẬT -->
      <div id="features" class="row g-3 mb-4">
        <!-- TÍNH NĂNG 1: CÔNG THỨC CHUẨN -->
        <div class="col-md-4">
          <div class="feature-card d-flex align-items-start gap-3">
            <div class="feature-icon"><i class="bi bi-cup-hot"></i></div>
            <div>
              <div class="fw-bold">Công thức chuẩn</div>
              <div class="small text-muted">Pha chế đồng nhất, tiêu chuẩn hương vị</div>
            </div>
          </div>
        </div>
        <!-- TÍNH NĂNG 2: GIAO NHANH -->
        <div class="col-md-4">
          <div class="feature-card d-flex align-items-start gap-3">
            <div class="feature-icon"><i class="bi bi-truck"></i></div>
            <div>
              <div class="fw-bold">Giao nhanh</div>
              <div class="small text-muted">Ship thần tốc trong khu vực hoạt động</div>
            </div>
          </div>
        </div>
        <!-- TÍNH NĂNG 3: AN TÂM CHẤT LƯỢNG -->
        <div class="col-md-4">
          <div class="feature-card d-flex align-items-start gap-3">
            <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
            <div>
              <div class="fw-bold">An tâm chất lượng</div>
              <div class="small text-muted">Nguồn nguyên liệu được kiểm soát</div>
            </div>
          </div>
        </div>
      </div>
      <!-- ROW HIỂN THỊ DANH SÁCH SẢN PHẨM -->
      <div class="row g-4">
        <!-- SỬ DỤNG PHP ĐỂ LẶP DANH SÁCH SẢN PHẨM TỪ CONTROLLER -->
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $product): ?>
            <div class="col-md-4">
              <!-- CARD SẢN PHẨM -->
              <div class="card product-card h-100 position-relative">
                <!-- NÚT YÊU THÍCH -->
                <button class="btn btn-sm position-absolute top-0 end-0 m-2 favorite-btn" data-id="<?= $product['id'] ?>"
                  onclick="toggleFavorite(<?= $product['id'] ?>)">
                  <i class="bi bi-heart fs-5"></i>
                </button>

                <!-- PHẦN HÌNH ẢNH SẢN PHẨM -->
                <div class="product-image-wrapper">
                  <img src="<?= htmlspecialchars($product['img']) ?>" class="card-img-top"
                    alt="<?= htmlspecialchars($product['name']) ?>">
                  <!-- CÁC NÚT TƯƠNG TÁC (HIỆN KHI HOVER) -->
                  <div class="product-actions">
                    <a href="<?= BASE_URL ?>?act=product-detail&id=<?= $product['id'] ?>"
                      class="btn btn-light btn-sm fw-semibold"><i class="bi bi-eye"></i> Xem</a>
                    <button type="button" class="btn btn-success btn-sm fw-semibold"
                      onclick="addToCart(<?= $product['id'] ?>)"><i class="bi bi-cart-plus"></i> Thêm</button>
                  </div>
                </div>

                <!-- PHẦN THÔNG TIN SẢN PHẨM -->
                <div class="card-body text-center">
                  <h5 class="card-title text-success fw-bold mb-1"><?= htmlspecialchars($product['name']) ?></h5>
                  <!-- ĐÁNH GIÁ SAO -->
                  <div class="rating-stars mb-2">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star"></i>
                  </div>
                  <!-- MÔ TẢ NGẮN GỌN -->
                  <p class="card-text mb-2 text-muted"><?= htmlspecialchars(mb_substr($product['description'], 0, 70)) ?>...
                  </p>
                  <!-- GIÁ SẢN PHẨM -->
                  <div class="mb-2">
                    <span class="product-price-pill"><i class="bi bi-cash-coin"></i> <?= number_format($product['price']) ?>
                      đ</span>
                  </div>
                  <!-- TÊN DANH MỤC -->
                  <div>
                    <span
                      class="badge bg-success-subtle text-success border border-success"><?= htmlspecialchars($product['category_name']) ?></span>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- THÔNG BÁO KHI KHÔNG CÓ SẢN PHẨM -->
          <div class="col-12 text-center">Chưa có sản phẩm nào.</div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- FOOTER - CHÂN TRANG WEBSITE -->
  <footer class="footer">
    <div class="container">
      <div class="row">
        <!-- CỘT 1: THÔNG TIN CÔNG TY -->
        <div class="col-md-3 mb-4">
          <h5>ANHSEO Trà Sữa</h5>
          <p>Hệ thống đặt hàng trà sữa, cà phê, đồ uống tiện lợi. Uy tín - Chất lượng - Giá tốt.</p>
          <p><i class="bi bi-geo-alt-fill me-2"></i>Bút Sơn-Phường Lý Thường Kiệt-Ninh Binh</p>
          <p><i class="bi bi-telephone-fill me-2"></i> 0386 036 692</p>
          <p><i class="bi bi-envelope-fill me-2"></i> support@anhseo.vn</p>
        </div>

        <!-- CỘT 2: LIÊN KẾT TRANG -->
        <div class="col-md-3 mb-4">
          <h5>Liên kết</h5>
          <a href="?act=home-client"
            class="<?= ($_GET['act'] ?? 'home-client') == 'home-client' ? 'active' : '' ?>">Trang chủ</a>
          <a href="?act=products" class="<?= ($_GET['act'] ?? '') == 'products' ? 'active' : '' ?>">Sản phẩm</a>
          <a href="?act=about" class="<?= ($_GET['act'] ?? '') == 'about' ? 'active' : '' ?>">Giới thiệu</a>
          <a href="?act=contact" class="<?= ($_GET['act'] ?? '') == 'contact' ? 'active' : '' ?>">Liên hệ</a>
        </div>

        <!-- CỘT 3: HỖ TRỢ KHÁCH HÀNG -->
        <div class="col-md-3 mb-4">
          <h5>Hỗ trợ</h5>
          <a href="#">Chính sách bảo mật</a>
          <a href="#">Điều khoản sử dụng</a>
          <a href="#">Câu hỏi thường gặp</a>
          <a href="#">Hướng dẫn mua hàng</a>
        </div>

        <!-- CỘT 4: MẠNG XÃ HỘI VÀ ĐĂNG KÝ NHẬN TIN -->
        <div class="col-md-3 mb-4">
          <h5>Kết nối</h5>
          <div class="social-icons mb-3">
            <a href="https://facebook.com" target="_blank" rel="noopener">
              <i class="bi bi-facebook me-1"></i>Facebook
            </a>
            <a href="#"><i class="bi bi-instagram me-1"></i>Instagram</a>
            <a href="#"><i class="bi bi-tiktok me-1"></i>TikTok</a>
            <a href="#"><i class="bi bi-youtube me-1"></i>YouTube</a>
          </div>

          <p>Đăng ký nhận khuyến mãi:</p>
          <form>
            <div class="input-group">
              <input type="email" class="form-control form-control-sm" placeholder="Nhập email...">
              <button class="btn btn-success btn-sm" type="submit"><i class="bi bi-send"></i></button>
            </div>
          </form>
        </div>
      </div>

      <div class="footer-bottom mt-4">
        &copy; <?= date('Y') ?> ANHSEO. Thiết kế bởi Đội Dev AnhSEO.
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Toast Container -->
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="favoriteToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <i class="bi bi-heart-fill me-2"></i>
          <span id="toastMessage"></span>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
          aria-label="Close"></button>
      </div>
    </div>
  </div>

  <style>
    .toast {
      background: linear-gradient(135deg, #28a745, #20c997);
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .toast.warning {
      background: linear-gradient(135deg, #ffc107, #fd7e14);
    }

    .favorite-btn {
      transition: all 0.3s ease;
    }

    .favorite-btn:hover {
      transform: scale(1.1);
    }

    .favorite-btn.active {
      background: #dc3545 !important;
      color: white !important;
    }

    .favorite-btn.active i {
      color: white !important;
    }
  </style>

  <script>
    let favoriteProducts = JSON.parse(localStorage.getItem('favoriteProducts') || '[]');

    function showFavorites() {
      const favorites = JSON.parse(localStorage.getItem('favoriteProducts')) || [];
      if (favorites.length === 0) {
        showCustomAlert("Bạn chưa có sản phẩm yêu thích nào.", "info");
      } else {
        showCustomAlert(`Bạn có ${favorites.length} sản phẩm yêu thích!`, "success");
      }
    }

    function toggleFavorite(productId) {
      const button = event.target.closest('.favorite-btn');
      const icon = button.querySelector('i');
      const productName = button.closest('.card').querySelector('.card-title').textContent;

      if (favoriteProducts.includes(productId)) {
        // Remove from favorites
        favoriteProducts = favoriteProducts.filter(id => id !== productId);
        icon.classList.remove('bi-heart-fill', 'text-danger');
        icon.classList.add('bi-heart');
        button.classList.remove('active');

        showCustomAlert(`Đã bỏ yêu thích "${productName}"!`, "warning");
        showToast(`Đã bỏ yêu thích "${productName}"!`, "warning");
      } else {
        // Add to favorites
        favoriteProducts.push(productId);
        icon.classList.remove('bi-heart');
        icon.classList.add('bi-heart-fill', 'text-danger');
        button.classList.add('active');

        showCustomAlert(`Đã thêm "${productName}" vào danh sách yêu thích!`, "success");
        showToast(`Đã thêm "${productName}" vào danh sách yêu thích!`, "success");
      }

      localStorage.setItem('favoriteProducts', JSON.stringify(favoriteProducts));
      updateFavoriteCount();
    }

    function showToast(message, type = 'success') {
      const toast = document.getElementById('favoriteToast');
      const toastMessage = document.getElementById('toastMessage');

      // Update toast content
      toastMessage.textContent = message;

      // Update toast style based on type
      if (type === 'success') {
        toast.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
        toast.querySelector('i').className = 'bi bi-heart-fill me-2';
      } else if (type === 'warning') {
        toast.style.background = 'linear-gradient(135deg, #ffc107, #fd7e14)';
        toast.querySelector('i').className = 'bi bi-heart me-2';
      }

      // Show toast
      const bsToast = new bootstrap.Toast(toast);
      bsToast.show();
    }

    function showCustomAlert(message, type = 'success') {
      // Create custom alert container
      let alertContainer = document.getElementById('customAlertContainer');
      if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'customAlertContainer';
        alertContainer.style.cssText = `
          position: fixed;
          top: 20px;
          left: 50%;
          transform: translateX(-50%);
          z-index: 10000;
          max-width: 400px;
          width: 90%;
        `;
        document.body.appendChild(alertContainer);
      }

      // Create alert element
      const alert = document.createElement('div');
      alert.className = `alert alert-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info'} alert-dismissible fade show`;
      alert.style.cssText = `
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        border: none;
        margin-bottom: 10px;
        animation: slideDown 0.3s ease;
      `;

      alert.innerHTML = `
        <div class="d-flex align-items-center">
          <i class="bi ${type === 'success' ? 'bi-check-circle' : type === 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle'} me-2 fs-5"></i>
          <span class="fw-bold">${message}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;

      // Add to container
      alertContainer.appendChild(alert);

      // Auto remove after 3 seconds
      setTimeout(() => {
        if (alert.parentNode) {
          alert.remove();
        }
      }, 3000);
    }

    function updateFavoriteCount() {
      const countElement = document.getElementById('favorite-count');
      if (countElement) {
        countElement.innerText = favoriteProducts.length;
      }
    }

    function addToCart(id) {
      const productCard = event.target.closest('.card');
      const productName = productCard.querySelector('.card-title').textContent;

      fetch(`?act=cart-add&id=${id}`)
        .then(response => response.json())
        .then(data => {
          if (data.cartCount !== undefined) {
            document.querySelector('.bi-cart3 + span').innerText = data.cartCount;
            showCustomAlert(`Đã thêm "${productName}" vào giỏ hàng!`, "success");
          } else if (data.error) {
            showCustomAlert(`Lỗi: ${data.error}`, "warning");
          }
        })
        .catch(error => {
          console.error("Lỗi khi thêm giỏ hàng:", error);
          showCustomAlert("Không thể thêm sản phẩm vào giỏ hàng!", "warning");
        });
    }

    // Initialize on page load
    document.addEventListener("DOMContentLoaded", () => {
      // Initialize favorite buttons
      const favoriteButtons = document.querySelectorAll('.favorite-btn');
      favoriteButtons.forEach(button => {
        const productId = parseInt(button.getAttribute('data-id'));
        const icon = button.querySelector('i');

        if (favoriteProducts.includes(productId)) {
          icon.classList.remove('bi-heart');
          icon.classList.add('bi-heart-fill', 'text-danger');
          button.classList.add('active');
        }
      });

      // Update favorite count
      updateFavoriteCount();
    });

    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
      @keyframes slideDown {
        from {
          transform: translateX(-50%) translateY(-100%);
          opacity: 0;
        }
        to {
          transform: translateX(-50%) translateY(0);
          opacity: 1;
        }
      }
      
      .favorite-btn {
        transition: all 0.3s ease;
      }
      
      .favorite-btn:hover {
        transform: scale(1.1);
      }
      
      .favorite-btn.active {
        background: #dc3545 !important;
        color: white !important;
      }
      
      .favorite-btn.active i {
        color: white !important;
      }
    `;
    document.head.appendChild(style);
  </script>
</body>

</html>