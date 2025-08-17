<?php
/**
 * FILE: models/Product.php
 * CHỨC NĂNG: Model quản lý sản phẩm - xử lý tương tác với bảng products trong database
 * LUỒNG CHẠY: 
 * - VÀO: Được gọi từ ProductController để thực hiện các thao tác CRUD với sản phẩm
 * - RA: Trả về dữ liệu sản phẩm cho controller để hiển thị hoặc xử lý
 * - NGƯỢC LẠI: Controller gọi các method này để thực hiện logic nghiệp vụ
 */
class Product
{
    public $conn; // Biến kết nối database

    /**
     * CONSTRUCTOR - KHỞI TẠO KẾT NỐI DATABASE
     * Chức năng: Tạo kết nối PDO khi khởi tạo object Product
     */
    public function __construct()
    {
        $this->conn = connectDB(); // Gọi hàm connectDB() từ function.php
    }

    /**
     * METHOD ALL - LẤY TẤT CẢ SẢN PHẨM
     * Chức năng: Lấy danh sách tất cả sản phẩm kèm tên danh mục
     * Sử dụng: Hiển thị danh sách sản phẩm trong admin
     * Trả về: Mảng các sản phẩm với thông tin danh mục
     */
    public function all()
    {
        try {
            // SQL JOIN với bảng categories để lấy tên danh mục
            $sql = "SELECT products.*, categories.name AS category_name
                    FROM products 
                    LEFT JOIN categories ON products.category_id = categories.id
                    ORDER BY products.id DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (Exception $e) {
            debug('Lỗi' . $e->getMessage()); // Gọi hàm debug khi có lỗi
        }
    }

    /**
     * METHOD SEARCH - TÌM KIẾM SẢN PHẨM THEO TỪ KHÓA
     * Chức năng: Tìm kiếm sản phẩm theo tên, mô tả hoặc tên danh mục
     * Sử dụng: Chức năng tìm kiếm sản phẩm
     * Tham số: $keyword - từ khóa tìm kiếm
     * Trả về: Mảng sản phẩm phù hợp với từ khóa
     */
    public function search(string $keyword): array
    {
        try {
            // SQL tìm kiếm trong tên sản phẩm, mô tả và tên danh mục
            $sql = "SELECT products.*, categories.name AS category_name
                    FROM products
                    LEFT JOIN categories ON products.category_id = categories.id
                    WHERE products.name LIKE :kw
                       OR products.description LIKE :kw
                       OR categories.name LIKE :kw
                    ORDER BY products.id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':kw' => '%' . $keyword . '%']); // Sử dụng wildcard % để tìm kiếm
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return []; // Trả về mảng rỗng nếu có lỗi
        }
    }

    /**
     * METHOD COUNTALL - ĐẾM TỔNG SỐ SẢN PHẨM
     * Chức năng: Đếm tổng số sản phẩm trong database
     * Sử dụng: Hiển thị thống kê trong dashboard admin
     * Trả về: Số nguyên tổng số sản phẩm
     */
    public function countAll(): int
    {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) AS total FROM products");
            $row = $stmt->fetch();
            return (int) ($row['total'] ?? 0); // Chuyển đổi về int và mặc định là 0
        } catch (Exception $e) {
            return 0; // Trả về 0 nếu có lỗi
        }
    }

    /**
     * METHOD FIND - TÌM SẢN PHẨM THEO ID
     * Chức năng: Lấy thông tin chi tiết một sản phẩm theo ID
     * Sử dụng: Hiển thị chi tiết sản phẩm, form edit
     * Tham số: $id - ID sản phẩm cần tìm
     * Trả về: Mảng thông tin sản phẩm hoặc null
     */
    public function find($id)
    {
        try {
            // SQL JOIN với categories để lấy tên danh mục
            $sql = "SELECT products.*, categories.name AS category_name
                    FROM products
                    LEFT JOIN categories ON products.category_id = categories.id
                    WHERE products.id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetch(); // Trả về 1 sản phẩm

        } catch (Exception $e) {
            debug("Lỗi" . $e); // Gọi hàm debug khi có lỗi
        }
    }

    /**
     * METHOD INSERT - THÊM SẢN PHẨM MỚI
     * Chức năng: Thêm sản phẩm mới vào database
     * Sử dụng: Form thêm sản phẩm trong admin
     * Tham số: Các thông tin cơ bản của sản phẩm
     */
    public function insert($name, $img, $price, $description, $category_id)
    {
        $sql = 'INSERT INTO products (name, img, price, description, category_id)
            VALUES (:name, :img, :price, :description, :category_id)';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':img' => $img,
            ':price' => $price,
            ':description' => $description,
            ':category_id' => $category_id
        ]);
    }

    /**
     * METHOD UPDATEBASIC - CẬP NHẬT THÔNG TIN CƠ BẢN SẢN PHẨM
     * Chức năng: Cập nhật thông tin sản phẩm theo ID
     * Sử dụng: Form edit sản phẩm trong admin
     * Tham số: ID và các thông tin cần cập nhật
     */
    public function updateBasic($id, $name, $img, $price, $description, $category_id)
    {
        try {
            $sql = 'UPDATE products SET
                    name = :name,
                    img = :img,
                    price = :price,
                    description = :description,
                    category_id = :category_id
                WHERE id = :id';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':img' => $img,
                ':price' => $price,
                ':description' => $description,
                ':category_id' => $category_id,
                ':id' => $id
            ]);
        } catch (Exception $e) {
            debug('Lỗi: ' . $e->getMessage()); // Gọi hàm debug khi có lỗi
        }
    }

    /**
     * METHOD DELETE - XÓA SẢN PHẨM
     * Chức năng: Xóa sản phẩm theo ID
     * Sử dụng: Chức năng xóa sản phẩm trong admin
     * Tham số: $id - ID sản phẩm cần xóa
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM products WHERE id = :id";
            $stm = $this->conn->prepare($sql);
            $stm->execute([":id" => $id]);

        } catch (Exception $e) {
            debug("Loi" . $e); // Gọi hàm debug khi có lỗi
        }
    }

    /**
     * METHOD GETRECENT - LẤY SẢN PHẨM GẦN ĐÂY
     * Chức năng: Lấy danh sách sản phẩm mới nhất
     * Sử dụng: Hiển thị sản phẩm nổi bật trong dashboard
     * Tham số: $limit - số lượng sản phẩm cần lấy (mặc định 5)
     * Trả về: Mảng các sản phẩm gần đây
     */
    public function getRecent(int $limit = 5): array
    {
        try {
            $stmt = $this->conn->prepare("SELECT id, name, created_at FROM products ORDER BY id DESC LIMIT :lim");
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT); // Bind parameter với kiểu INT
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return []; // Trả về mảng rỗng nếu có lỗi
        }
    }

}

?>