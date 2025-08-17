<?php
/**
 * FILE: controllers/ProductController.php
 * CHỨC NĂNG: Controller quản lý sản phẩm - xử lý logic nghiệp vụ liên quan đến sản phẩm
 * LUỒNG CHẠY: 
 * - VÀO: Nhận request từ index.php với các action khác nhau (?act=...)
 * - RA: Gọi model để xử lý dữ liệu và require view để hiển thị giao diện
 * - NGƯỢC LẠI: Tương tác với các model (Product, Categorie, Variant) để thực hiện CRUD
 */
class ProductController
{
    // Khai báo các model cần thiết
    public $product;      // Model quản lý sản phẩm
    public $categorie;    // Model quản lý danh mục
    public $variant;      // Model quản lý biến thể sản phẩm

    /**
     * CONSTRUCTOR - KHỞI TẠO CÁC MODEL
     * Chức năng: Tạo các instance của model khi khởi tạo controller
     */
    public function __construct()
    {
        $this->product = new Product();
        $this->categorie = new Categorie();
        $this->variant = new Variant();
    }

    /**
     * METHOD DASHBOARD - TRANG TỔNG QUAN ADMIN
     * Chức năng: Hiển thị dashboard với thống kê tổng quan hệ thống
     * Sử dụng: Trang chủ admin sau khi đăng nhập
     * Luồng: Kiểm tra session admin → Lấy dữ liệu thống kê → Hiển thị dashboard
     */
    public function dashboard()
    {
        // Khởi tạo session nếu chưa có
        if (!isset($_SESSION))
            session_start();
        
        // Kiểm tra quyền admin - nếu không có thì chuyển về trang login
        if (!isset($_SESSION['admin'])) {
            header('Location: ?act=login');
            exit;
        }

        // Khởi tạo các model cần thiết cho thống kê
        $orderModel = new Order();
        $userModel = new User();

        // Lấy dữ liệu thống kê tổng quan
        $totalProducts = $this->product->countAll();      // Tổng số sản phẩm
        $totalOrders = $orderModel->countAll();           // Tổng số đơn hàng
        $totalUsers = $userModel->countAll();             // Tổng số người dùng
        $totalCategories = $this->categorie->countAll();  // Tổng số danh mục

        // Lấy danh sách dữ liệu để hiển thị
        $categories = $this->categorie->all();            // Danh sách danh mục
        $products = $this->product->all();                // Danh sách sản phẩm
        $users = $userModel->all();                       // Danh sách người dùng
        $orders = $orderModel->all();                     // Danh sách đơn hàng

        // Lấy dữ liệu gần đây để hiển thị trong dashboard
        $recentProducts = $this->product->getRecent(3);   // 3 sản phẩm mới nhất
        $recentOrders = $orderModel->getRecent(3);        // 3 đơn hàng gần đây
        $recentUsers = $userModel->getRecent(3);          // 3 người dùng mới nhất

        // Hiển thị view dashboard
        require_once './views/admin/dashboard.php';
    }

    /**
     * METHOD LIST - DANH SÁCH SẢN PHẨM (ADMIN)
     * Chức năng: Hiển thị danh sách tất cả sản phẩm kèm biến thể
     * Sử dụng: Trang quản lý sản phẩm trong admin
     * Luồng: Lấy danh sách sản phẩm → Lấy biến thể cho từng sản phẩm → Hiển thị list
     */
    public function list()
    {
        $productModel = new Product();
        $productList = $productModel->all();              // Lấy tất cả sản phẩm
        $variantModel = new Variant();

        $products = [];

        // Duyệt qua từng sản phẩm để lấy thông tin biến thể
        foreach ($productList as $item) {
            $productId = $item['id'];
            if (!isset($products[$productId])) {
                $products[$productId] = $item;
                // Lấy danh sách biến thể cho sản phẩm này
                $products[$productId]['variants'] = $variantModel->getByProductId($productId);
            }
        }

        $products = array_values($products);              // Chuyển về indexed array
        require './views/admin/sanpham/list.php';         // Hiển thị view danh sách
    }

