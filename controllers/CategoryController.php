<?php
/**
 * FILE: controllers/CategoryController.php
 * CHỨC NĂNG: Controller quản lý danh mục sản phẩm - xử lý logic nghiệp vụ liên quan đến danh mục
 * LUỒNG CHẠY: 
 * - VÀO: Nhận request từ index.php với các action khác nhau (?act=category-...)
 * - RA: Gọi model để xử lý dữ liệu và require view để hiển thị giao diện
 * - NGƯỢC LẠI: Tương tác với model Categorie để thực hiện CRUD danh mục
 */
class CategoryController
{
    private Categorie $categoryModel; // Model quản lý danh mục

    /**
     * CONSTRUCTOR - KHỞI TẠO MODEL
     * Chức năng: Tạo instance của model Categorie khi khởi tạo controller
     */
    public function __construct()
    {
        $this->categoryModel = new Categorie();
    }

    /**
     * METHOD LIST - HIỂN THỊ DANH SÁCH DANH MỤC
     * Chức năng: Hiển thị trang danh sách tất cả danh mục
     * Sử dụng: Trang quản lý danh mục (admin)
     * Luồng: Kiểm tra quyền admin → Lấy danh sách danh mục → Hiển thị view
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

        $categories = $this->categoryModel->all(); // Lấy tất cả danh mục
        require_once './views/admin/categories/list.php'; // Hiển thị view
    }

    /**
     * METHOD ADD - THÊM DANH MỤC MỚI
     * Chức năng: Xử lý form thêm danh mục mới
     * Sử dụng: Form thêm danh mục trong admin
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

            // Validate dữ liệu
            if ($name === '') {
                $error = 'Vui lòng nhập tên danh mục';
            } else {
                $result = $this->categoryModel->insert($name); // Thêm danh mục
                if ($result) {
                    $_SESSION['flash'] = [
                        'type' => 'success',
                        'message' => 'Thêm danh mục "' . htmlspecialchars($name) . '" thành công!'
                    ];
                } else {
                    $_SESSION['flash'] = [
                        'type' => 'danger',
                        'message' => 'Có lỗi xảy ra khi thêm danh mục. Vui lòng thử lại!'
                    ];
                }
                header('Location: ?act=category-list'); // Chuyển hướng về danh sách
                exit;
            }
        }

        require_once './views/admin/categories/add.php'; // Hiển thị form thêm
    }

    /**
     * METHOD EDIT - SỬA DANH MỤC
     * Chức năng: Xử lý form sửa danh mục
     * Sử dụng: Form edit danh mục trong admin
     * Luồng: Kiểm tra quyền → Lấy thông tin danh mục → Xử lý POST → Validate → Cập nhật → Redirect
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
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'ID danh mục không hợp lệ!'
            ];
            header('Location: ?act=category-list');
            exit;
        }

        $category = $this->categoryModel->find($id); // Lấy thông tin danh mục
        if (!$category) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Không tìm thấy danh mục!'
            ];
            header('Location: ?act=category-list');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');

            // Validate dữ liệu
            if ($name === '') {
                $error = 'Vui lòng nhập tên danh mục';
            } else {
                $result = $this->categoryModel->update($id, $name); // Cập nhật danh mục
                if ($result) {
                    $_SESSION['flash'] = [
                        'type' => 'success',
                        'message' => 'Cập nhật danh mục "' . htmlspecialchars($name) . '" thành công!'
                    ];
                } else {
                    $_SESSION['flash'] = [
                        'type' => 'danger',
                        'message' => 'Có lỗi xảy ra khi cập nhật danh mục. Vui lòng thử lại!'
                    ];
                }
                header('Location: ?act=category-list'); // Chuyển hướng về danh sách
                exit;
            }
        }

        require_once './views/admin/categories/edit.php'; // Hiển thị form edit
    }

    /**
     * METHOD DELETE - XÓA DANH MỤC
     * Chức năng: Xóa danh mục theo ID
     * Sử dụng: Nút xóa danh mục trong admin
     * Luồng: Kiểm tra quyền → Validate ID → Lấy tên danh mục → Xóa → Thông báo → Redirect
     */
    public function delete()
    {
        if (!isset($_SESSION))
            session_start();
        // Kiểm tra quyền admin
        if (!isset($_SESSION['admin'])) {
            header('Location: ?act=login');
            exit;
        }

        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            // Lấy tên danh mục trước khi xóa để hiển thị thông báo
            $category = $this->categoryModel->find($id);
            $categoryName = $category['name'] ?? '';

            $result = $this->categoryModel->delete($id); // Xóa danh mục
            if ($result) {
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'Xóa danh mục "' . htmlspecialchars($categoryName) . '" thành công!'
                ];
            } else {
                $_SESSION['flash'] = [
                    'type' => 'danger',
                    'message' => 'Có lỗi xảy ra khi xóa danh mục. Vui lòng thử lại!'
                ];
            }
        } else {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'ID danh mục không hợp lệ!'
            ];
        }
        header('Location: ?act=category-list'); // Chuyển hướng về danh sách
        exit;
    }
}
?>