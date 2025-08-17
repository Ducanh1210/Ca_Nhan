<?php
/**
 * FILE: models/Order.php
 * CHỨC NĂNG: Model quản lý đơn hàng - xử lý tương tác với bảng orders và order_details trong database
 * LUỒNG CHẠY: 
 * - VÀO: Được gọi từ OrderController và UserController để quản lý đơn hàng
 * - RA: Trả về dữ liệu đơn hàng cho controller để hiển thị và xử lý
 * - NGƯỢC LẠI: Controller gọi các method này để thực hiện CRUD đơn hàng
 */

class Order
{
    public $conn; // Biến kết nối database
    public $lastError = ''; // Biến lưu lỗi cuối cùng

    /**
     * CONSTRUCTOR - KHỞI TẠO KẾT NỐI DATABASE
     * Chức năng: Tạo kết nối PDO khi khởi tạo object Order
     */
    public function __construct()
    {
        $this->conn = connectDB(); // Gọi hàm connectDB() từ function.php
    }

    /**
     * METHOD CREATEORDER - TẠO ĐƠN HÀNG MỚI
     * Chức năng: Tạo đơn hàng mới và trả về ID đơn hàng
     * Sử dụng: Quá trình checkout khi user đặt hàng
     * Tham số: Các thông tin cơ bản của đơn hàng
     * Trả về: ID đơn hàng mới hoặc false nếu thất bại
     */
    public function createOrder(
        $user_id,
        $note,
        $address,
        $phone,
        $total_price,
        $discount_percent,
        $total,
        $voucher_id = null
    ) {
        try {
            $this->conn->beginTransaction(); // Bắt đầu transaction

            $sql = "INSERT INTO orders (user_id, note, shipping_address, phone, total, total_price, discount_percent, created_at, status, voucher_id)
                            VALUES (:user_id, :note, :address, :phone, :total, :total_price, :discount_percent, NOW(), 'pending', :voucher_id)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':note' => $note,
                ':address' => $address,
                ':phone' => $phone,
                ':total' => $total,
                ':total_price' => $total_price,
                ':discount_percent' => $discount_percent,
                ':voucher_id' => $voucher_id
            ]);

