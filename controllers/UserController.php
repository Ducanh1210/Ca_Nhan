<?php
/**
 * FILE: controllers/UserController.php
 * CHỨC NĂNG: Controller quản lý người dùng - xử lý logic nghiệp vụ liên quan đến user
 * LUỒNG CHẠY: 
 * - VÀO: Nhận request từ index.php với các action khác nhau (?act=user-...)
 * - RA: Gọi model để xử lý dữ liệu và require view để hiển thị giao diện
 * - NGƯỢC LẠI: Tương tác với model User để thực hiện CRUD user
 */
class UserController
{
    /**
     * METHOD REGISTER - ĐĂNG KÝ TÀI KHOẢN MỚI
     * Chức năng: Xử lý form đăng ký tài khoản mới
     * Sử dụng: Form đăng ký trong trang client
     * Luồng: Nhận dữ liệu POST → Gọi model register → Redirect về login
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            try {
                (new User())->register($name, $email, $password); // Gọi model để đăng ký
                header('Location: ?act=login'); // Chuyển hướng về trang đăng nhập
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        require_once './views/client/register.php'; // Hiển thị form đăng ký
    }

    /**
     * METHOD LOGIN - ĐĂNG NHẬP HỆ THỐNG
     * Chức năng: Xử lý form đăng nhập và phân quyền user/admin
     * Sử dụng: Form đăng nhập trong trang client
     * Luồng: Nhận dữ liệu POST → Xác thực → Phân quyền → Redirect theo vai trò
     */
    public function login()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $userModel = new User();
            $user = $userModel->findByEmail($email); // Tìm user theo email