    /**
     * METHOD ADDVARIANTS - THÊM NHIỀU BIẾN THỂ TỪ MODAL TRONG LIST
     * Chức năng: Xử lý thêm biến thể cho sản phẩm từ form modal
     * Sử dụng: Modal thêm biến thể trong trang danh sách sản phẩm
     * Luồng: Kiểm tra POST request → Lấy product_id → Thêm các biến thể → Redirect về list
     */
    public function addVariants()
    {
        // Chỉ chấp nhận POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=p-list');
            exit;
        }

        // Lấy product_id từ URL parameter
        $productId = (int) ($_GET['product_id'] ?? 0);
        if ($productId <= 0) {
            header('Location: ?act=p-list');
            exit;
        }

        // Lấy dữ liệu biến thể từ form POST
        $names = $_POST['variant_name'] ?? [];
        $prices = $_POST['variant_price'] ?? [];
        
        // Xử lý thêm từng biến thể
        if (!empty($names)) {
            foreach ($names as $i => $name) {
                $name = trim($name);
                $price = isset($prices[$i]) ? (int) $prices[$i] : 0;
                // Chỉ thêm khi có tên và giá hợp lệ
                if ($name !== '' && $price >= 0) {
                    $this->variant->insert($productId, $name, $price);
                }
            }
        }

