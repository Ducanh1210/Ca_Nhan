<?php
/**
 * FILE: controllers/OrderController.php
 * CHỨC NĂNG: Controller quản lý đơn hàng - xử lý logic nghiệp vụ liên quan đến đơn hàng
 * LUỒNG CHẠY: 
 * - VÀO: Nhận request từ index.php với các action khác nhau (?act=order-...)
 * - RA: Gọi model để xử lý dữ liệu và require view để hiển thị giao diện
 * - NGƯỢC LẠI: Tương tác với model Order để thực hiện CRUD đơn hàng
 */
class OrderController
{
    /**
     * METHOD LIST - HIỂN THỊ DANH SÁCH ĐƠN HÀNG (ADMIN)
     * Chức năng: Hiển thị trang danh sách tất cả đơn hàng cho admin
     * Sử dụng: Trang quản lý đơn hàng (admin)
     * Luồng: Kiểm tra quyền admin → Lấy danh sách đơn hàng → Hiển thị view
     */
    public function list()
    {
        session_start();
        // Kiểm tra quyền admin
        if (!isset($_SESSION['admin'])) {
            header('Location: ?act=login');
            exit;
        }

        $orderModel = new Order();
        $orders = $orderModel->all(); // Lấy tất cả đơn hàng
        require_once './views/admin/orders/list.php'; // Hiển thị view
    }

    /**
     * METHOD DELETE - XÓA ĐƠN HÀNG (BỊ VÔ HIỆU)
     * Chức năng: Chức năng xóa đơn hàng đã bị vô hiệu hóa
     * Sử dụng: Nút xóa đơn hàng trong admin (không hoạt động)
     * Luồng: Hiển thị thông báo vô hiệu → Redirect về danh sách
     */
    public function delete()
    {
        session_start();
        // Kiểm tra quyền admin
        if (!isset($_SESSION['admin'])) {
            header('Location: ?act=login');
            exit;
        }

        // Chức năng xóa bị vô hiệu theo yêu cầu
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Chức năng xóa đơn hàng đã bị vô hiệu.'];
        header('Location: ?act=orders');
        exit;
    }

