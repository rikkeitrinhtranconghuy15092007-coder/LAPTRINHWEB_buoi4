<?php
// --- Phần này nằm trong file chứa hàm require_login() (ví dụ functions.php) ---
function require_login() {
    if (!isset($_SESSION['user'])) {
        // Ghi nhớ URL hiện tại mà người dùng muốn vào
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit;
    }
}

// --- Phần này nằm trong file xử lý login.php (sau khi xác thực thành công) ---
/*
// ... Code kiểm tra user/pass thành công ở đây ...
$_SESSION['user'] = $user_data; // Lưu session user

// Kiểm tra xem có URL cũ cần quay lại không
if (isset($_SESSION['return_to'])) {
    $redirect_url = $_SESSION['return_to'];
    unset($_SESSION['return_to']); // Xóa đi sau khi đã lấy
} else {
    $redirect_url = 'index.php'; // Mặc định về trang chủ
}

header("Location: " . $redirect_url);
exit;
*/
?>
