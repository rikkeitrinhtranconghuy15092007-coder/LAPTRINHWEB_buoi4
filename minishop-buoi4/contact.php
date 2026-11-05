<?php
require 'db.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    $stmt = $pdo->prepare("INSERT INTO contacts (fullname, email, subject, message) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$fullname, $email, $subject, $message])) {
        $msg = "Tin nhắn của bạn đã được gửi thành công!";
    }
}
?>
<!-- Form HTML -->
<?php if ($msg) echo "<p>$msg</p>"; ?>
<form method="POST">
    Họ tên: <input type="text" name="fullname" required><br>
    Email: <input type="email" name="email" required><br>
    Chủ đề: <input type="text" name="subject"><br>
    Tin nhắn: <textarea name="message" required></textarea><br>
    <button type="submit">Gửi liên hệ</button>
</form>
