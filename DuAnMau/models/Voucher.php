<?php
/**
 * FILE: models/Voucher.php
 * CHỨC NĂNG: Model quản lý mã giảm giá (voucher) - xử lý tương tác với bảng vouchers trong database
 * LUỒNG CHẠY: 
 * - VÀO: Được gọi từ VoucherController và UserController để quản lý mã giảm giá
 * - RA: Trả về dữ liệu voucher cho controller để xử lý áp dụng mã giảm giá
 * - NGƯỢC LẠI: Controller gọi các method này để thực hiện CRUD voucher
 */
class Voucher
{
    public $conn; // Biến kết nối database

    /**
     * CONSTRUCTOR - KHỞI TẠO KẾT NỐI DATABASE
     * Chức năng: Tạo kết nối PDO khi khởi tạo object Voucher
     */
    public function __construct()
    {
        $this->conn = connectDB(); // Gọi hàm connectDB() từ function.php
    }

    /**
     * METHOD ALL - LẤY TẤT CẢ VOUCHER
     * Chức năng: Lấy danh sách tất cả mã giảm giá trong hệ thống
     * Sử dụng: Trang quản lý voucher (admin)
     * Trả về: Mảng tất cả voucher hoặc null nếu có lỗi
     */
    public function all()
    {
        try {
            $sql = "SELECT * FROM vouchers ORDER BY id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            debug('Lỗi' . $e->getMessage()); // Gọi hàm debug khi có lỗi
        }
    }

    /**
     * METHOD INSERT - THÊM VOUCHER MỚI
     * Chức năng: Thêm mã giảm giá mới vào database
     * Sử dụng: Form thêm voucher trong admin
     * Tham số: $code - mã voucher, $discount_percent - phần trăm giảm giá, $expiry_date - ngày hết hạn
     */
    public function insert($code, $discount_percent, $expiry_date)
    {
        try {
            $sql = "INSERT INTO vouchers (code, discount_percent, expiry_date, status) 
            VALUES (:code, :discount_percent, :expiry_date, 'active')";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':code' => $code,
                ':discount_percent' => $discount_percent,
                ':expiry_date' => $expiry_date
            ]);
        } catch (Exception $e) {
            debug('Lỗi' . $e->getMessage()); // Gọi hàm debug khi có lỗi
        }
    }

    /**
     * METHOD FINDBYCODE - TÌM VOUCHER THEO MÃ
     * Chức năng: Tìm voucher theo mã và kiểm tra tính hợp lệ (active + chưa hết hạn)
     * Sử dụng: Form áp dụng mã giảm giá trong checkout
     * Tham số: $code - mã voucher cần tìm
     * Trả về: Mảng thông tin voucher hoặc false nếu không hợp lệ
     */
    public function findByCode($code)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM vouchers WHERE code = ? AND status = 'active' AND expiry_date >= CURDATE()");
            $stmt->execute([$code]);
            return $stmt->fetch();
        } catch (Exception $e) {
            debug('Lỗi: ' . $e->getMessage()); // Gọi hàm debug khi có lỗi
            return false;
        }
    }

    /**
     * METHOD FINDBYID - TÌM VOUCHER THEO ID
     * Chức năng: Lấy thông tin chi tiết voucher theo ID
     * Sử dụng: Form edit voucher trong admin
     * Tham số: $id - ID voucher cần tìm
     * Trả về: Mảng thông tin voucher hoặc null
     */
    public function findById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM vouchers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * METHOD UPDATE - CẬP NHẬT VOUCHER
     * Chức năng: Cập nhật thông tin của một voucher
     * Sử dụng: Form edit voucher trong admin
     * Tham số: $id, $code, $discount_percent, $expiry_date, $status (mặc định 'active')
     */
    public function update($id, $code, $discount_percent, $expiry_date, $status = 'active')
    {
        try {
            $sql = "UPDATE vouchers SET code = :code, discount_percent = :discount_percent, expiry_date = :expiry_date, status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':code' => $code,
                ':discount_percent' => $discount_percent,
                ':expiry_date' => $expiry_date,
                ':status' => $status,
                ':id' => $id
            ]);
        } catch (Exception $e) {
            debug('Lỗi' . $e->getMessage()); // Gọi hàm debug khi có lỗi
        }
    }

    /**
     * METHOD UPDATESTATUS - CẬP NHẬT TRẠNG THÁI VOUCHER
     * Chức năng: Cập nhật trạng thái của voucher (active/inactive)
     * Sử dụng: Kích hoạt/vô hiệu hóa voucher trong admin
     * Tham số: $id - ID voucher, $status - trạng thái mới
     * Trả về: true nếu thành công, false nếu thất bại
     */
    public function updateStatus($id, $status)
    {
        try {
            $sql = "UPDATE vouchers SET status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':status' => $status,
                ':id' => $id
            ]);
            return true;
        } catch (Exception $e) {
            debug('Lỗi: ' . $e->getMessage()); // Gọi hàm debug khi có lỗi
            return false;
        }
    }

    /**
     * METHOD DELETE - XÓA VOUCHER
     * Chức năng: Xóa voucher theo ID
     * Sử dụng: Nút xóa voucher trong admin
     * Tham số: $id - ID voucher cần xóa
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM vouchers WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
        } catch (Exception $e) {
            debug('Lỗi' . $e->getMessage()); // Gọi hàm debug khi có lỗi
        }
    }
}
?>