        // Redirect về trang danh sách sản phẩm
        header('Location: ?act=p-list');
        exit;
    }

    /**
     * METHOD DELETEVARIANT - XÓA BIẾN THỂ SẢN PHẨM
     * Chức năng: Xóa một biến thể cụ thể của sản phẩm
     * Sử dụng: Nút xóa biến thể trong trang danh sách sản phẩm
     * Luồng: Kiểm tra GET request → Lấy variant_id và product_id → Xóa biến thể → Redirect
     */
    public function deleteVariant()
    {
        // Chỉ chấp nhận GET request
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Location: ?act=p-list');
            exit;
        }
        
        // Lấy thông tin từ URL parameters
        $variantId = (int) ($_GET['id'] ?? 0);
        $productId = (int) ($_GET['product_id'] ?? 0);
        
        // Kiểm tra tính hợp lệ của ID
        if ($variantId <= 0 || $productId <= 0) {
            header('Location: ?act=p-list');
            exit;
        }
        
        // Xóa biến thể
        $this->variant->delete($variantId);
        header('Location: ?act=p-list');
        exit;
    }

    /**
     * METHOD CLIENT - TRANG CHỦ CLIENT
     * Chức năng: Hiển thị trang chủ cho khách hàng với tìm kiếm sản phẩm
     * Sử dụng: Trang chủ website (client)
     * Luồng: Khởi tạo session → Xử lý tìm kiếm → Hiển thị sản phẩm
     */
    public function client()
    {
        // Khởi tạo session nếu chưa có
        if (!isset($_SESSION))
            session_start();
            
        // Xử lý tìm kiếm sản phẩm
        $q = trim($_GET['q'] ?? '');
        // Nếu có từ khóa tìm kiếm thì search, không thì lấy tất cả
        $products = $q !== '' ? $this->product->search($q) : $this->product->all();
        
        require_once './views/client/home.php';           // Hiển thị view trang chủ
    }

    /**
     * METHOD SHOW - HIỂN THỊ CHI TIẾT SẢN PHẨM (ADMIN)
     * Chức năng: Hiển thị chi tiết sản phẩm cho admin
     * Sử dụng: Xem chi tiết sản phẩm trong admin
     * Luồng: Kiểm tra quyền admin → Lấy thông tin sản phẩm → Hiển thị detail
     */
    public function show($id)
    {
        // Khởi tạo session nếu chưa có
        if (!isset($_SESSION))
            session_start();
            
        // Kiểm tra quyền admin
        if (!isset($_SESSION['admin'])) {
            header('Location: ?act=login');
            exit;
        }

        // Kiểm tra và chuyển đổi ID
        $id = (int) $id;
        if ($id <= 0) {
            header('Location: ?act=p-list');
            exit;
        }

        // Lấy thông tin sản phẩm
        $product = $this->product->find($id);
        if (!$product) {
            header('Location: ?act=p-list');
            exit;
        }

        // Lấy thông tin danh mục của sản phẩm
        $category = $this->categorie->find($product['category_id']);

        // Lấy các biến thể của sản phẩm
        $variants = $this->variant->getByProductId($id);

        require_once './views/admin/sanpham/detail.php';   // Hiển thị view chi tiết
    }

    /**
     * METHOD CLIENTS - TRANG DANH SÁCH SẢN PHẨM (CLIENT)
     * Chức năng: Hiển thị danh sách tất cả sản phẩm cho khách hàng
     * Sử dụng: Trang danh sách sản phẩm (client)
     * Luồng: Lấy tất cả sản phẩm → Hiển thị trang home (với tất cả sản phẩm)
     */
    public function clients()
    {
        $products = $this->product->all();                // Lấy tất cả sản phẩm
        require_once './views/client/home.php';           // Hiển thị view trang chủ
    }

    /**
     * METHOD ADD - THÊM SẢN PHẨM MỚI (ADMIN)
     * Chức năng: Hiển thị form thêm sản phẩm và xử lý thêm sản phẩm mới
     * Sử dụng: Form thêm sản phẩm trong admin
     * Luồng: GET: Hiển thị form → POST: Xử lý upload ảnh → Thêm sản phẩm → Thêm biến thể → Redirect
     */
    public function add()
    {
        // Lấy danh sách danh mục để hiển thị trong form
        $categories = $this->categorie->all();

        // Xử lý khi form được submit (POST)
        if (!empty($_POST)) {
            // Xử lý upload ảnh sản phẩm
            $dest = 'img/' . basename($_FILES['img']['name']);
            $temp = $_FILES['img']['tmp_name'];
            move_uploaded_file($temp, $dest);
            $img = $dest;

            // Thêm sản phẩm cơ bản vào database
            $this->product->insert(
                $_POST['name'],
                $img,
                (int) $_POST['price'],
                $_POST['description'],
                (int) $_POST['category_id']
            );

            // Lấy ID của sản phẩm vừa thêm để thêm biến thể
            $lastProductId = $this->getLastProductId();

            // Thêm nhiều biến thể động (name + price)
            $variant_names = $_POST['variant_name'] ?? [];
            $variant_prices = $_POST['variant_price'] ?? [];
            if (!empty($variant_names)) {
                foreach ($variant_names as $idx => $vname) {
                    $vname = trim($vname);
                    $vprice = isset($variant_prices[$idx]) ? (int) $variant_prices[$idx] : 0;
                    // Chỉ thêm khi có tên và giá hợp lệ
                    if ($vname !== '' && $vprice >= 0) {
                        $this->variant->insert($lastProductId, $vname, $vprice);
                    }
                }
            }

            // Redirect về trang danh sách sản phẩm
            header("Location: ?act=p-list");
            exit();
        }

        // Hiển thị form thêm sản phẩm (GET request)
        require_once './views/admin/sanpham/add.php';
    }

    /**
     * METHOD GETLASTPRODUCTID - LẤY ID SẢN PHẨM CUỐI CÙNG
     * Chức năng: Lấy ID của sản phẩm vừa được thêm vào database
     * Sử dụng: Để thêm biến thể cho sản phẩm mới
     * Trả về: ID của sản phẩm cuối cùng hoặc null
     */
    private function getLastProductId()
    {
        $conn = $this->product->conn;
        $stmt = $conn->query("SELECT MAX(id) as id FROM products");
        $row = $stmt->fetch();
        return $row ? $row['id'] : null;
    }

    /**
     * METHOD EDIT - SỬA SẢN PHẨM (ADMIN)
     * Chức năng: Hiển thị form sửa sản phẩm và xử lý cập nhật thông tin
     * Sử dụng: Form sửa sản phẩm trong admin
     * Luồng: GET: Hiển thị form với dữ liệu hiện tại → POST: Xử lý cập nhật → Redirect
     */
    public function edit($id)
    {
        // Lấy thông tin sản phẩm cần sửa
        $product = $this->product->find($id);
        $categories = $this->categorie->all();            // Danh sách danh mục
        $variants = $this->variant->getByProductId($id);  // Biến thể hiện tại

        // Xử lý khi form được submit (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $img = $product['img']; // Giữ ảnh cũ nếu không upload ảnh mới
            
            // Xử lý upload ảnh mới nếu có
            if (!empty($_FILES['img']['name'])) {
                $dest = 'img/' . basename($_FILES['img']['name']);
                $temp = $_FILES['img']['tmp_name'];
                move_uploaded_file($_FILES['img']['tmp_name'], $dest);
                $img = $dest;
            }

            // Cập nhật thông tin cơ bản của sản phẩm
            $this->product->updateBasic(
                $id,
                $_POST['name'],
                $img,
                (int) $_POST['price'],
                $_POST['description'],
                (int) $_POST['category_id']
            );

            // Cập nhật biến thể (nếu form gửi kèm). Nếu không gửi thì giữ nguyên biến thể cũ
            if (isset($_POST['variant_name']) && is_array($_POST['variant_name'])) {
                $variant_names = $_POST['variant_name'];
                $variant_prices = $_POST['variant_price'] ?? [];

                // Chỉ khi có ít nhất 1 dòng hợp lệ mới tiến hành thay thế toàn bộ
                $hasAtLeastOne = false;
                foreach ($variant_names as $idx => $vname) {
                    if (trim((string) $vname) !== '') {
                        $hasAtLeastOne = true;
                        break;
                    }
                }

                if ($hasAtLeastOne) {
                    // Thay thế toàn bộ danh sách biến thể bằng danh sách từ form
                    $this->variant->deleteByProductId($id);
                    foreach ($variant_names as $idx => $vname) {
                        $vname = trim((string) $vname);
                        $vprice = isset($variant_prices[$idx]) ? (int) $variant_prices[$idx] : 0;
                        if ($vname !== '' && $vprice >= 0) {
                            $this->variant->insert($id, $vname, $vprice);
                        }
                    }
                }
                // Trường hợp form gửi nhưng không có dòng hợp lệ: bỏ qua để GIỮ NGUYÊN biến thể cũ
            }

            // Redirect về trang danh sách sản phẩm
            header("Location: ?act=p-list");
            exit;
        }

        // Hiển thị form sửa sản phẩm (GET request)
        require_once './views/admin/sanpham/edit.php';
    }

    /**
     * METHOD DELETE - XÓA SẢN PHẨM (ADMIN)
     * Chức năng: Xóa sản phẩm và tất cả biến thể liên quan
     * Sử dụng: Nút xóa sản phẩm trong admin
     * Luồng: Xóa biến thể trước → Xóa sản phẩm → Redirect
     */
    public function delete($id)
    {
        // Xóa biến thể trước để đảm bảo toàn vẹn dữ liệu
        $this->variant->deleteByProductId($id);
        // Xóa sản phẩm
        $this->product->delete($id);
        header("Location: ?act=p-list");
        exit();
    }

    /**
     * METHOD DETAIL - CHI TIẾT SẢN PHẨM (CLIENT)
     * Chức năng: Hiển thị chi tiết sản phẩm cho khách hàng
     * Sử dụng: Trang chi tiết sản phẩm (client)
     * Luồng: Lấy ID từ URL → Lấy thông tin sản phẩm và biến thể → Hiển thị detail
     */
    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $productModel = new Product();
        $variantModel = new Variant();
        
        // Lấy thông tin sản phẩm và biến thể
        $product = $productModel->find($id);
        $variants = $variantModel->getByProductId($id);

        require_once './views/client/product-detail.php';  // Hiển thị view chi tiết
    }

}
?>