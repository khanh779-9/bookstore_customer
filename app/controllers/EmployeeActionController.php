<?php

class EmployeeController extends BaseController
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = \Database::getInstance();
    }

    public function handleEmployeeLogin() {
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_login');
        employeeLogin();
    }

    public function handleEmployeeLogout() {
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php');
        employeeLogout();
    }

    public function handleProductSave() {
        requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_products');

        $danhmucSP_id = isset($_POST['danhmucSP_id']) ? (int)$_POST['danhmucSP_id'] : null;
        if (!$danhmucSP_id && isset($_POST['type'])) {
            $danhmucSP_id = $_POST['type'] === 'Sach' ? 1 : 2;
        }
        if (!$danhmucSP_id) {
            $danhmucSP_id = 1;
        }
        $_POST['danhmucSP_id'] = $danhmucSP_id;

        $prepared = prepareProductData($_POST);
        $data = $prepared['data'];
        $is_sach = !empty($prepared['is_sach']);
        $type = $is_sach ? 'Sach' : 'VPP';

        // Server-side validation
        $errors = [];
        $title = trim($data['tenSach'] ?? $data['tenVPP'] ?? $data['name'] ?? '');
        if ($title === '') $errors[] = 'Tên sản phẩm không được để trống.';
        if (!is_numeric($data['gia'] ?? 0) || floatval($data['gia'] ?? 0) < 0) $errors[] = 'Giá không hợp lệ.';
        if (!is_int($data['soluongton'] ?? null) && !ctype_digit(strval($data['soluongton'] ?? '0'))) {
            $errors[] = 'Số lượng tồn phải là số nguyên.';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            error_log('Product save validation failed: ' . json_encode($errors) . ' Payload: ' . json_encode($data));
            redirect('index.php?page=employee_products');
        }

        // Log payload for debugging
        error_log('Product save payload: ' . json_encode($data));

        if(ProductModel::checkProductNameExists($type, $title)) {
            $_SESSION['error'] = 'Tên sản phẩm đã tồn tại trong hệ thống.';
            error_log('Product save failed: duplicate product name ' . $title);
            redirect('index.php?page=employee_products');
            return;
        }

        try {
            $sanpham_id = ProductModel::addProduct($type, $data);
            if ($sanpham_id && is_numeric($sanpham_id) && $sanpham_id > 0) {
                // Handle uploaded image file (if provided)
                if (!empty($_FILES['hinhanh_file']) && ($_FILES['hinhanh_file']['error'] ?? 1) === UPLOAD_ERR_OK) {
                    $upload = $_FILES['hinhanh_file'];
                    if (!is_uploaded_file($upload['tmp_name'])) {
                        error_log('Upload failed: not an uploaded file');
                    } else {
                        $maxSize = 2 * 1024 * 1024; // 2MB
                        if (($upload['size'] ?? 0) > $maxSize) {
                            error_log('Uploaded file too large: ' . ($upload['name'] ?? ''));
                        } else {
                            $origName = $upload['name'] ?? '';
                            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                            $allowedExt = ['jpg','jpeg','png','gif','webp'];
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mime = $finfo ? finfo_file($finfo, $upload['tmp_name']) : null;
                            if ($finfo) finfo_close($finfo);
                            $allowedMimes = ['image/jpeg','image/png','image/gif','image/webp'];

                            // basic checks: extension + mime type
                            if (in_array($ext, $allowedExt) && in_array($mime, $allowedMimes)) {
                                $danhmuc = (int)($data['danhmucSP_id'] ?? $danhmucSP_id ?? 1);
                                $filename = $sanpham_id . '-' . $danhmuc . '.' . $ext;
                                $targetDir = dirname(__DIR__, 2) . '/assets/images/products';
                                if (!is_dir($targetDir)) @mkdir($targetDir, 0755, true);
                                $targetPath = $targetDir . '/' . $filename;
                                // move and set safe permissions
                                if (move_uploaded_file($upload['tmp_name'], $targetPath)) {
                                    @chmod($targetPath, 0644);
                                    ProductModel::updateProduct($type, (int)$sanpham_id, ['hinhanh' => $filename]);
                                    $data['hinhanh'] = $filename;
                                } else {
                                    error_log('Failed to move uploaded file for product ' . $sanpham_id);
                                }
                            } else {
                                error_log('Uploaded file failed mime/extension checks: ' . $origName . ' mime=' . ($mime ?? 'unknown'));
                            }
                        }
                    }
                }

                $_SESSION['success'] = 'Thêm sản phẩm thành công!';
            } else {
                $_SESSION['error'] = 'Thêm sản phẩm thất bại!';
            }
        } catch (RuntimeException $re) {
            error_log('Product save runtime error: ' . $re->getMessage());
            $_SESSION['error'] = 'Lỗi hệ thống: ' . $re->getMessage();
        } catch (Exception $e) {
            error_log('Product save error: ' . $e->getMessage());
            $_SESSION['error'] = 'Thêm sản phẩm thất bại: ' . $e->getMessage();
        }

        redirect('index.php?page=employee_products');
    }

    public function handleProductUpdate() {
        requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_products');

        $product_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($product_id <= 0) {
            $_SESSION['error'] = 'ID sản phẩm không hợp lệ.';
            redirect('index.php?page=employee_products');
        }

        $product = ProductModel::getProductById($product_id);
        if (empty($product)) {
            $_SESSION['error'] = 'Sản phẩm không tồn tại.';
            redirect('index.php?page=employee_products');
        }

        $danhmucSP_id = isset($_POST['danhmucSP_id']) ? (int)$_POST['danhmucSP_id'] : ($product['danhmucSP_id'] ?? 1);
        $is_sach = is_book_category($danhmucSP_id);
        $type = $is_sach ? 'Sach' : 'VPP';

        $data = [];
        $fields = ['danhmucSP_id', 'hinhanh', 'mo_ta', 'soluongton', 'gia', 'nhacungcap_id'];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = in_array($field, ['danhmucSP_id', 'soluongton', 'nhacungcap_id'])
                    ? (int)$_POST[$field]
                    : ($field === 'gia' ? (float)$_POST[$field] : $_POST[$field]);
            }
        }

            if (isset($_POST['danhmucSP_id'])) {
            $newDanhmucSP_id = (int)$_POST['danhmucSP_id'];
            $newIsSach = is_book_category($newDanhmucSP_id);
            $oldIsSach = is_book_category((int)($product['danhmucSP_id'] ?? 1));

            if ($newIsSach !== $oldIsSach) {
                if (!$newIsSach) {
                    if (isset($_POST['tenVPP'])) {
                        $data['tenVPP'] = $_POST['tenVPP'];
                    }
                } else {
                    $sachFields = ['tenSach', 'nhaxuatban_id', 'tacgia_id', 'loaisach_code', 'namXB'];
                    foreach ($sachFields as $field) {
                        if (isset($_POST[$field])) {
                            $data[$field] = in_array($field, ['nhaxuatban_id', 'tacgia_id', 'namXB'])
                                ? (!empty($_POST[$field]) ? (int)$_POST[$field] : null)
                                : $_POST[$field];
                        }
                    }
                }
                $type = $newIsSach ? 'Sach' : 'VPP';
            } else {
                if ($is_sach) {
                    $sachFields = ['tenSach', 'nhaxuatban_id', 'tacgia_id', 'loaisach_code', 'namXB'];
                    foreach ($sachFields as $field) {
                        if (isset($_POST[$field])) {
                            $data[$field] = in_array($field, ['nhaxuatban_id', 'tacgia_id', 'namXB'])
                                ? (!empty($_POST[$field]) ? (int)$_POST[$field] : null)
                                : $_POST[$field];
                        }
                    }
                } else {
                    if (isset($_POST['tenVPP'])) {
                        $data['tenVPP'] = $_POST['tenVPP'];
                    }
                }
            }
        } else {
            if ($type === 'Sach') {
                $sachFields = ['tenSach', 'nhaxuatban_id', 'tacgia_id', 'loaisach_code', 'namXB'];
                foreach ($sachFields as $field) {
                    if (isset($_POST[$field])) {
                        $data[$field] = in_array($field, ['nhaxuatban_id', 'tacgia_id', 'namXB'])
                            ? (!empty($_POST[$field]) ? (int)$_POST[$field] : null)
                            : $_POST[$field];
                    }
                }
            } else {
                if (isset($_POST['tenVPP'])) {
                    $data['tenVPP'] = $_POST['tenVPP'];
                }
            }
        }

        // Accept simplified `name` input from the form and map to tenSach/tenVPP for updates
        if (isset($_POST['name']) && $_POST['name'] !== '') {
            if ($type === 'Sach') {
                $data['tenSach'] = $_POST['name'];
            } else {
                $data['tenVPP'] = $_POST['name'];
            }
        }

        // Server-side validation for update
        $errors = [];
        $title = trim($data['tenSach'] ?? $data['tenVPP'] ?? $data['name'] ?? '');
        if ($title === '') $errors[] = 'Tên sản phẩm không được để trống.';
        if (isset($data['gia']) && (!is_numeric($data['gia']) || floatval($data['gia']) < 0)) $errors[] = 'Giá không hợp lệ.';
        if (isset($data['soluongton']) && (!is_int($data['soluongton']) && !ctype_digit(strval($data['soluongton'])))) $errors[] = 'Số lượng tồn phải là số nguyên.';

        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            error_log('Product update validation failed: ' . json_encode($errors) . ' Payload: ' . json_encode($data));
            redirect('index.php?page=employee_products');
        }

        // Log payload for debugging
        error_log('Product update payload: ' . json_encode(['id' => $product_id, 'type' => $type, 'data' => $data]));

        // Handle uploaded image file (if provided) - use product id and current/new category id
        if (!empty($_FILES['hinhanh_file']) && ($_FILES['hinhanh_file']['error'] ?? 1) === UPLOAD_ERR_OK) {
            $upload = $_FILES['hinhanh_file'];
            if (!is_uploaded_file($upload['tmp_name'])) {
                error_log('Upload update failed: not an uploaded file');
            } else {
                $maxSize = 2 * 1024 * 1024; // 2MB
                if (($upload['size'] ?? 0) > $maxSize) {
                    error_log('Uploaded file too large (update): ' . ($upload['name'] ?? ''));
                } else {
                    $origName = $upload['name'] ?? '';
                    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                    $allowedExt = ['jpg','jpeg','png','gif','webp'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = $finfo ? finfo_file($finfo, $upload['tmp_name']) : null;
                    if ($finfo) finfo_close($finfo);
                    $allowedMimes = ['image/jpeg','image/png','image/gif','image/webp'];

                    if (in_array($ext, $allowedExt) && in_array($mime, $allowedMimes)) {
                        $danhmuc = (int)($data['danhmucSP_id'] ?? $danhmucSP_id ?? ($product['danhmucSP_id'] ?? 1));
                        $filename = $product_id . '-' . $danhmuc . '.' . $ext;
                        $targetDir = dirname(__DIR__, 2) . '/assets/images/products';
                        if (!is_dir($targetDir)) @mkdir($targetDir, 0755, true);
                        $targetPath = $targetDir . '/' . $filename;
                        if (move_uploaded_file($upload['tmp_name'], $targetPath)) {
                            @chmod($targetPath, 0644);
                            $data['hinhanh'] = $filename;
                        } else {
                            error_log('Failed to move uploaded file for product update ' . $product_id);
                        }
                    } else {
                        error_log('Uploaded file failed mime/extension checks (update): ' . $origName . ' mime=' . ($mime ?? 'unknown'));
                    }
                }
            }
        }

        $result = ProductModel::updateProduct($type, $product_id, $data);
        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Cập nhật sản phẩm thành công!'
            : 'Cập nhật sản phẩm thất bại!';

        redirect('index.php?page=employee_products');
    }

    public function handleProductDelete() {
        requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_products');

        // Accept id from POST (preferred) or GET
        $product_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($product_id <= 0) {
            $_SESSION['error'] = 'ID sản phẩm không hợp lệ.';
            redirect('index.php?page=employee_products');
        }

        $product = ProductModel::getProductById($product_id);
        if (empty($product)) {
            $_SESSION['error'] = 'Sản phẩm không tồn tại.';
            redirect('index.php?page=employee_products');
        }

        $type = is_book_category($product['danhmucSP_id'] ?? 0) ? 'Sach' : 'VPP';
        $result = ProductModel::deleteProduct($type, $product_id);

        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Xóa sản phẩm thành công!'
            : 'Xóa sản phẩm thất bại!';

        redirect('index.php?page=employee_products');
    }

    public function handleOrderStatusUpdate() {
        requireEmployeeLogin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_orders');

        $order_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($order_id <= 0) {
            $_SESSION['error'] = 'ID đơn hàng không hợp lệ.';   
            redirect('index.php?page=employee_orders');
        }

        $trangthai = $_POST['trangthai'] ?? 'cho_xac_nhan';
        // Cập nhật nhân viên duyệt với session chuẩn
        $nhanvien_id = $_SESSION['employee_account']['id'] ?? null;
        $result = OrdersModel::updateOrderStatus($order_id, $trangthai, $nhanvien_id);

        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Cập nhật trạng thái đơn hàng thành công!'
            : 'Cập nhật trạng thái đơn hàng thất bại!';

        redirect('index.php?page=employee_orders&subpage=view&id=' . $order_id);
    }

    public function handleOrderCreate() {
        requireEmployeeLogin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_orders');

        // Determine customer: existing or create new
        $customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
        if ($customer_id <= 0) {
            // create new customer from provided fields
            $new = [
                'ho' => $_POST['new_name'] ?? '',
                'ten' => '',
                'email' => $_POST['new_email'] ?? null,
                'sdt' => $_POST['new_phone'] ?? null,
                'diachi' => $_POST['new_address'] ?? null,
                'password' => null
            ];
            $created = CustomerModel::createCustomer($new);
            if (!$created) {
                $_SESSION['error'] = 'Không thể tạo khách hàng mới.';
                redirect('index.php?page=employee_orders&subpage=create');
            }
            $customer_id = (int)$created;
        }

        // collect items
        $items = [];
        $productIds = $_POST['product_id'] ?? [];
        $quantities = $_POST['soluong'] ?? [];
        if (!is_array($productIds) || empty($productIds)) {
            $_SESSION['error'] = 'Vui lòng thêm ít nhất một sản phẩm cho đơn hàng.';
            redirect('index.php?page=employee_orders&subpage=create');
        }
        $stockErrors = [];
        foreach ($productIds as $i => $pid) {
            $pid = (int)$pid;
            $qty = isset($quantities[$i]) ? (int)$quantities[$i] : 0;
            if ($pid <= 0 || $qty <= 0) continue;
            // fetch current product to check stock and default price
            $p = ProductModel::getProductById($pid);
            $currentStock = (int)($p['soluongton'] ?? 0);
            $productName = $p['name'] ?? $p['tenSach'] ?? $p['tenVPP'] ?? ('#' . $pid);
            if ($qty > $currentStock) {
                $stockErrors[] = "'{$productName}' (yêu cầu: {$qty}, tồn: {$currentStock})";
                // still continue to collect other errors
                continue;
            }
            // price override from form if provided
            $postedPrice = isset($_POST['gia'][$i]) ? $_POST['gia'][$i] : null;
            if ($postedPrice !== null && is_numeric($postedPrice)) {
                $price = floatval($postedPrice);
            } else {
                $price = floatval($p['gia'] ?? 0);
            }
            $items[] = ['product_id' => $pid, 'soluong' => $qty, 'gia' => $price];
        }

        if (!empty($stockErrors)) {
            $_SESSION['error'] = 'Không thể tạo đơn do tồn kho không đủ cho: ' . implode(', ', $stockErrors) . '.';
            redirect('index.php?page=employee_orders&subpage=create');
        }

        if (empty($items)) {
            $_SESSION['error'] = 'Không có sản phẩm hợp lệ trong đơn hàng.';
            redirect('index.php?page=employee_orders&subpage=create');
        }

        $payment = $_POST['payment_method'] ?? 'tien_mat';
        $address = $_POST['shipping_address'] ?? null;

        $hoadon_id = OrdersModel::createOrder($customer_id, $items, $payment, $address);
        if ($hoadon_id === false) {
            $_SESSION['error'] = 'Tạo đơn hàng thất bại.';
            redirect('index.php?page=employee_orders&subpage=create');
        }

        $_SESSION['success'] = 'Tạo đơn hàng thành công!';
        redirect('index.php?page=employee_orders&subpage=view&id=' . $hoadon_id);
    }

    // AJAX: return addresses for a customer as JSON
    public function handleGetCustomerAddresses() {
        requireEmployeeLogin();
        $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;
        if ($customer_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Customer id required']);
            exit;
        }
        $addresses = [];
        if (class_exists('AddressesModel')) {
            try { $addresses = AddressesModel::getAddressesByCustomer($customer_id); } catch (Throwable $e) { $addresses = []; }
        }
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'addresses' => $addresses]);
        exit;
    }

    // AJAX create order (returns JSON). Similar checks to handleOrderCreate.
    public function handleOrderCreateAjax() {
        requireEmployeeLogin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_orders', true);

        // Reuse logic from handleOrderCreate but return JSON instead of redirecting
        $customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
        if ($customer_id <= 0) {
            $new = [
                'ho' => $_POST['new_name'] ?? '',
                'ten' => '',
                'email' => $_POST['new_email'] ?? null,
                'sdt' => $_POST['new_phone'] ?? null,
                'diachi' => $_POST['new_address'] ?? null,
                'password' => null
            ];
            $created = CustomerModel::createCustomer($new);
            if (!$created) {
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'message' => 'Không thể tạo khách hàng mới.']); exit;
            }
            $customer_id = (int)$created;
        }

        $items = [];
        $productIds = $_POST['product_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        if (!is_array($productIds) || empty($productIds)) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Vui lòng thêm ít nhất một sản phẩm cho đơn hàng.']); exit;
        }

        $stockErrors = [];
        foreach ($productIds as $i => $pid) {
            $pid = (int)$pid;
            $qty = isset($quantities[$i]) ? (int)$quantities[$i] : 0;
            if ($pid <= 0 || $qty <= 0) continue;
            $p = ProductModel::getProductById($pid);
            $currentStock = (int)($p['soluongton'] ?? 0);
            $productName = $p['name'] ?? $p['tenSach'] ?? $p['tenVPP'] ?? ('#' . $pid);
            if ($qty > $currentStock) { $stockErrors[] = "{$productName} (yêu cầu: {$qty}, tồn: {$currentStock})"; continue; }
            $postedPrice = isset($_POST['price'][$i]) ? $_POST['price'][$i] : null;
            if ($postedPrice !== null && is_numeric($postedPrice)) { $price = floatval($postedPrice); } else { $price = floatval($p['gia'] ?? 0); }
            $items[] = ['product_id' => $pid, 'quantity' => $qty, 'price' => $price];
        }

        if (!empty($stockErrors)) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Tồn kho không đủ cho: ' . implode(', ', $stockErrors)]);
            exit;
        }

        if (empty($items)) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Không có sản phẩm hợp lệ trong đơn hàng.']); exit;
        }

        $payment = $_POST['payment_method'] ?? 'tien_mat';
        $address = $_POST['shipping_address'] ?? null;

        $hoadon_id = OrdersModel::createOrder($customer_id, $items, $payment, $address);
        if ($hoadon_id === false) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Tạo đơn hàng thất bại.']); exit;
        }

        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'hoadon_id' => $hoadon_id, 'message' => 'Tạo đơn hàng thành công']);
        exit;
    }

    public function handleEmployeeSave() {
        requireEmployeeAdmin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_employees');

        $data = [
            'ho' => $_POST['ho'] ?? '',
            'tendem' => $_POST['tendem'] ?? null,
            'ten' => $_POST['ten'] ?? '',
            'email' => $_POST['email'] ?? '',
            'sdt' => $_POST['sdt'] ?? null,
            'gioitinh' => $_POST['gioitinh'] ?? null,
            'ngaysinh' => $_POST['ngaysinh'] ?: null,
            'diachi' => $_POST['diachi'] ?? null,
            'ngayvaolam' => $_POST['ngayvaolam'] ?? date('Y-m-d'),
            'trangthai' => $_POST['trangthai'] ?? 'dang_lam',
            'role' => $_POST['role'] ?? 'nhanvien',
            'ghichu' => $_POST['ghichu'] ?? null,
            'password' => $_POST['password'] ?? '123456'
        ];

        $result = EmployeeModel:: createEmployee($data);
        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Thêm nhân viên thành công!'
            : 'Thêm nhân viên thất bại!';

        redirect('index.php?page=employee_employees');
    }

    public function handleEmployeeUpdate() {
        requireEmployeeLogin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_employees');
        $employee_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $current_role = $_SESSION['employee_account']['role'] ?? 'nhanvien';
        $current_id = $_SESSION['employee_account']['id'] ?? 0;

        if ($employee_id <= 0) {
            $_SESSION['error'] = 'ID nhân viên không hợp lệ.';
            redirect('index.php?page=employee_employees');
        }

        if ($current_role !== 'admin' && $employee_id != $current_id) {
            $_SESSION['error'] = 'Bạn không có quyền sửa thông tin nhân viên này.';
            redirect('index.php?page=employee_employees');
        }

        $data = [];
        $allowedFields = ['ho', 'tendem', 'ten', 'email', 'sdt', 'gioitinh', 'ngaysinh', 'diachi', 'ghichu'];
        foreach ($allowedFields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = $_POST[$field] ?: null;
            }
        }

        if ($current_role === 'admin') {
            if (isset($_POST['role'])) $data['role'] = $_POST['role'];
            if (isset($_POST['trangthai'])) $data['trangthai'] = $_POST['trangthai'];
            if (isset($_POST['ngayvaolam'])) $data['ngayvaolam'] = $_POST['ngayvaolam'];
        }

        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }

        $result = EmployeeModel::updateEmployee($employee_id, $data);
        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Cập nhật thông tin nhân viên thành công!'
            : 'Cập nhật thông tin nhân viên thất bại!';

        if ($result && $employee_id == $current_id) {
            $updated = EmployeeModel::getEmployeeById($employee_id);
            $_SESSION['employee_account']['ho'] = $updated['ho'] ?? '';
            $_SESSION['employee_account']['tendem'] = $updated['tendem'] ?? '';
            $_SESSION['employee_account']['ten'] = $updated['ten'] ?? '';
            $_SESSION['employee_account']['ho_ten'] = trim(($updated['ho'] ?? '') . ' ' . ($updated['tendem'] ?? '') . ' ' . ($updated['ten'] ?? ''));
            $_SESSION['employee_account']['email'] = $updated['email'] ?? '';
        }

        redirect($employee_id == $current_id ? 'index.php?page=employee_profile' : 'index.php?page=employee_employees');
    }

    public function handleEmployeeDelete() {
        requireEmployeeAdmin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_employees');

        $employee_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($employee_id <= 0) {
            $_SESSION['error'] = 'ID nhân viên không hợp lệ.';
            redirect('index.php?page=employee_employees');
        }

        if ($employee_id == $_SESSION['employee_account']['id']) {
            $_SESSION['error'] = 'Bạn không thể xóa chính mình.';
        } else {
            $result = EmployeeModel::deleteEmployee($employee_id);
            $_SESSION[$result ? 'success' : 'error'] = $result
                ? 'Xóa nhân viên thành công!'
                : 'Xóa nhân viên thất bại!';
        }

        redirect('index.php?page=employee_employees');
    }

    public function handleEmployeeProfileUpdate() {
        requireEmployeeLogin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_profile');

        $employee_id = $_SESSION['employee_account']['id'] ?? null;
        if (!$employee_id) {
            $_SESSION['error'] = 'Phiên đăng nhập không hợp lệ.';
            redirect('index.php?page=employee_login');
        }

        $data = [];
        $allowedFields = ['ho', 'tendem', 'ten', 'email', 'sdt', 'gioitinh', 'ngaysinh', 'diachi'];
        foreach ($allowedFields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = $_POST[$field] ?: null;
            }
        }

        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }

        $result = EmployeeModel::updateEmployee($employee_id, $data);
        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Cập nhật thông tin thành công!'
            : 'Cập nhật thông tin thất bại!';

        if ($result) {
            $updated = EmployeeModel::getEmployeeById($employee_id);
            $_SESSION['employee_account']['ho'] = $updated['ho'] ?? '';
            $_SESSION['employee_account']['tendem'] = $updated['tendem'] ?? '';
            $_SESSION['employee_account']['ten'] = $updated['ten'] ?? '';
            $_SESSION['employee_account']['ho_ten'] = trim(($updated['ho'] ?? '') . ' ' . ($updated['tendem'] ?? '') . ' ' . ($updated['ten'] ?? ''));
            $_SESSION['employee_account']['email'] = $updated['email'] ?? '';
        }

        redirect('index.php?page=employee_profile');
    }


    // Các hàm xử lý nhà xuất bản
    public function handlePublisherSave() {
        requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_publishers');

        $data = [
            'tenNhaXuatBan' => $_POST['tenNhaXuatBan'] ?? '',
            'diachi' => $_POST['diachi'] ?? null,
            'sdt' => $_POST['sdt'] ?? null,
            'email' => $_POST['email'] ?? null,
            'website' => $_POST['website'] ?? null,
            'ghichu' => $_POST['ghichu'] ?? null
        ];

        // Normalize to model expected keys
        if (!isset($data['ten']) && isset($data['tenNhaXuatBan'])) {
            $data['ten'] = $data['tenNhaXuatBan'];
        }

        $result = PublisherModel::createPublisher($data);
        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Thêm nhà xuất bản thành công!'
            : 'Thêm nhà xuất bản thất bại!';

        redirect('index.php?page=employee_publishers');
    }

    public function handlePublisherUpdate() {
        requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_publishers');

        $publisher_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($publisher_id <= 0) {
            $_SESSION['error'] = 'ID nhà xuất bản không hợp lệ.';
            redirect('index.php?page=employee_publishers');
        }

        $data = [];
        $allowedFields = ['ten', 'diachi', 'sdt', 'email'];
        foreach ($allowedFields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = $_POST[$field] ?: null;
            } elseif ($field === 'ten' && isset($_POST['tenNhaXuatBan'])) {
                $data['ten'] = $_POST['tenNhaXuatBan'] ?: null;
            }
        }

        $result = PublisherModel::updatePublisher($publisher_id, $data);
        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Cập nhật nhà xuất bản thành công!'
            : 'Cập nhật nhà xuất bản thất bại!';

        redirect('index.php?page=employee_publishers');
    }

    public function handlePublisherDelete() {
        requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_publishers');

        $publisher_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($publisher_id <= 0) {
            $_SESSION['error'] = 'ID nhà xuất bản không hợp lệ.';
            redirect('index.php?page=employee_publishers');
        }

        $result = PublisherModel::deletePublisher($publisher_id);
        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Xóa nhà xuất bản thành công!'
            : 'Xóa nhà xuất bản thất bại!';

        redirect('index.php?page=employee_publishers');
    }

    // Các hàm xử lý nhà cung cấp
    public function handleProviderSave() {
        requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_providers');

        $data = [
            'ten' => $_POST['ten'] ?? '',
            'diachi' => $_POST['diachi'] ?? null,
            'sdt' => $_POST['sdt'] ?? null,
            'email' => $_POST['email'] ?? null
        ];

        $result = ProviderModel::createProvider($data);
        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Thêm nhà cung cấp thành công!'
            : 'Thêm nhà cung cấp thất bại!';

        redirect('index.php?page=employee_providers');
    }

    // Các hàm xử lý danh mục sản phẩm
    public function handleCategorySave() {
        requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_categories');

        $data = [
            'tenDanhMuc' => $_POST['tenDanhMuc'] ?? ($_POST['ten'] ?? ''),
            'mo_ta' => $_POST['mo_ta'] ?? null
        ];

        $result = CategoriesModel::createCategory($data);
        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Thêm danh mục thành công!'
            : 'Thêm danh mục thất bại!';

        redirect('index.php?page=employee_categories');
    }

    public function handleCategoryUpdate() {
        requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_categories');

        $category_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($category_id <= 0) {
            $_SESSION['error'] = 'ID danh mục không hợp lệ.';
            redirect('index.php?page=employee_categories');
        }

        $data = [];
        if (isset($_POST['tenDanhMuc'])) $data['tenDanhMuc'] = $_POST['tenDanhMuc'] ?: '';
        if (!isset($data['tenDanhMuc']) && isset($_POST['ten'])) $data['tenDanhMuc'] = $_POST['ten'] ?: '';
        if (isset($_POST['mo_ta'])) $data['mo_ta'] = $_POST['mo_ta'] ?: null;

        $result = CategoriesModel::updateCategory($category_id, $data);
        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Cập nhật danh mục thành công!'
            : 'Cập nhật danh mục thất bại!';

        redirect('index.php?page=employee_categories');
    }

    public function handleCategoryDelete() {
        requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_categories');

        $category_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($category_id <= 0) {
            $_SESSION['error'] = 'ID danh mục không hợp lệ.';
            redirect('index.php?page=employee_categories');
        }

        $result = CategoriesModel::deleteCategory($category_id);
        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Xóa danh mục thành công!'
            : 'Xóa danh mục thất bại!';

        redirect('index.php?page=employee_categories');
    }

    public function handleProviderUpdate() {
        requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_providers');

        $provider_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($provider_id <= 0) {
            $_SESSION['error'] = 'ID nhà cung cấp không hợp lệ.';
            redirect('index.php?page=employee_providers');
        }

        $data = [];
        $allowedFields = ['ten', 'diachi', 'sdt', 'email'];
        foreach ($allowedFields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = $_POST[$field] ?: null;
            }
        }

        $result = ProviderModel::updateProvider($provider_id, $data);
        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Cập nhật nhà cung cấp thành công!'
            : 'Cập nhật nhà cung cấp thất bại!';

        redirect('index.php?page=employee_providers');
    }

    public function handleProviderDelete() {
        requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_providers');

        $provider_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($provider_id <= 0) {
            $_SESSION['error'] = 'ID nhà cung cấp không hợp lệ.';
            redirect('index.php?page=employee_providers');
        }

        $result = ProviderModel::deleteProvider($provider_id);
        $_SESSION[$result ? 'success' : 'error'] = $result
            ? 'Xóa nhà cung cấp thành công!'
            : 'Xóa nhà cung cấp thất bại!';

        redirect('index.php?page=employee_providers');
    }

    // ==================== PROMOTION HANDLERS ====================
    
    public function handlePromotionSave() {
        requireEmployeeLogin();
        EmployeeAuthController::requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_promotions');

        $data = [
            'ten' => trim($_POST['ten'] ?? ''),
            'ngaybatdau' => $_POST['ngaybatdau'] ?? '',
            'ngayketthuc' => $_POST['ngayketthuc'] ?? ''
        ];

        if (empty($data['ten']) || empty($data['ngaybatdau']) || empty($data['ngayketthuc'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin khuyến mãi!';
            redirect('index.php?page=employee_promotions&subpage=add');
        }

        try {
            $promotion_id = PromotionModel::createPromotion($data);
            
            // Add promotion details if provided
            if (!empty($_POST['sanpham_id']) && is_array($_POST['sanpham_id'])) {
                foreach ($_POST['sanpham_id'] as $index => $sanpham_id) {
                    if (!empty($sanpham_id)) {
                        $detailData = [
                            'khuyenmai_id' => $promotion_id,
                            'sanpham_id' => (int)$sanpham_id,
                            'soluong' => (int)($_POST['soluong'][$index] ?? 0),
                            'tilegiamgia' => (float)($_POST['tilegiamgia'][$index] ?? 0)
                        ];
                        PromotionModel::addPromotionDetail($detailData);
                    }
                }
            }

            $_SESSION['success'] = 'Thêm khuyến mãi thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Thêm khuyến mãi thất bại: ' . $e->getMessage();
        }

        redirect('index.php?page=employee_promotions');
    }

    public function handlePromotionUpdate() {
        requireEmployeeLogin();
        EmployeeAuthController::requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_promotions');

        $promotion_id = (int)($_POST['promotion_id'] ?? 0);
        if ($promotion_id <= 0) {
            $_SESSION['error'] = 'ID khuyến mãi không hợp lệ.';
            redirect('index.php?page=employee_promotions');
        }

        $data = [
            'ten' => trim($_POST['ten'] ?? ''),
            'ngaybatdau' => $_POST['ngaybatdau'] ?? '',
            'ngayketthuc' => $_POST['ngayketthuc'] ?? ''
        ];

        if (empty($data['ten']) || empty($data['ngaybatdau']) || empty($data['ngayketthuc'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin khuyến mãi!';
            redirect('index.php?page=employee_promotions&subpage=edit&id=' . $promotion_id);
        }

        try {
            PromotionModel::updatePromotion($promotion_id, $data);
            $_SESSION['success'] = 'Cập nhật khuyến mãi thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Cập nhật khuyến mãi thất bại: ' . $e->getMessage();
        }

        redirect('index.php?page=employee_promotions');
    }

    public function handlePromotionDelete() {
        requireEmployeeLogin();
        EmployeeAuthController::requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_promotions');

        $promotion_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($promotion_id <= 0) {
            $_SESSION['error'] = 'ID khuyến mãi không hợp lệ.';
            redirect('index.php?page=employee_promotions');
        }

        try {
            $result = PromotionModel::deletePromotion($promotion_id);
            $_SESSION[$result ? 'success' : 'error'] = $result
                ? 'Xóa khuyến mãi thành công!'
                : 'Xóa khuyến mãi thất bại!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Xóa khuyến mãi thất bại: ' . $e->getMessage();
        }

        redirect('index.php?page=employee_promotions');
    }

    public function handlePromotionDetailAdd() {
        requireEmployeeLogin();
        EmployeeAuthController::requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_promotions');

        $data = [
            'khuyenmai_id' => (int)($_POST['khuyenmai_id'] ?? 0),
            'sanpham_id' => (int)($_POST['sanpham_id'] ?? 0),
            'soluong' => (int)($_POST['soluong'] ?? 0),
            'tilegiamgia' => (float)($_POST['tilegiamgia'] ?? 0)
        ];

        if ($data['khuyenmai_id'] <= 0 || $data['sanpham_id'] <= 0) {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ!';
            redirect('index.php?page=employee_promotions&subpage=edit&id=' . $data['khuyenmai_id']);
        }

        try {
            PromotionModel::addPromotionDetail($data);
            $_SESSION['success'] = 'Thêm sản phẩm vào khuyến mãi thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Thêm sản phẩm thất bại: ' . $e->getMessage();
        }

        redirect('index.php?page=employee_promotions&subpage=edit&id=' . $data['khuyenmai_id']);
    }

    public function handlePromotionDetailDelete() {
        requireEmployeeLogin();
        EmployeeAuthController::requireEmployeeOrManager();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=employee_promotions');

        $ctkm_id = (int)($_POST['ctkm_id'] ?? 0);
        $khuyenmai_id = (int)($_POST['khuyenmai_id'] ?? 0);

        if ($ctkm_id <= 0) {
            $_SESSION['error'] = 'ID chi tiết khuyến mãi không hợp lệ!';
            redirect('index.php?page=employee_promotions');
        }

        try {
            $result = PromotionModel::deletePromotionDetail($ctkm_id);
            $_SESSION[$result ? 'success' : 'error'] = $result
                ? 'Xóa sản phẩm khỏi khuyến mãi thành công!'
                : 'Xóa sản phẩm thất bại!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Xóa sản phẩm thất bại: ' . $e->getMessage();
        }

        redirect('index.php?page=employee_promotions&subpage=edit&id=' . $khuyenmai_id);
    }

    public function routeEmployeeActions($page, $action = '') {
        switch ($page) {
            case 'employee_login':
                $this->handleEmployeeLogin();
                break;
            case 'employee_logout':
                $this->handleEmployeeLogout();
                break;
            case 'employee_products':
                switch ($action) {
                    case 'save':
                        $this->handleProductSave();
                        break;
                    case 'update':
                        $this->handleProductUpdate();
                        break;
                    case 'delete':
                        $this->handleProductDelete();
                        break;
                }
                break;
            case 'employee_publishers':
            case 'admin_publishers':
                switch ($action) {
                    case 'save':
                        $this->handlePublisherSave();
                        break;
                    case 'update':
                        $this->handlePublisherUpdate();
                        break;
                    case 'delete':
                        $this->handlePublisherDelete();
                        break;
                }
                break;
            case 'employee_providers':
            case 'admin_providers':
                switch ($action) {
                    case 'save':
                        $this->handleProviderSave();
                        break;
                    case 'update':
                        $this->handleProviderUpdate();
                        break;
                    case 'delete':
                        $this->handleProviderDelete();
                        break;
                }
                break;
            case 'employee_categories':
            case 'admin_categories':
                switch ($action) {
                    case 'save':
                        $this->handleCategorySave();
                        break;
                    case 'update':
                        $this->handleCategoryUpdate();
                        break;
                    case 'delete':
                        $this->handleCategoryDelete();
                        break;
                }
                break;
            case 'employee_promotions':
            case 'admin_promotions':
                switch ($action) {
                    case 'save':
                        $this->handlePromotionSave();
                        break;
                    case 'update':
                        $this->handlePromotionUpdate();
                        break;
                    case 'delete':
                        $this->handlePromotionDelete();
                        break;
                    case 'detail_add':
                        $this->handlePromotionDetailAdd();
                        break;
                    case 'detail_delete':
                        $this->handlePromotionDetailDelete();
                        break;
                }
                break;
            case 'employee_orders':
            case 'admin_orders':
                if ($action === 'update_status') {
                    $this->handleOrderStatusUpdate();
                } elseif ($action === 'create') {
                    $this->handleOrderCreate();
                } elseif ($action === 'get_addresses') {
                    $this->handleGetCustomerAddresses();
                } elseif ($action === 'create_ajax') {
                    $this->handleOrderCreateAjax();
                }
                break;
            
        case 'employee_employees':
        case 'admin_employees':
            switch ($action) {
                case 'save':
                    $this->handleEmployeeSave();
                    break;
                case 'update':
                    $this->handleEmployeeUpdate();
                    break;
                case 'delete':
                    $this->handleEmployeeDelete();
                    break;
            }
            break;
        case 'employee_profile':
            if ($action === 'update') {
                $this->handleEmployeeProfileUpdate();
            }
            break;
        }
    }
}
