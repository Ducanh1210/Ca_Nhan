<?php
/**
 * FILE: controllers/VoucherController.php
 * CHỨC NĂNG: Controller quản lý mã giảm giá (voucher) - xử lý logic nghiệp vụ liên quan đến voucher
 * LUỒNG CHẠY: 
 * - VÀO: Nhận request từ index.php với các action khác nhau (?act=voucher-...)
 * - RA: Gọi model để xử lý dữ liệu và require view để hiển thị giao diện
 * - NGƯỢC LẠI: Tương tác với model Voucher để thực hiện CRUD voucher
 */
class VoucherController
{
    /**
     * METHOD LIST - HIỂN THỊ DANH SÁCH VOUCHER (ADMIN)
     * Chức năng: Hiển thị trang danh sách tất cả mã giảm giá
     * Sử dụng: Trang quản lý voucher (admin)
     * Luồng: Lấy danh sách voucher → Hiển thị view
     */
    public function list()
    {
        $voucherModel = new Voucher();
        $vouchers = $voucherModel->all(); // Lấy tất cả voucher
        require_once './views/admin/voucher/vouchers.php'; // Hiển thị view
    }

    /**
     * METHOD ADD - THÊM VOUCHER MỚI (ADMIN)
     * Chức năng: Xử lý form thêm mã giảm giá mới
     * Sử dụng: Form thêm voucher trong admin
     * Luồng: Xử lý POST → Validate → Kiểm tra trùng mã → Thêm vào DB → Redirect
     */
    public function add()
    {
        $voucherModel = new Voucher();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');
            $discount_percent = (int) ($_POST['discount_percent'] ?? 0);
            $expiry_date = trim($_POST['expiry_date'] ?? '');
            $status = trim($_POST['status'] ?? 'active');
            $error = '';
            
            // Validate dữ liệu đầu vào
            if ($code === '' || $discount_percent <= 0 || $expiry_date === '') {
                $error = 'Vui lòng nhập đầy đủ thông tin và giảm giá > 0!';
            } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry_date)) {
                $error = 'Ngày hết hạn không hợp lệ! Định dạng đúng: yyyy-mm-dd';
            } elseif ($voucherModel->findByCode($code)) {
                $error = 'Mã voucher đã tồn tại, vui lòng nhập mã khác!';
            } else {
                $voucherModel->insert($code, $discount_percent, $expiry_date); // Thêm voucher mới
                header('Location: ?act=vouchers'); // Chuyển hướng về danh sách
                exit;
            }
        }
        require_once './views/admin/voucher/voucher_add.php'; // Hiển thị form thêm
    }

    /**
     * METHOD EDIT - SỬA VOUCHER (ADMIN)
     * Chức năng: Xử lý form sửa mã giảm giá
     * Sử dụng: Form edit voucher trong admin
     * Luồng: Lấy thông tin voucher → Xử lý POST → Validate → Kiểm tra trùng mã → Cập nhật → Redirect
     */
    public function edit()
    {
        $voucherModel = new Voucher();
        $id = $_GET['id'] ?? 0;
        $voucher = $voucherModel->findById($id); // Lấy thông tin voucher cần sửa
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');
            $discount_percent = (int) ($_POST['discount_percent'] ?? 0);
            $expiry_date = trim($_POST['expiry_date'] ?? '');
            $status = trim($_POST['status'] ?? 'active');
            $error = '';
            
            // Validate dữ liệu đầu vào
            if ($code === '' || $discount_percent <= 0 || $expiry_date === '') {
                $error = 'Vui lòng nhập đầy đủ thông tin và giảm giá > 0!';
            } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry_date)) {
                $error = 'Ngày hết hạn không hợp lệ! Định dạng đúng: yyyy-mm-dd';
            } else {
                $voucherExist = $voucherModel->findByCode($code);
                // Kiểm tra mã voucher đã tồn tại bởi user khác
                if ($voucherExist && $voucherExist['id'] != $id) {
                    $error = 'Mã voucher đã tồn tại, vui lòng nhập mã khác!';
                } else {
                    $voucherModel->update($id, $code, $discount_percent, $expiry_date, $status); // Cập nhật voucher
                    header('Location: ?act=vouchers'); // Chuyển hướng về danh sách
                    exit;
                }
            }
        }
        require_once './views/admin/voucher/voucher_edit.php'; // Hiển thị form edit
    }

    /**
     * METHOD DELETE - XÓA VOUCHER (ADMIN)
     * Chức năng: Xóa mã giảm giá theo ID
     * Sử dụng: Nút xóa voucher trong admin
     * Luồng: Lấy ID → Xóa voucher → Redirect về danh sách
     */
    public function delete()
    {
        $voucherModel = new Voucher();
        $id = $_GET['id'] ?? 0;
        $voucherModel->delete($id); // Xóa voucher
        header('Location: ?act=vouchers'); // Chuyển hướng về danh sách
        exit;
    }

    /**
     * METHOD CHECKOUT - XỬ LÝ VOUCHER TRONG CHECKOUT
     * Chức năng: Xử lý áp dụng mã giảm giá trong quá trình thanh toán
     * Sử dụng: Form áp dụng voucher trong trang checkout
     * Luồng: Nhận mã voucher → Tìm và validate → Lưu vào session → Thông báo → Redirect
     */
    public function checkout()
    {
        session_start();

        // Xử lý áp dụng mã giảm giá
        if (isset($_POST['apply_voucher'])) {
            $code = trim($_POST['voucher'] ?? '');
            if ($code !== '') {
                require_once './models/Voucher.php';
                $voucherModel = new Voucher();
                $voucher = $voucherModel->findByCode($code); // Tìm voucher theo mã

                if ($voucher) {
                    $_SESSION['voucher'] = $voucher; // Lưu voucher vào session
                    $_SESSION['voucher_success'] = 'Áp dụng mã thành công!';
                } else {
                    unset($_SESSION['voucher']); // Xóa voucher khỏi session nếu không hợp lệ
                    $_SESSION['voucher_error'] = 'Mã không hợp lệ hoặc đã hết hạn.';
                }
            }
            // Reload lại trang để hiển thị message
            header('Location: ?act=checkout');
            exit;
        }

        $cart = $_SESSION['cart'] ?? [];
        $voucher = $_SESSION['voucher'] ?? null;
        $voucher_message = $_SESSION['voucher_success'] ?? ($_SESSION['voucher_error'] ?? null);

        // Xóa message sau khi load 1 lần
        unset($_SESSION['voucher_success']);
        unset($_SESSION['voucher_error']);

        require_once './views/client/checkout.php'; // Hiển thị trang checkout
    }
}
?>