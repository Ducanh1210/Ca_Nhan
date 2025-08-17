<?php
/**
 * FILE: controllers/CartController.php
 * CHỨC NĂNG: Controller quản lý giỏ hàng - xử lý logic nghiệp vụ liên quan đến giỏ hàng
 * LUỒNG CHẠY: 
 * - VÀO: Nhận request từ index.php với các action khác nhau (?act=cart-...)
 * - RA: Xử lý giỏ hàng và trả về kết quả hoặc chuyển hướng
 * - NGƯỢC LẠI: Tương tác với session cart và các model để quản lý giỏ hàng
 */
class CartController
{
    /**
     * METHOD ADD - THÊM SẢN PHẨM VÀO GIỎ HÀNG
     * Chức năng: Thêm sản phẩm vào giỏ hàng hoặc cập nhật số lượng nếu đã có
     * Sử dụng: Nút "Thêm vào giỏ" từ trang sản phẩm
     * Luồng: Kiểm tra ID sản phẩm → Tìm sản phẩm → Thêm/cập nhật giỏ hàng → Trả về JSON
     */
    public function add()
    {
        session_start(); // Khởi tạo session

        // Lấy ID sản phẩm từ URL parameter
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            echo json_encode(['error' => 'ID sản phẩm không hợp lệ']);
            exit;
        }

        // Tìm sản phẩm trong database
        $productModel = new Product();
        $product = $productModel->find($id);
        if (!$product) {
            echo json_encode(['error' => 'Sản phẩm không tồn tại']);
            exit;
        }

        // Khởi tạo giỏ hàng nếu chưa có
        if (!isset($_SESSION['cart']))
            $_SESSION['cart'] = [];

        // Lấy giới hạn số lượng tối đa cho mỗi sản phẩm
        $maxQty = defined('CART_ITEM_MAX_QTY') ? (int) CART_ITEM_MAX_QTY : 10;