    /**
     * METHOD UPDATESTATUS - CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG
     * Chức năng: Cập nhật trạng thái của đơn hàng
     * Sử dụng: Form cập nhật trạng thái trong admin
     * Luồng: Kiểm tra quyền → Validate dữ liệu → Cập nhật → Thông báo → Redirect
     */
    public function updateStatus()
    {
        session_start();
        // Kiểm tra quyền admin
        if (!isset($_SESSION['admin'])) {
            header('Location: ?act=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'], $_POST['status'])) {
            $id = (int) $_GET['id'];
            $status = trim($_POST['status']);

            // Validate status - chỉ cho phép các trạng thái hợp lệ
            $valid_statuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
            if (!in_array($status, $valid_statuses)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Trạng thái không hợp lệ!'];
                header('Location: ?act=orders');
                exit;
            }

            $orderModel = new Order();
            $result = $orderModel->updateStatus($id, $status); // Cập nhật trạng thái

            if ($result) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Cập nhật trạng thái đơn hàng thành công!'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Không thể cập nhật trạng thái: ' . $orderModel->getLastError()];
            }
        } else {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Thiếu thông tin cập nhật!'];
        }

        header('Location: ?act=orders'); // Chuyển hướng về danh sách
        exit;
    }

    /**
     * METHOD SHOW - HIỂN THỊ CHI TIẾT ĐƠN HÀNG (ADMIN)
     * Chức năng: Hiển thị trang chi tiết một đơn hàng cụ thể
     * Sử dụng: Nút "Xem chi tiết" trong danh sách đơn hàng
     * Luồng: Kiểm tra quyền → Validate ID → Lấy thông tin đơn hàng → Hiển thị view
     */
    public function show()
    {
        session_start();
        // Kiểm tra quyền admin
        if (!isset($_SESSION['admin'])) {
            header('Location: ?act=login');
            exit;
        }

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Thiếu mã đơn hàng.'];
            header('Location: ?act=orders');
            exit;
        }

        $orderModel = new Order();

        // Debug: Kiểm tra kết nối database
        if (!$orderModel->conn) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Lỗi kết nối database.'];
            header('Location: ?act=orders');
            exit;
        }

        $order = $orderModel->find($id); // Tìm đơn hàng theo ID

        if (!$order) {
            // Kiểm tra xem có phải lỗi database không
            $error = $orderModel->getLastError();
            if ($error) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Lỗi database: ' . $error];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Không tìm thấy đơn hàng #' . $id . '. Có thể đơn hàng đã bị xóa hoặc không tồn tại.'];
            }
            header('Location: ?act=orders');
            exit;
        }

        // Bổ sung thông tin người dùng cho view
        if (!empty($order['user_id'])) {
            $userModel = new User();
            $user = $userModel->find((int) $order['user_id']);
            if ($user) {
                $order['user_name'] = $user['name'] ?? '';
                $order['user_email'] = $user['email'] ?? '';
            }
        }

        // Lấy chi tiết sản phẩm trong đơn hàng
        $items = $orderModel->getOrderItems($id);

        // Debug: Kiểm tra dữ liệu
        if (empty($items)) {
            // Nếu không có items, có thể đơn hàng bị lỗi
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Đơn hàng #' . $id . ' không có sản phẩm nào.'];
        }

        // Debug: Log dữ liệu để kiểm tra
        error_log("OrderController::show() - Order ID: " . $id);
        error_log("OrderController::show() - Order data: " . print_r($order, true));
        error_log("OrderController::show() - Items count: " . count($items));

        require_once './views/admin/orders/detail.php'; // Hiển thị view chi tiết
    }

    /**
     * METHOD DEBUG - DEBUG DATABASE (CHỈ DÀNH CHO ADMIN)
     * Chức năng: Hiển thị thông tin debug về database và đơn hàng
     * Sử dụng: Nút debug trong admin để kiểm tra hệ thống
     * Luồng: Kiểm tra quyền → Kiểm tra kết nối → Hiển thị thông tin debug
     */
    public function debug()
    {
        session_start();
        // Kiểm tra quyền admin
        if (!isset($_SESSION['admin'])) {
            header('Location: ?act=login');
            exit;
        }

        $orderModel = new Order();

        // Kiểm tra kết nối database
        $dbStatus = $orderModel->conn ? 'OK' : 'FAILED';

        // Lấy tất cả đơn hàng
        $allOrders = $orderModel->all();

        // Lấy tổng số đơn hàng
        $totalOrders = $orderModel->countAll();

        // Debug info
        echo "<h2>Debug Information</h2>";
        echo "<p><strong>Database Connection:</strong> " . $dbStatus . "</p>";
        echo "<p><strong>Total Orders:</strong> " . $totalOrders . "</p>";
        echo "<p><strong>All Order IDs:</strong> ";
        if (!empty($allOrders)) {
            $ids = array_column($allOrders, 'id');
            echo implode(', ', $ids);
        } else {
            echo "None";
        }
        echo "</p>";

        // Kiểm tra đơn hàng cụ thể nếu có ID
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            echo "<p><strong>Checking Order #" . $id . ":</strong></p>";

            $order = $orderModel->find($id);
            if ($order) {
                echo "<p>✅ Order #" . $id . " exists</p>";
                echo "<pre>" . print_r($order, true) . "</pre>";
            } else {
                echo "<p>❌ Order #" . $id . " NOT found</p>";
                echo "<p>Error: " . $orderModel->getLastError() . "</p>";
            }
        }

        echo "<p><a href='?act=orders'>← Quay lại danh sách đơn hàng</a></p>";
        exit;
    }

    /**
     * METHOD CANCELORDER - HỦY ĐƠN HÀNG (CLIENT)
     * Chức năng: Cho phép user hủy đơn hàng của mình
     * Sử dụng: Nút "Hủy đơn" trong trang lịch sử đơn hàng của user
     * Luồng: Kiểm tra đăng nhập → Validate đơn hàng → Kiểm tra quyền → Hủy → Thông báo
     */
    public function cancelOrder()
    {
        session_start();
        // ✅ Nếu chưa đăng nhập thì chuyển hướng về login
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            header('Location: ?act=login&message=not_logged_in');
            exit;
        }

        $order_id = $_GET['id'] ?? 0;
        $user_id = $_SESSION['user']['id'] ?? 0;

        if (!$order_id || !$user_id) {
            $_SESSION['cancel_error'] = 'Thiếu thông tin đơn hàng hoặc người dùng.';
            header('Location: ?act=orders-client');
            exit;
        }

        $orderModel = new Order();
        $order = $orderModel->find($order_id); // Tìm đơn hàng

        // Kiểm tra đơn hàng có tồn tại không
        if (!$order) {
            $_SESSION['cancel_error'] = 'Không tìm thấy đơn hàng #' . $order_id;
            header('Location: ?act=orders-client');
            exit;
        }

        // Kiểm tra đơn hàng có thuộc về user hiện tại không
        if ($order['user_id'] != $user_id) {
            $_SESSION['cancel_error'] = 'Bạn không có quyền hủy đơn hàng này.';
            header('Location: ?act=orders-client');
            exit;
        }

        // Kiểm tra trạng thái đơn hàng - chỉ cho phép hủy đơn đang xử lý
        if ($order['status'] !== 'pending') {
            $status_text = $this->getStatusText($order['status']);
            $_SESSION['cancel_error'] = 'Không thể hủy đơn hàng ở trạng thái: ' . $status_text . '. Chỉ có thể hủy đơn hàng đang xử lý.';
            header('Location: ?act=orders-client');
            exit;
        }

        // Thực hiện hủy đơn hàng
        $result = $orderModel->updateStatus($order_id, 'cancelled');

        if ($result) {
            $_SESSION['cancel_success'] = 'Hủy đơn hàng #' . $order_id . ' thành công!';
        } else {
            $_SESSION['cancel_error'] = 'Không thể hủy đơn hàng: ' . $orderModel->getLastError();
        }

        header('Location: ?act=orders-client'); // Chuyển hướng về trang đơn hàng
        exit;
    }

    /**
     * METHOD GETSTATUSTEXT - HÀM HELPER CHUYỂN ĐỔI TRẠNG THÁI THÀNH TEXT TIẾNG VIỆT
     * Chức năng: Chuyển đổi mã trạng thái thành text dễ hiểu
     * Sử dụng: Hiển thị thông báo lỗi khi hủy đơn hàng
     * Tham số: $status - mã trạng thái
     * Trả về: Text tiếng Việt tương ứng
     */
    private function getStatusText($status)
    {
        $status_map = [
            'pending' => 'Đang xử lý',
            'confirmed' => 'Đã xử lý',
            'shipped' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy'
        ];

        return $status_map[$status] ?? $status;
    }
}