            $order_id = $this->conn->lastInsertId(); // Lấy ID đơn hàng vừa tạo
            $this->conn->commit(); // Commit transaction
            return $order_id;

        } catch (PDOException $e) {
            $this->conn->rollBack(); // Rollback nếu có lỗi
            $this->lastError = "Lỗi tạo đơn hàng: " . $e->getMessage();
            return false;
        }
    }

    /**
     * METHOD ADDORDERITEM - THÊM SẢN PHẨM VÀO ĐƠN HÀNG
     * Chức năng: Thêm sản phẩm vào bảng order_details
     * Sử dụng: Sau khi tạo đơn hàng, thêm từng sản phẩm
     * Tham số: $order_id, $product_id, $price, $quantity, $variant_ids
     * Trả về: true nếu thành công, false nếu thất bại
     */
    public function addOrderItem($order_id, $product_id, $price, $quantity, $variant_ids = '')
    {
        try {
            $sql = "INSERT INTO order_details (order_id, product_id, price, quantity, variant_ids) 
                        VALUES (:order_id, :product_id, :price, :quantity, :variant_ids)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $product_id,
                ':price' => $price,
                ':quantity' => $quantity,
                ':variant_ids' => $variant_ids
            ]);

            if (!$result) {
                $this->lastError = "Không thể thêm sản phẩm vào đơn hàng";
                return false;
            }
            return true;

        } catch (PDOException $e) {
            $this->lastError = "Lỗi thêm sản phẩm vào đơn hàng: " . $e->getMessage();
            return false;
        }
    }

    /**
     * METHOD GETORDERSBYUSER - LẤY ĐƠN HÀNG CỦA USER (DẠNG NGẮN)
     * Chức năng: Lấy danh sách đơn hàng của một user cụ thể
     * Sử dụng: Trang lịch sử đơn hàng của user
     * Tham số: $user_id - ID của user
     * Trả về: Mảng các đơn hàng với thông tin tóm tắt
     */
    public function getOrdersByUser($user_id)
    {
        try {
            $sql = "SELECT o.*, 
                           COUNT(oi.id) as total_items,
                           SUM(oi.price * oi.quantity) as total_amount
                    FROM orders o
                    LEFT JOIN order_details oi ON o.id = oi.order_id
                    WHERE o.user_id = :user_id 
                    GROUP BY o.id
                    ORDER BY o.id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':user_id' => $user_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->lastError = "Lỗi lấy đơn hàng: " . $e->getMessage();
            return [];
        }
    }

    /**
     * METHOD GETORDERITEMS - LẤY CHI TIẾT SẢN PHẨM CỦA 1 ĐƠN HÀNG
     * Chức năng: Lấy danh sách sản phẩm trong một đơn hàng cụ thể
     * Sử dụng: Trang chi tiết đơn hàng (admin)
     * Tham số: $order_id - ID đơn hàng
     * Trả về: Mảng các sản phẩm trong đơn hàng
     */
    public function getOrderItems($order_id)
    {
        try {
            $sql = "SELECT oi.*, p.name as product_name, p.img as product_img 
                        FROM order_details oi 
                        JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = :order_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':order_id' => $order_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->lastError = "Lỗi lấy sản phẩm đơn hàng: " . $e->getMessage();
            return [];
        }
    }

    /**
     * METHOD ALL - ADMIN XEM TẤT CẢ ĐƠN HÀNG
     * Chức năng: Lấy danh sách tất cả đơn hàng với thông tin user và sản phẩm
     * Sử dụng: Trang quản lý đơn hàng (admin)
     * Trả về: Mảng tất cả đơn hàng với thông tin chi tiết
     */
    public function all()
    {
        try {
            $sql = "SELECT 
                o.id, o.note, o.shipping_address AS address, o.phone, o.created_at, o.status,
                o.total, o.total_price, o.discount_percent,
                u.name AS user_name, u.email AS user_email,
                COUNT(oi.id) as total_items,
                (SELECT p.name FROM products p 
                 JOIN order_details oi2 ON p.id = oi2.product_id 
                 WHERE oi2.order_id = o.id 
                 LIMIT 1) as product_name,
                (SELECT p.img FROM products p 
                 JOIN order_details oi2 ON p.id = oi2.product_id 
                 WHERE oi2.order_id = o.id 
                 LIMIT 1) as product_img
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN order_details oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY o.id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->lastError = "Lỗi lấy tất cả đơn hàng: " . $e->getMessage();
            return [];
        }
    }

    /**
     * METHOD UPDATESTATUS - CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG
     * Chức năng: Cập nhật trạng thái của đơn hàng
     * Sử dụng: Admin cập nhật trạng thái đơn hàng
     * Tham số: $order_id - ID đơn hàng, $status - trạng thái mới
     * Trả về: true nếu thành công, false nếu thất bại
     */
    public function updateStatus($order_id, $status)
    {
        try {
            $sql = "UPDATE orders SET status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':status' => $status,
                ':id' => $order_id
            ]);

            if (!$result) {
                $this->lastError = "Không thể cập nhật trạng thái đơn hàng";
                return false;
            }
            return true;

        } catch (PDOException $e) {
            $this->lastError = "Lỗi cập nhật trạng thái đơn hàng: " . $e->getMessage();
            return false;
        }
    }

    /**
     * METHOD DELETE - XÓA ĐƠN HÀNG VÀ CÁC ITEM CON
     * Chức năng: Xóa đơn hàng và tất cả sản phẩm liên quan
     * Sử dụng: Admin xóa đơn hàng
     * Tham số: $id - ID đơn hàng cần xóa
     * Trả về: true nếu thành công, false nếu thất bại
     */
    public function delete($id)
    {
        try {
            $this->conn->beginTransaction(); // Bắt đầu transaction

            // Xóa order_details trước để đảm bảo toàn vẹn dữ liệu
            $this->conn->prepare("DELETE FROM order_details WHERE order_id = :id")->execute([':id' => $id]);
            // Xóa order chính
            $this->conn->prepare("DELETE FROM orders WHERE id = :id")->execute([':id' => $id]);

            $this->conn->commit(); // Commit transaction
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack(); // Rollback nếu có lỗi
            $this->lastError = "Lỗi xóa đơn hàng: " . $e->getMessage();
            return false;
        }
    }

    /**
     * METHOD GETORDERSBYUSERIDWITHDETAILS - LẤY ĐƠN HÀNG CỦA USER KÈM CHI TIẾT
     * Chức năng: Lấy danh sách đơn hàng của user với thông tin chi tiết sản phẩm
     * Sử dụng: Trang lịch sử đơn hàng của user
     * Tham số: $userId - ID của user
     * Trả về: Mảng đơn hàng với tóm tắt sản phẩm
     */
    public function getOrdersByUserIdWithDetails($userId)
    {
        try {
            $sql = "SELECT 
                    o.id, o.created_at, o.shipping_address AS address, o.phone, o.note, o.status,
                    o.total, o.total_price, o.discount_percent,
                    u.name AS user_name, u.email AS user_email,
                    GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ')') SEPARATOR ', ') as products_summary,
                    COUNT(oi.id) as total_items
                FROM orders o
                JOIN users u ON o.user_id = u.id
                LEFT JOIN order_details oi ON o.id = oi.order_id
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE o.user_id = :user_id
                GROUP BY o.id
                ORDER BY o.id DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->lastError = "Lỗi lấy đơn hàng chi tiết: " . $e->getMessage();
            return [];
        }
    }

    /**
     * METHOD FIND - TÌM ĐƠN HÀNG THEO ID
     * Chức năng: Lấy thông tin chi tiết một đơn hàng theo ID
     * Sử dụng: Trang chi tiết đơn hàng (admin)
     * Tham số: $id - ID đơn hàng cần tìm
     * Trả về: Mảng thông tin đơn hàng hoặc false
     */
    public function find($id)
    {
        try {
            $sql = "SELECT o.*, u.name as user_name, u.email as user_email
                    FROM orders o
                    JOIN users u ON o.user_id = u.id
                    WHERE o.id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            $this->lastError = "Lỗi tìm đơn hàng: " . $e->getMessage();
            return false;
        }
    }

    /**
     * METHOD GETLASTERROR - LẤY LỖI CUỐI CÙNG
     * Chức năng: Trả về thông báo lỗi cuối cùng
     * Sử dụng: Controller để hiển thị lỗi cho user
     * Trả về: Chuỗi thông báo lỗi
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * METHOD COUNTALL - ĐẾM TỔNG SỐ ĐƠN HÀNG
     * Chức năng: Đếm tổng số đơn hàng trong database
     * Sử dụng: Hiển thị thống kê trong dashboard admin
     * Trả về: Số nguyên tổng số đơn hàng
     */
    public function countAll()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM orders";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            $this->lastError = "Lỗi đếm đơn hàng: " . $e->getMessage();
            return 0;
        }
    }

    /**
     * METHOD GETRECENT - LẤY ĐƠN HÀNG GẦN ĐÂY
     * Chức năng: Lấy danh sách đơn hàng mới nhất
     * Sử dụng: Hiển thị trong dashboard admin
     * Tham số: $limit - số lượng đơn hàng cần lấy (mặc định 5)
     * Trả về: Mảng các đơn hàng gần đây
     */
    public function getRecent($limit = 5)
    {
        try {
            $sql = "SELECT o.*, u.name as user_name 
                    FROM orders o 
                    JOIN users u ON o.user_id = u.id 
                    ORDER BY o.created_at DESC 
                    LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->lastError = "Lỗi lấy đơn hàng gần đây: " . $e->getMessage();
            return [];
        }
    }

    /**
     * METHOD COUNTBYSTATUS - ĐẾM ĐƠN HÀNG THEO TRẠNG THÁI
     * Chức năng: Đếm số lượng đơn hàng theo trạng thái cụ thể
     * Sử dụng: Thống kê dashboard admin
     * Tham số: $status - trạng thái cần đếm
     * Trả về: Số nguyên số lượng đơn hàng
     */
    public function countByStatus($status)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM orders WHERE status = :status";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':status' => $status]);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            $this->lastError = "Lỗi đếm đơn hàng theo trạng thái: " . $e->getMessage();
            return 0;
        }
    }

    /**
     * METHOD GETSTATS - LẤY THỐNG KÊ ĐƠN HÀNG
     * Chức năng: Lấy thống kê tổng quan về đơn hàng
     * Sử dụng: Dashboard admin
     * Trả về: Mảng thống kê với các chỉ số quan trọng
     */
    public function getStats()
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_orders,
                        SUM(total) as total_revenue,
                        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
                        COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_orders,
                        COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders
                    FROM orders";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            $this->lastError = "Lỗi lấy thống kê đơn hàng: " . $e->getMessage();
            return [
                'total_orders' => 0,
                'total_revenue' => 0,
                'pending_orders' => 0,
                'delivered_orders' => 0,
                'cancelled_orders' => 0
            ];
        }
    }
}