        // Tìm xem sản phẩm đã có trong giỏ chưa
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ((int) $item['id'] === (int) $id) {
                $found = true;
                
                // Cập nhật biến thể nếu có gửi
                if (isset($_GET['variant_id'])) {
                    $variantId = (int) $_GET['variant_id'];
                    $variant = (new Variant())->find($variantId);
                    if ($variant && (int) $variant['product_id'] === (int) $id) {
                        $item['variant_id'] = $variantId;
                        $item['price'] = (int) $variant['price'];
                    }
                }
                
                // Kiểm tra giới hạn số lượng
                if ((int) $item['quantity'] >= $maxQty) {
                    // Đã đạt số lượng tối đa cho sản phẩm này
                    $uniqueCount = count($_SESSION['cart']);
                    $badge = min($uniqueCount, defined('CART_BADGE_CAP') ? CART_BADGE_CAP : 99);
                    echo json_encode(['error' => 'Bạn đã đạt số lượng tối đa cho sản phẩm này.', 'cartCount' => $badge]);
                    exit;
                }
                
                // Tăng số lượng
                $item['quantity'] = min($maxQty, ((int) $item['quantity']) + 1);
                break;
            }
        }

        // Nếu là sản phẩm mới, kiểm tra giới hạn số mặt hàng unique
        if (!$found) {
            $price = (int) $product['price'];
            $variantId = 0;
            
            // Xử lý biến thể nếu có
            if (isset($_GET['variant_id'])) {
                $variantRow = (new Variant())->find((int) $_GET['variant_id']);
                if ($variantRow && (int) $variantRow['product_id'] === (int) $id) {
                    $price = (int) $variantRow['price'];
                    $variantId = (int) $variantRow['id'];
                }
            }
            
            // Kiểm tra giới hạn số sản phẩm khác nhau trong giỏ
            if (count($_SESSION['cart']) >= (defined('CART_MAX_UNIQUE') ? CART_MAX_UNIQUE : 50)) {
                echo json_encode(['error' => 'Giỏ hàng đã đạt giới hạn sản phẩm.']);
                exit;
            }
            
            // Thêm sản phẩm mới vào giỏ
            $_SESSION['cart'][] = [
                'id' => (int) $id,
                'name' => $product['name'],
                'price' => $price,
                'img' => $product['img'],
                'variant_id' => $variantId,
                'quantity' => 1
            ];
        }

        // Trả về số lượng sản phẩm trong giỏ (badge)
        $uniqueCount = count($_SESSION['cart']);
        $badge = $uniqueCount;
        if (defined('CART_BADGE_CAP')) {
            $badge = min($badge, CART_BADGE_CAP);
        }
        echo json_encode(['cartCount' => $badge]);
        exit;
    }

    /**
     * METHOD VIEW - XEM GIỎ HÀNG
     * Chức năng: Chuyển hướng đến trang checkout để xem giỏ hàng
     * Sử dụng: Nút "Giỏ hàng" trong header
     * Luồng: Chuyển hướng đến trang checkout (dùng chung giao diện)
     */
    public function view()
    {
        session_start();
        // Dùng chung trang checkout làm giao diện giỏ hàng + thanh toán
        header('Location: ?act=checkout');
        exit;
    }

    /**
     * METHOD REMOVE - XÓA SẢN PHẨM KHỎI GIỎ HÀNG
     * Chức năng: Xóa một sản phẩm cụ thể khỏi giỏ hàng
     * Sử dụng: Nút "Xóa" trong giỏ hàng
     * Luồng: Lấy ID sản phẩm → Tìm và xóa khỏi session → Chuyển hướng về giỏ hàng
     */
    public function remove()
    {
        session_start();
        $id = $_GET['id'] ?? 0;
        
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $k => $item) {
                if ($item['id'] == $id) {
                    unset($_SESSION['cart'][$k]); // Xóa sản phẩm khỏi giỏ
                    break;
                }
            }
        }
        
        header('Location: ?act=cart-view');
        exit;
    }

    /**
     * METHOD UPDATE - CẬP NHẬT GIỎ HÀNG
     * Chức năng: Cập nhật số lượng và biến thể của các sản phẩm trong giỏ
     * Sử dụng: Form cập nhật giỏ hàng
     * Luồng: Nhận dữ liệu POST → Cập nhật session → Chuyển hướng về checkout
     */
    public function update()
    {
        session_start();
        
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                $id = $item['id'] ?? 0;
                $quantity = $item['quantity'] ?? 1;
                $maxQty = defined('CART_ITEM_MAX_QTY') ? (int) CART_ITEM_MAX_QTY : 10;
                
                // Tìm và cập nhật sản phẩm trong giỏ
                foreach ($_SESSION['cart'] as &$cartItem) {
                    if ($cartItem['id'] == $id) {
                        $cartItem['quantity'] = max(1, min($maxQty, intval($quantity)));
                        
                        // Cập nhật biến thể nếu có
                        if (isset($item['variant_id'])) {
                            $variantId = (int) $item['variant_id'];
                            if ($variantId > 0) {
                                $variantRow = (new Variant())->find($variantId);
                                if ($variantRow && (int) $variantRow['product_id'] === (int) $id) {
                                    $cartItem['variant_id'] = $variantId;
                                    $cartItem['price'] = (int) $variantRow['price'];
                                }
                            } else {
                                // Mặc định quay về giá gốc sản phẩm nếu chọn "mặc định"
                                $productRow = (new Product())->find($id);
                                if ($productRow) {
                                    $cartItem['variant_id'] = 0;
                                    $cartItem['price'] = (int) $productRow['price'];
                                }
                            }
                        }
                        break;
                    }
                }
            }
        }
        
        header('Location: ?act=checkout');
        exit;
    }

    /**
     * METHOD BUYNOW - MUA NGAY
     * Chức năng: Thêm sản phẩm vào giỏ hàng và chuyển ngay đến trang thanh toán
     * Sử dụng: Nút "Mua ngay" từ trang sản phẩm
     * Luồng: Lấy thông tin sản phẩm → Thêm vào giỏ → Chuyển hướng đến checkout
     */
    public function buyNow()
    {
        session_start();
        
        $id = $_GET['id'] ?? 0;
        $qty = isset($_GET['qty']) ? max(1, (int) $_GET['qty']) : 1;
        $maxQty = defined('CART_ITEM_MAX_QTY') ? (int) CART_ITEM_MAX_QTY : 10;
        $qty = min($qty, $maxQty); // Giới hạn số lượng
        
        if (!$id) {
            header('Location: ?act=home-client');
            exit;
        }

        // Tìm sản phẩm trong database
        $productModel = new Product();
        $product = $productModel->find($id);
        if (!$product) {
            header('Location: ?act=home-client');
            exit;
        }

        // Khởi tạo giỏ hàng nếu chưa có
        if (!isset($_SESSION['cart']))
            $_SESSION['cart'] = [];

        // Tìm xem sản phẩm đã có trong giỏ chưa
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $id) {
                $item['quantity'] = min($maxQty, ((int) $item['quantity']) + $qty);
                $found = true;
                break;
            }
        }

        // Nếu là sản phẩm mới, thêm vào giỏ
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $id,
                'name' => $product['name'],
                'price' => $product['price'],
                'img' => $product['img'],
                'quantity' => $qty
            ];
        }

        // Chuyển hướng đến trang thanh toán
        header('Location: ?act=checkout');
        exit;
    }
}