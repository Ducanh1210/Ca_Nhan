<?php
/**
 * FILE: models/User.php
 * CHỨC NĂNG: Model quản lý người dùng - xử lý tương tác với bảng users trong database
 * LUỒNG CHẠY: 
 * - VÀO: Được gọi từ UserController để thực hiện các thao tác CRUD với người dùng
 * - RA: Trả về dữ liệu người dùng cho controller để xử lý đăng nhập, đăng ký, quản lý
 * - NGƯỢC LẠI: Controller gọi các method này để thực hiện logic nghiệp vụ
 */
class User
{
    public $conn; // Biến kết nối database

    /**
     * CONSTRUCTOR - KHỞI TẠO KẾT NỐI DATABASE
     * Chức năng: Tạo kết nối PDO khi khởi tạo object User
     */
    public function __construct()
    {
        $this->conn = connectDB(); // Gọi hàm connectDB() từ function.php
    }

    /**
     * METHOD REGISTER - ĐĂNG KÝ NGƯỜI DÙNG MỚI
     * Chức năng: Thêm người dùng mới vào database với mật khẩu đã hash
     * Sử dụng: Form đăng ký tài khoản
     * Tham số: $name, $email, $password
     */
    public function register($name, $email, $password)
    {
        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT) // Hash mật khẩu bảo mật
        ]);
    }

    /**
     * METHOD LOGIN - XÁC THỰC ĐĂNG NHẬP
     * Chức năng: Kiểm tra thông tin đăng nhập và trả về thông tin user nếu thành công
     * Sử dụng: Form đăng nhập
     * Tham số: $email, $password
     * Trả về: Mảng thông tin user hoặc false nếu thất bại
     */
    public function login($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // Kiểm tra mật khẩu đã hash
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * METHOD ALL - LẤY TẤT CẢ NGƯỜI DÙNG
     * Chức năng: Lấy danh sách tất cả người dùng trong hệ thống
     * Sử dụng: Trang quản lý user (admin)
     * Trả về: Mảng tất cả người dùng
     */
    public function all()
    {
        $stmt = $this->conn->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * METHOD COUNTALL - ĐẾM TỔNG SỐ NGƯỜI DÙNG
     * Chức năng: Đếm tổng số người dùng trong database
     * Sử dụng: Hiển thị thống kê trong dashboard admin
     * Trả về: Số nguyên tổng số người dùng
     */
    public function countAll(): int
    {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) AS total FROM users");
            $row = $stmt->fetch();
            return (int) ($row['total'] ?? 0); // Chuyển đổi về int và mặc định là 0
        } catch (Exception $e) {
            return 0; // Trả về 0 nếu có lỗi
        }
    }

    /**
     * METHOD FIND - TÌM NGƯỜI DÙNG THEO ID
     * Chức năng: Lấy thông tin chi tiết một người dùng theo ID
     * Sử dụng: Form edit user, xem chi tiết user
     * Tham số: $id - ID người dùng cần tìm
     * Trả về: Mảng thông tin user hoặc null
     */
    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * METHOD FINDBYEMAIL - TÌM NGƯỜI DÙNG THEO EMAIL
     * Chức năng: Lấy thông tin user theo email
     * Sử dụng: Kiểm tra email tồn tại khi đăng ký, đăng nhập
     * Tham số: $email - Email cần tìm
     * Trả về: Mảng thông tin user hoặc null
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * METHOD EXISTSEMAILEXCEPTID - KIỂM TRA EMAIL TỒN TẠI (LOẠI TRỪ ID)
     * Chức năng: Kiểm tra email đã tồn tại bởi user khác (dùng cho edit)
     * Sử dụng: Form edit user để tránh trùng email
     * Tham số: $email - Email cần kiểm tra, $exceptId - ID user cần loại trừ
     * Trả về: true nếu email tồn tại, false nếu không
     */
    public function existsEmailExceptId(string $email, int $exceptId): bool
    {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1");
        $stmt->execute([':email' => $email, ':id' => $exceptId]);
        return (bool) $stmt->fetch();
    }

    /**
     * METHOD INSERTWITHROLE - THÊM NGƯỜI DÙNG VỚI VAI TRÒ
     * Chức năng: Thêm user mới với vai trò cụ thể (admin/user)
     * Sử dụng: Admin thêm user mới
     * Tham số: $name, $email, $password, $role (mặc định 'user')
     */
    public function insertWithRole(string $name, string $email, string $password, string $role = 'user')
    {
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT), // Hash mật khẩu
            ':role' => $role === 'admin' ? 'admin' : 'user' // Chỉ cho phép admin hoặc user
        ]);
    }

    /**
     * METHOD UPDATEBASICROLE - CẬP NHẬT THÔNG TIN CƠ BẢN VÀ VAI TRÒ
     * Chức năng: Cập nhật tên, email và vai trò của user
     * Sử dụng: Form edit user (admin)
     * Tham số: $id, $name, $email, $role
     */
    public function updateBasicRole(int $id, string $name, string $email, string $role)
    {
        $stmt = $this->conn->prepare("UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':role' => $role === 'admin' ? 'admin' : 'user', // Validate role
            ':id' => $id,
        ]);
    }

    /**
     * METHOD GETRECENT - LẤY NGƯỜI DÙNG GẦN ĐÂY
     * Chức năng: Lấy danh sách user mới nhất
     * Sử dụng: Hiển thị trong dashboard admin
     * Tham số: $limit - số lượng user cần lấy (mặc định 5)
     * Trả về: Mảng các user gần đây
     */
    public function getRecent(int $limit = 5): array
    {
        try {
            $stmt = $this->conn->prepare("SELECT id, name, created_at FROM users ORDER BY id DESC LIMIT :lim");
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT); // Bind parameter với kiểu INT
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return []; // Trả về mảng rỗng nếu có lỗi
        }
    }
}
