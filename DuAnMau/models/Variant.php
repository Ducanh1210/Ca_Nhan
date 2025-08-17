<?php
/**
 * FILE: models/Variant.php
 * CHỨC NĂNG: Model quản lý biến thể sản phẩm - xử lý tương tác với bảng variants trong database
 * LUỒNG CHẠY: 
 * - VÀO: Được gọi từ ProductController để quản lý các biến thể của sản phẩm
 * - RA: Trả về dữ liệu biến thể cho controller để hiển thị và xử lý
 * - NGƯỢC LẠI: Controller gọi các method này để thực hiện CRUD biến thể
 */

class Variant
{
    public $conn; // Biến kết nối database

    /**
     * CONSTRUCTOR - KHỞI TẠO KẾT NỐI DATABASE
     * Chức năng: Tạo kết nối PDO khi khởi tạo object Variant
     */
    public function __construct()
    {
        $this->conn = connectDB(); // Gọi hàm connectDB() từ function.php
    }

    /**
     * METHOD INSERT - THÊM BIẾN THỂ MỚI
     * Chức năng: Thêm biến thể mới cho sản phẩm
     * Sử dụng: Form thêm biến thể trong admin
     * Tham số: $product_id - ID sản phẩm, $name - tên biến thể, $price - giá biến thể
     * Trả về: true nếu thành công, false nếu thất bại
     */
    public function insert($product_id, $name, $price)
    {
        $sql = "INSERT INTO variants (product_id, name, price) VALUES (:product_id, :name, :price)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':product_id' => $product_id,
            ':name' => $name,
            ':price' => $price,
        ]);
    }

    /**
     * METHOD GETBYPRODUCTID - LẤY BIẾN THỂ THEO SẢN PHẨM
     * Chức năng: Lấy tất cả biến thể của một sản phẩm cụ thể
     * Sử dụng: Hiển thị biến thể trong form edit sản phẩm, trang chi tiết sản phẩm
     * Tham số: $productId - ID sản phẩm cần lấy biến thể
     * Trả về: Mảng các biến thể của sản phẩm
     */
    public function getByProductId($productId)
    {
        $sql = "SELECT id, product_id, name, price FROM variants WHERE product_id = ? ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * METHOD UPDATE - CẬP NHẬT BIẾN THỂ
     * Chức năng: Cập nhật thông tin của một biến thể
     * Sử dụng: Form edit biến thể trong admin
     * Tham số: $id - ID biến thể, $name - tên mới, $price - giá mới
     * Trả về: true nếu thành công, false nếu thất bại
     */
    public function update($id, $name, $price)
    {
        $sql = "UPDATE variants SET name = :name, price = :price WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':price' => $price,
        ]);
    }

    /**
     * METHOD FIND - TÌM BIẾN THỂ THEO ID
     * Chức năng: Lấy thông tin chi tiết một biến thể theo ID
     * Sử dụng: Form edit biến thể, kiểm tra thông tin biến thể
     * Tham số: $id - ID biến thể cần tìm
     * Trả về: Mảng thông tin biến thể hoặc null
     */
    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT id, product_id, name, price FROM variants WHERE id = ? LIMIT 1");
        $stmt->execute([(int) $id]); // Chuyển đổi về int để bảo mật
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * METHOD DELETE - XÓA BIẾN THỂ THEO ID
     * Chức năng: Xóa một biến thể cụ thể
     * Sử dụng: Nút xóa biến thể trong admin
     * Tham số: $id - ID biến thể cần xóa
     * Trả về: true nếu thành công, false nếu thất bại
     */
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM variants WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * METHOD DELETEBYPRODUCTID - XÓA TẤT CẢ BIẾN THỂ CỦA SẢN PHẨM
     * Chức năng: Xóa tất cả biến thể thuộc về một sản phẩm
     * Sử dụng: Khi xóa sản phẩm để đảm bảo toàn vẹn dữ liệu
     * Tham số: $productId - ID sản phẩm cần xóa biến thể
     * Trả về: true nếu thành công, false nếu thất bại
     */
    public function deleteByProductId($productId)
    {
        $stmt = $this->conn->prepare("DELETE FROM variants WHERE product_id = :pid");
        return $stmt->execute([':pid' => $productId]);
    }
}