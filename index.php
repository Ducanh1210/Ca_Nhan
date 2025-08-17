<?php
/**
 * FILE: index.php
 * CHỨC NĂNG: Điểm khởi đầu của ứng dụng web - Router chính và khởi tạo hệ thống
 * LUỒNG CHẠY: 
 * - VÀO: Từ URL với tham số ?act= để xác định hành động cần thực hiện
 * - RA: Gọi controller tương ứng để xử lý request và hiển thị view
 * - NGƯỢC LẠI: Các controller xử lý logic và trả về kết quả cho user
 */

// CẤU HÌNH HIỂN THỊ LỖI ĐỂ DEBUG
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// KHỞI TẠO CÁC FILE CẦN THIẾT CHO HỆ THỐNG
// Commons - Cấu hình và hàm tiện ích
require_once('./commons/env.php');           // Cấu hình môi trường và database
require_once('./commons/function.php');      // Các hàm tiện ích chung

// Models - Lớp xử lý dữ liệu và tương tác với database
require_once('./models/Product.php');        // Model quản lý sản phẩm
require_once('./models/Categorie.php');      // Model quản lý danh mục
require_once('./models/Order.php');          // Model quản lý đơn hàng
require_once('./models/Variant.php');        // Model quản lý biến thể sản phẩm
require_once('./models/Voucher.php');        // Model quản lý mã giảm giá
require_once('./models/User.php');           // Model quản lý người dùng

// Controllers - Lớp điều khiển logic nghiệp vụ
require_once('./controllers/ProductController.php');    // Controller quản lý sản phẩm
require_once('./controllers/VoucherController.php');    // Controller quản lý voucher
require_once('./controllers/OrderController.php');      // Controller quản lý đơn hàng
require_once('./controllers/UserController.php');       // Controller quản lý người dùng
require_once('./controllers/CartController.php');       // Controller quản lý giỏ hàng
require_once('./controllers/CategoryController.php');   // Controller quản lý danh mục

// ROUTING SYSTEM - HỆ THỐNG ĐỊNH TUYẾN CHÍNH
// Lấy action từ URL parameter, mặc định là 'home-client'
$act = $_GET['act'] ?? 'home-client';

// SỬ DỤNG MATCH EXPRESSION ĐỂ ROUTING (PHP 8.0+)
match ($act) {
    // ===== QUẢN LÝ VOUCHER (ADMIN) =====
    'vouchers' => (new VoucherController())->list(),        // Hiển thị danh sách tất cả voucher (trang admin)
    'voucher-add' => (new VoucherController())->add(),      // Thêm voucher mới (hiển thị form + xử lý POST)
    'voucher-edit' => (new VoucherController())->edit(),    // Sửa voucher theo ID (?id=) (GET để hiển thị form, POST để lưu)
    'voucher-delete' => (new VoucherController())->delete(), // Xóa voucher theo ID (?id=)

    // ===== CLIENT - GIAO DIỆN NGƯỜI DÙNG =====
    'home-client' => (new ProductController())->client(),   // Trang chủ client - hiển thị sản phẩm nổi bật
    'sp-clients' => (new ProductController())->clients(),   // Trang danh sách sản phẩm client - tất cả sản phẩm

    // ===== ADMIN - GIAO DIỆN QUẢN TRỊ =====
    'dashboard' => (new ProductController())->dashboard(),  // Trang dashboard admin - tổng quan hệ thống
    'p-list' => (new ProductController())->list(),          // Danh sách sản phẩm (admin)
    'p-add' => (new ProductController())->add(),            // Thêm sản phẩm mới (admin)
    'p-edit' => (new ProductController())->edit($_GET['id'] ?? 0), // Sửa sản phẩm theo id (admin)
    'p-show' => (new ProductController())->show($_GET['id'] ?? 0), // Xem chi tiết sản phẩm (admin)
    'p-delete' => (new ProductController())->delete($_GET['id'] ?? 0), // Xóa sản phẩm theo id (admin)

    // ===== QUẢN LÝ DANH MỤC (ADMIN) =====
    'category-list' => (new CategoryController())->list(),   // Danh sách danh mục
    'category-add' => (new CategoryController())->add(),     // Thêm danh mục mới
    'category-edit' => (new CategoryController())->edit(),   // Sửa danh mục
    'category-delete' => (new CategoryController())->delete(), // Xóa danh mục

    // ===== QUẢN LÝ BIẾN THỂ SẢN PHẨM (ADMIN) =====
    'variant-add' => (new ProductController())->addVariants(),    // Thêm biến thể từ list (modal)
    'variant-delete' => (new ProductController())->deleteVariant(), // Xóa biến thể theo id

    // ===== QUẢN LÝ ĐƠN HÀNG (ADMIN) =====
    'orders' => (new OrderController())->list(),             // Danh sách đơn hàng (admin)
    'order-show' => (new OrderController())->show(),         // Xem chi tiết đơn hàng (admin)
    'order-debug' => (new OrderController())->debug(),       // Debug đơn hàng (admin)

    // ===== QUẢN LÝ NGƯỜI DÙNG =====
    'register' => (new UserController())->register(),        // Đăng ký tài khoản mới
    'login' => (new UserController())->login(),              // Đăng nhập vào hệ thống
    'logout' => (new UserController())->logout(),            // Đăng xuất khỏi hệ thống
    'users' => (new UserController())->list(),               // Danh sách người dùng (admin)
    'user-add' => (new UserController())->add(),             // Thêm người dùng mới (admin)
    'user-edit' => (new UserController())->edit(),           // Sửa thông tin người dùng (admin)
    'checkout' => (new UserController())->checkout(),        // Trang thanh toán đơn hàng
    'orders-client' => (new UserController())->orders(),     // Xem đơn hàng của người dùng đã đăng nhập

    // ===== QUẢN LÝ GIỎ HÀNG =====
    'cart-add' => (new CartController())->add(),             // Thêm sản phẩm vào giỏ hàng
    'cart-view' => (new CartController())->view(),           // Xem nội dung giỏ hàng
    'cart-remove' => (new CartController())->remove(),       // Xóa sản phẩm khỏi giỏ hàng
    'cart-update' => (new CartController())->update(),       // Cập nhật số lượng sản phẩm trong giỏ
    'buy-now' => (new CartController())->buyNow(),           // Xử lý Mua Ngay và chuyển tới thanh toán

    // ===== CHI TIẾT SẢN PHẨM =====
    'product-detail' => (new ProductController())->detail(), // Hiển thị chi tiết sản phẩm

    // ===== QUẢN TRỊ ADMIN =====
    'admin' => (new UserController())->adminLogout(),        // Trang quản trị admin

    // ===== XỬ LÝ ĐƠN HÀNG =====
    'order-update-status' => (new OrderController())->updateStatus(), // Cập nhật trạng thái đơn hàng (admin)
    'cancel-order' => (new OrderController())->cancelOrder(), // Hủy đơn hàng của người dùng

    // ===== TRƯỜNG HỢP MẶC ĐỊNH =====
    default => notFound(),                                   // Hiển thị trang 404 khi không tìm thấy route
}

    ?>