            if ($user && password_verify($password, $user['password'])) {
                // Nếu là admin → chuyển về trang quản trị
                if ($user['role'] === 'admin') {
                    $_SESSION['admin'] = $user;
                    header('Location: ?act=dashboard');
                    exit;
                } else {
                    // Ngược lại, là user → về trang client hoặc trang dự định
                    $_SESSION['user'] = $user;
                    $redirect = $_SESSION['redirect_to'] ?? '';
                    unset($_SESSION['redirect_to']);
                    if (!empty($redirect)) {
                        header('Location: ' . $redirect);
                    } else {
                        header('Location: ?act=home-client');
                    }
                    exit;
                }
            } else {
                $error = 'Sai email hoặc mật khẩu!';
            }
        }

        require_once './views/client/login.php'; // Hiển thị form đăng nhập
    }

    /**
     * METHOD ADMINLOGOUT - ĐĂNG XUẤT ADMIN
     * Chức năng: Đăng xuất tài khoản admin
     * Sử dụng: Nút đăng xuất trong trang admin
     * Luồng: Xóa session admin → Redirect về trang login
     */
    public function adminLogout()
    {
        session_start();
        unset($_SESSION['admin']); // Xóa session admin
        header('Location: ?act=login');
        exit;
    }

    /**
     * METHOD LOGOUT - ĐĂNG XUẤT TẤT CẢ
     * Chức năng: Đăng xuất tất cả tài khoản (user và admin)
     * Sử dụng: Nút đăng xuất trong trang client
     * Luồng: Xóa tất cả session → Destroy session → Redirect về login
     */
    public function logout()
    {
        session_start();
        unset($_SESSION['user']); // Xóa session user
        unset($_SESSION['admin']); // Xóa session admin
        session_destroy(); // Hủy hoàn toàn session
        header('Location: ?act=login');
        exit;
    }

    /**
     * METHOD LIST - HIỂN THỊ DANH SÁCH USER (ADMIN)
     * Chức năng: Hiển thị trang danh sách tất cả user cho admin
     * Sử dụng: Trang quản lý user (admin)
     * Luồng: Kiểm tra quyền admin → Lấy danh sách user → Hiển thị view
     */
    public function list()
    {
        if (!isset($_SESSION))
            session_start();
        // Kiểm tra quyền admin
        if (!isset($_SESSION['admin'])) {
            header('Location: ?act=login');
            exit;
        }

        $users = (new User())->all(); // Lấy tất cả user
        require_once './views/admin/users/list.php'; // Hiển thị view
    }

    /**
     * METHOD ADD - THÊM USER MỚI (ADMIN)
     * Chức năng: Admin thêm user mới với vai trò cụ thể
     * Sử dụng: Form thêm user trong admin
     * Luồng: Kiểm tra quyền → Xử lý POST → Validate → Thêm vào DB → Redirect
     */
    public function add()
    {
        if (!isset($_SESSION))
            session_start();
        // Kiểm tra quyền admin
        if (!isset($_SESSION['admin'])) {
            header('Location: ?act=login');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] === 'admin' ? 'admin' : 'user';
            $password = trim($_POST['password'] ?? '');

            if ($name === '' || $email === '') {
                $error = 'Vui lòng nhập tên và email';
            } elseif ((new User())->findByEmail($email)) {
                $error = 'Email đã tồn tại';
            } else {
                if ($password === '') {
                    $password = '123456'; // Mật khẩu mặc định
                }
                (new User())->insertWithRole($name, $email, $password, $role); // Thêm user với vai trò
                header('Location: ?act=users'); // Chuyển hướng về danh sách
                exit;
            }
        }

        require_once './views/admin/users/add.php'; // Hiển thị form thêm
    }

    /**
     * METHOD EDIT - SỬA USER (ADMIN)
     * Chức năng: Admin sửa thông tin user (tên, email, vai trò)
     * Sử dụng: Form edit user trong admin
     * Luồng: Kiểm tra quyền → Lấy thông tin user → Xử lý POST → Validate → Cập nhật → Redirect
     */
    public function edit()
    {
        if (!isset($_SESSION))
            session_start();
        // Kiểm tra quyền admin
        if (!isset($_SESSION['admin'])) {
            header('Location: ?act=login');
            exit;
        }

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ?act=users');
            exit;
        }

        $userModel = new User();
        $user = $userModel->find($id); // Lấy thông tin user
        if (!$user) {
            header('Location: ?act=users');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] === 'admin' ? 'admin' : 'user';

            if ($name === '' || $email === '') {
                $error = 'Vui lòng nhập tên và email';
            } elseif ($userModel->existsEmailExceptId($email, $id)) {
                $error = 'Email đã được sử dụng bởi người dùng khác';
            } else {
                $userModel->updateBasicRole($id, $name, $email, $role); // Cập nhật thông tin user
                header('Location: ?act=users'); // Chuyển hướng về danh sách
                exit;
            }
        }

        require_once './views/admin/users/edit.php'; // Hiển thị form edit
    }

    /**
     * METHOD CHECKOUT - XỬ LÝ THANH TOÁN
     * Chức năng: Xử lý quá trình thanh toán và tạo đơn hàng
     * Sử dụng: Form thanh toán trong trang checkout
     * Luồng: Xử lý voucher → Xử lý thanh toán → Tạo đơn hàng → Dọn session → Redirect
     */
    public function checkout()
    {
        session_start();
        // Cho phép xem giỏ và điền form khi CHƯA đăng nhập; chỉ yêu cầu đăng nhập khi bấm Thanh toán
        $cart = $_SESSION['cart'] ?? [];
        $user = $_SESSION['user'] ?? null;

        // Không chuyển hướng nếu giỏ hàng trống để dùng chung trang (hiển thị cảnh báo trong view)

        $discount = 0;
        $discount_percent = 0;
        $total = 0;
        $voucher = $_SESSION['voucher'] ?? null;
        $success_message = '';
        $error_message = '';
        $voucher_message = '';

        // Tính tổng tiền
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // ===== 1. ÁP DỤNG MÃ GIẢM GIÁ =====
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_voucher'])) {
            // Đồng bộ số lượng từ form (nếu có) trước khi áp dụng voucher
            if (isset($_POST['items']) && is_array($_POST['items'])) {
                foreach ($_POST['items'] as $item) {
                    $id = $item['id'] ?? 0;
                    $quantity = max(1, intval($item['quantity'] ?? 1));
                    foreach ($_SESSION['cart'] as &$cartItem) {
                        if ((int) $cartItem['id'] === (int) $id) {
                            $cartItem['quantity'] = $quantity;
                            break;
                        }
                    }
                }
                // Cập nhật lại nguồn dữ liệu và tổng tiền sau đồng bộ
                $cart = $_SESSION['cart'] ?? [];
                $total = 0;
                foreach ($cart as $item) {
                    $total += $item['price'] * $item['quantity'];
                }
            }
            $code = trim($_POST['voucher'] ?? '');

            if (empty($code)) {
                $error_message = "Vui lòng nhập mã giảm giá!";
            } else {
                $voucherModel = new Voucher();
                $voucher = $voucherModel->findByCode($code); // Tìm voucher theo mã

                if ($voucher && $voucher['status'] == 'active') {
                    $_SESSION['voucher'] = $voucher;
                    $success_message = "Áp dụng mã giảm giá thành công! Giảm " . $voucher['discount_percent'] . "%";
                    $voucher_message = "Mã giảm giá: " . $code . " - Giảm " . $voucher['discount_percent'] . "%";
                } else {
                    unset($_SESSION['voucher']);
                    $error_message = "Mã giảm giá không hợp lệ hoặc đã hết hạn!";
                }
            }

            // Lưu tạm dữ liệu form (nếu đã nhập)
            $_SESSION['checkout_form'] = [
                'name' => $_POST['name'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address' => $_POST['address'] ?? '',
                'note' => $_POST['note'] ?? ''
            ];

            // Chuyển hướng với thông báo
            if (!empty($success_message)) {
                $_SESSION['checkout_success'] = $success_message;
            }
            if (!empty($error_message)) {
                $_SESSION['checkout_error'] = $error_message;
            }
            header('Location: ?act=checkout');
            exit;
        }

        // ===== 2. THANH TOÁN =====
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
            // Nếu chưa đăng nhập → chuyển tới login sau khi lưu form và ý định quay lại checkout
            if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
                $_SESSION['checkout_form'] = [
                    'name' => $_POST['name'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'address' => $_POST['address'] ?? '',
                    'note' => $_POST['note'] ?? ''
                ];
                $_SESSION['redirect_to'] = '?act=checkout';
                $_SESSION['checkout_error'] = 'Vui lòng đăng nhập để thanh toán.';
                header('Location: ?act=login');
                exit;
            }
            // Đồng bộ số lượng từ form (nếu có) trước khi tạo đơn
            if (isset($_POST['items']) && is_array($_POST['items'])) {
                foreach ($_POST['items'] as $item) {
                    $id = $item['id'] ?? 0;
                    $quantity = max(1, intval($item['quantity'] ?? 1));
                    foreach ($_SESSION['cart'] as &$cartItem) {
                        if ((int) $cartItem['id'] === (int) $id) {
                            $cartItem['quantity'] = $quantity;
                            break;
                        }
                    }
                }
            }

            // Làm mới dữ liệu từ session sau khi đồng bộ
            $cart = $_SESSION['cart'] ?? [];
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            // Tính toán giảm giá
            $discount_percent = 0;
            $discount = 0;
            $voucher_id = null;

            if (isset($_SESSION['voucher']) && !empty($_SESSION['voucher'])) {
                $voucher_id = $_SESSION['voucher']['id'];
                $discount_percent = (int) ($_SESSION['voucher']['discount_percent'] ?? 0);
                $discount = round($total * $discount_percent / 100);
            }

            $total_payment = max(0, $total - $discount);

            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $note = trim($_POST['note'] ?? '');

            // Validate cơ bản
            if ($name === '' || $phone === '' || $address === '') {
                $_SESSION['checkout_error'] = 'Vui lòng điền đủ thông tin bắt buộc!';
                header('Location: ?act=checkout');
                exit;
            }

            // Validate số điện thoại
            if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
                $_SESSION['checkout_error'] = 'Số điện thoại không hợp lệ!';
                header('Location: ?act=checkout');
                exit;
            }

            try {
                $orderModel = new Order();
                $order_id = $orderModel->createOrder( // Tạo đơn hàng mới
                    $user['id'],
                    $note,
                    $address,
                    $phone,
                    $total, // total_price (giá gốc)
                    $discount_percent,
                    $total_payment, // total (sau khi giảm giá)
                    $voucher_id
                );

                if (!$order_id) {
                    $_SESSION['checkout_error'] = 'Không thể tạo đơn hàng. ' . ($orderModel->getLastError() ?: 'Vui lòng thử lại.');
                    header('Location: ?act=checkout');
                    exit;
                }

                // Thêm từng sản phẩm vào đơn hàng
                $all_items_added = true;
                foreach ($cart as $item) {
                    $result = $orderModel->addOrderItem( // Thêm sản phẩm vào đơn hàng
                        $order_id,
                        $item['id'],
                        $item['price'],
                        $item['quantity'],
                        $item['variant_ids'] ?? ''
                    );

                    if (!$result) {
                        $all_items_added = false;
                        break;
                    }
                }

                if (!$all_items_added) {
                    // Nếu có lỗi khi thêm sản phẩm, xóa đơn hàng
                    $orderModel->delete($order_id);
                    $_SESSION['checkout_error'] = 'Không thể thêm sản phẩm vào đơn hàng. ' . $orderModel->getLastError();
                    header('Location: ?act=checkout');
                    exit;
                }

                // Đặt hàng thành công - dọn sạch session
                unset($_SESSION['voucher'], $_SESSION['cart'], $_SESSION['checkout_form']);
                $_SESSION['order_success'] = 'Đặt hàng thành công! Mã đơn hàng: #' . $order_id;

                header('Location: ?act=orders-client'); // Chuyển hướng về trang đơn hàng
                exit;

            } catch (Exception $e) {
                $_SESSION['checkout_error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
                header('Location: ?act=checkout');
                exit;
            }
        }

        // ===== 3. HIỂN THỊ FORM =====

        // Lấy thông báo từ session
        $success_message = $_SESSION['checkout_success'] ?? '';
        $error_message = $_SESSION['checkout_error'] ?? '';
        unset($_SESSION['checkout_success'], $_SESSION['checkout_error']);

        // Tính toán giảm giá để hiển thị
        if (isset($_SESSION['voucher']) && !empty($_SESSION['voucher'])) {
            $voucher = $_SESSION['voucher'];
            $discount_percent = $_SESSION['voucher']['discount_percent'];
            $discount = round($total * $discount_percent / 100);
            $voucher_message = "Mã giảm giá: " . $voucher['code'] . " - Giảm " . $discount_percent . "%";
        }

        require './views/client/checkout.php'; // Hiển thị trang checkout
    }

    /**
     * METHOD ORDERS - HIỂN THỊ ĐƠN HÀNG CỦA USER
     * Chức năng: Hiển thị trang lịch sử đơn hàng của user đã đăng nhập
     * Sử dụng: Trang lịch sử đơn hàng của user
     * Luồng: Lấy user ID → Lấy đơn hàng của user → Hiển thị view
     */
    public function orders()
    {
        if (!isset($_SESSION))
            session_start();

        $userId = $_SESSION['user']['id'] ?? 0;
        $orders = [];

        if ($userId) {
            $orders = (new Order())->getOrdersByUserIdWithDetails($userId); // Lấy đơn hàng của user
        }

        require_once './views/client/orders.php'; // Hiển thị view đơn hàng
    }
}
