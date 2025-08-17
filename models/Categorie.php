<?php
/**
 * FILE: models/Categorie.php
 * CHỨC NĂNG: Model quản lý danh mục sản phẩm - xử lý tương tác với bảng categories trong database
 * LUỒNG CHẠY: 
 * - VÀO: Được gọi từ CategoryController và ProductController để quản lý danh mục
 * - RA: Trả về dữ liệu danh mục cho controller để hiển thị và xử lý
 * - NGƯỢC LẠI: Controller gọi các method này để thực hiện CRUD danh mục
 */
class Categorie
{
    public $conn; // Biến kết nối database

    /**
     * CONSTRUCTOR - KHỞI TẠO KẾT NỐI DATABASE
     * Chức năng: Tạo kết nối PDO khi khởi tạo object Categorie
     */
    public function __construct()
    {
        $this->conn = connectDB(); // Gọi hàm connectDB() từ function.php
    }

    /**
     * METHOD ALL - LẤY TẤT CẢ DANH MỤC
     * Chức năng: Lấy danh sách tất cả danh mục sản phẩm
     * Sử dụng: Form thêm/sửa sản phẩm, dropdown chọn danh mục
     * Trả về: Mảng tất cả danh mục hoặc null nếu có lỗi
     */
    public function all()
    {
        try {
            $sql = "SELECT * FROM categories";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            debug('Lỗi' . $e->getMessage()); // Gọi hàm debug khi có lỗi
        }
    }

    /**
     * METHOD COUNTALL - ĐẾM TỔNG SỐ DANH MỤC
     * Chức năng: Đếm tổng số danh mục trong database
     * Sử dụng: Hiển thị thống kê trong dashboard admin
     * Trả về: Số nguyên tổng số danh mục
     */
    public function countAll(): int
    {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) AS total FROM categories");
            $row = $stmt->fetch();
            return (int) ($row['total'] ?? 0); // Chuyển đổi về int và mặc định là 0
        } catch (Exception $e) {
            return 0; // Trả về 0 nếu có lỗi
        }
    }

    /**
     * METHOD FIND - TÌM DANH MỤC THEO ID
     * Chức năng: Lấy thông tin chi tiết một danh mục theo ID
     * Sử dụng: Form edit danh mục, hiển thị thông tin danh mục
     * Tham số: $id - ID danh mục cần tìm
     * Trả về: Mảng thông tin danh mục hoặc null
     */
    public function find($id)
    {
        try {
            $sql = "SELECT * FROM categories WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            debug('Lỗi' . $e->getMessage()); // Gọi hàm debug khi có lỗi
        }
    }

    /**
     * METHOD INSERT - THÊM DANH MỤC MỚI
     * Chức năng: Thêm danh mục mới vào database
     * Sử dụng: Form thêm danh mục trong admin
     * Tham số: $name - tên danh mục
     * Trả về: true nếu thành công, false nếu thất bại
     */
    public function insert($name)
    {
        try {
            $sql = "INSERT INTO categories (name) VALUES (:name)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([':name' => $name]);
            return $result;
        } catch (Exception $e) {
            debug('Lỗi' . $e->getMessage()); // Gọi hàm debug khi có lỗi
            return false;
        }
    }

    /**
     * METHOD UPDATE - CẬP NHẬT DANH MỤC
     * Chức năng: Cập nhật tên của một danh mục
     * Sử dụng: Form edit danh mục trong admin
     * Tham số: $id - ID danh mục, $name - tên mới
     * Trả về: true nếu thành công, false nếu thất bại
     */
    public function update($id, $name)
    {
        try {
            $sql = "UPDATE categories SET name = :name WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([':name' => $name, ':id' => $id]);
            return $result;
        } catch (Exception $e) {
            debug('Lỗi' . $e->getMessage()); // Gọi hàm debug khi có lỗi
            return false;
        }
    }

    /**
     * METHOD DELETE - XÓA DANH MỤC
     * Chức năng: Xóa danh mục theo ID
     * Sử dụng: Nút xóa danh mục trong admin
     * Tham số: $id - ID danh mục cần xóa
     * Trả về: true nếu thành công, false nếu thất bại
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM categories WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([':id' => $id]);
            return $result;
        } catch (Exception $e) {
            debug('Lỗi' . $e->getMessage()); // Gọi hàm debug khi có lỗi
            return false;
        }
    }
}
