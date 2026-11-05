<?php
session_start();
require 'db.php'; // Kết nối CSDL biến $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_cancel'])) {
    if (!isset($_SESSION['user'])) {
        die("Bạn chưa đăng nhập");
    }

    $order_id = $_POST['order_id'];
    $user_id = $_SESSION['user']['id'];

    try {
        $pdo->beginTransaction();

        // 1. Kiểm tra đơn hàng xem có phải của user này và trạng thái pending không (có FOR UPDATE)
        $stmtCheck = $pdo->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ? AND status = 'pending' FOR UPDATE");
        $stmtCheck->execute([$order_id, $user_id]);
        
        if ($stmtCheck->rowCount() === 0) {
            throw new Exception("Đơn hàng không tồn tại, không thuộc quyền sở hữu hoặc không ở trạng thái pending.");
        }

        // 2. Đổi trạng thái thành cancelled
        $stmtUpdateStatus = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
        $stmtUpdateStatus->execute([$order_id]);

        // 3. Lấy danh sách sản phẩm trong đơn để cộng trả tồn kho
        $stmtGetItems = $pdo->prepare("SELECT product_id, quantity FROM order_details WHERE order_id = ?");
        $stmtGetItems->execute([$order_id]);
        $items = $stmtGetItems->fetchAll(PDO::FETCH_ASSOC);

        // 4. Cộng trả tồn kho cho từng sản phẩm
        $stmtUpdateStock = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        foreach ($items as $item) {
            $stmtUpdateStock->execute([$item['quantity'], $item['product_id']]);
        }

        $pdo->commit();
        $_SESSION['message'] = "Hủy đơn hàng thành công!";
        header("Location: order_detail.php?id=" . $order_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Lỗi: " . $e->getMessage());
    }
}
?>
