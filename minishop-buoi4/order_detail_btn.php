<?php if ($order['status'] === 'pending'): ?>
    <form method="POST" action="cancel_order.php" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn này?');">
        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
        <button type="submit" name="btn_cancel">Hủy đơn</button>
    </form>
<?php endif; ?>
