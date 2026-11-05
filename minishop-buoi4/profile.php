<?php
session_start();
require 'db.php';

// Giả sử có hàm yêu cầu đăng nhập
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Cập nhật CSDL
    $stmt = $pdo->prepare("UPDATE users SET fullname = ?, phone = ?, address = ? WHERE id = ?");
    if ($stmt->execute([$fullname, $phone, $address, $user['id']])) {
        // QUAN TRỌNG: Cập nhật lại Session
        $_SESSION['user']['fullname'] = $fullname;
        $_SESSION['user']['phone'] = $phone;
        $_SESSION['user']['address'] = $address;
        
        $message = "Cập nhật thông tin thành công!";
        $user = $_SESSION['user']; // Cập nhật lại biến $user để hiển thị xuống form
    } else {
        $message = "Có lỗi xảy ra, vui lòng thử lại.";
    }
}
?>

<!DOCTYPE html>
<html>
<body>
    <h2>Trang tài khoản</h2>
    <?php if ($message) echo "<p>$message</p>"; ?>
    <form method="POST" action="">
        Họ tên: <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required><br>
        Số điện thoại: <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"><br>
        Địa chỉ: <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>"><br>
        <button type="submit">Lưu thông tin</button>
    </form>
</body>
</html>
