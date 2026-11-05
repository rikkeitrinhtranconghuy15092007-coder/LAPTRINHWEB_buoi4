# CÂU HỎI ÔN TẬP - LẬP TRÌNH WEB BUỔI 4

**1. HTTP không có trạng thái nghĩa là gì? Cookie và session phối hợp thế nào để khắc phục?**
- HTTP không có trạng thái (stateless) nghĩa là mỗi request từ client gửi lên server đều độc lập, server không "nhớ" bất kỳ thông tin nào về các request trước đó của cùng một người dùng.
- Khắc phục: Server tạo ra một Session (phiên làm việc) và lưu dữ liệu trên server, đồng thời cấp một mã định danh (Session ID). Mã này được gửi về client và lưu dưới dạng Cookie. Trong các request tiếp theo, trình duyệt tự động gửi kèm Cookie (chứa Session ID) lên server để server nhận diện được người dùng và lấy đúng dữ liệu Session tương ứng.

**2. Dữ liệu session nằm ở đâu? Vì sao có thể tin dữ liệu trong `$_SESSION` hơn dữ liệu trong `$_POST`?**
- Dữ liệu session được lưu trữ ở phía **Server** (thường là trong các file tạm, RAM hoặc database).
- Có thể tin `$_SESSION` hơn `$_POST` vì `$_POST` được gửi trực tiếp từ phía client (trình duyệt) lên, người dùng hoặc hacker có thể dễ dàng can thiệp, chỉnh sửa dữ liệu trước khi gửi đi. Ngược lại, `$_SESSION` được quản lý hoàn toàn trên server, người dùng chỉ giữ Session ID nên không thể tự ý thay đổi giá trị thật của session.

**3. Mẫu Post/Redirect/Get giải quyết vấn đề gì?**
- Mẫu PRG (Post/Redirect/Get) giúp giải quyết vấn đề **gửi lặp dữ liệu form (Duplicate Form Submission)**. Nếu sau khi xử lý POST, ta chỉ render HTML trả về, khi người dùng nhấn F5 (Refresh), trình duyệt sẽ hỏi có muốn gửi lại dữ liệu POST không. Bằng cách dùng Redirect (chuyển hướng) sang một trang GET sau khi xử lý POST thành công, việc Refresh sẽ chỉ load lại trang GET đó.

**4. Vì sao phải kiểm tra dữ liệu ở máy chủ khi HTML và JavaScript đã kiểm tra rồi?**
- Kiểm tra ở HTML/JS (Client-side validation) chỉ mang tính trải nghiệm người dùng (UX), giúp báo lỗi nhanh. Tuy nhiên, client-side rất dễ bị qua mặt (hacker có thể tắt JavaScript, dùng tool như Postman, cURL để gửi request trực tiếp bỏ qua form). Do đó, máy chủ (Server-side) là chốt chặn cuối cùng bắt buộc phải kiểm tra lại để đảm bảo an toàn và tính toàn vẹn dữ liệu.

**5. Vì sao không lưu mật khẩu nguyên văn? Vì sao không thể "giải mã" kết quả của `password_hash`?**
- Không lưu nguyên văn (plaintext) vì nếu database bị lộ (hack, rò rỉ), kẻ gian sẽ lấy được toàn bộ mật khẩu thật của người dùng.
- Không thể "giải mã" kết quả của `password_hash` vì nó sử dụng **hàm băm một chiều (one-way hashing)** như bcrypt/Argon2. Quá trình băm biến đổi chuỗi thành một đoạn mã cố định không có thuật toán dịch ngược (không phải là mã hóa - encryption). Để kiểm tra, ta dùng hàm `password_verify` băm mật khẩu nhập vào và so sánh 2 mã băm với nhau.

**6. Vì sao giỏ hàng chỉ lưu mã sản phẩm và số lượng mà không lưu giá?**
- Giá sản phẩm thường xuyên biến động (tăng/giảm, khuyến mãi). Nếu lưu thẳng giá vào giỏ hàng, người dùng có thể mua hàng với giá cũ không còn hiệu lực. Chỉ lưu ID và số lượng để khi thanh toán, hệ thống sẽ query trực tiếp giá mới nhất từ CSDL.

**7. Giao dịch bảo đảm điều gì? Điều gì xảy ra nếu bỏ giao dịch và chương trình lỗi sau khi ghi `orders`?**
- Giao dịch (Transaction) đảm bảo tính **toàn vẹn dữ liệu (ACID)**, đặc biệt là tính Nguyên tử (Atomicity): "Thành công tất cả hoặc không có gì".
- Nếu bỏ giao dịch và lỗi xảy ra sau khi ghi bảng `orders` (nhưng chưa ghi `order_details` hoặc chưa trừ tồn kho), hệ thống sẽ sinh ra **dữ liệu rác/lỗi logic**: Có đơn hàng tồn tại nhưng không có sản phẩm nào bên trong, hoặc kho hàng không bị trừ đi tương ứng.

**8. `FOR UPDATE` ngăn chặn tình huống nào?**
- Ngăn chặn tình huống **Race Condition (Xung đột đồng thời)**. Ví dụ: Sản phẩm chỉ còn 1 cái, 2 người cùng bấm mua một lúc. `FOR UPDATE` sẽ khóa (lock) dòng dữ liệu đang đọc trong quá trình transaction chạy, bắt người thứ 2 phải chờ người thứ 1 hoàn tất giao dịch rồi mới được xử lý tiếp, tránh việc kho bị âm.

**9. Trang chi tiết đơn hàng cần kiểm tra những gì trước khi hiển thị?**
- Cần kiểm tra 3 thứ cốt lõi:
  1. Người dùng đã đăng nhập chưa (Authentication).
  2. ID đơn hàng truyền vào có tồn tại trong CSDL không.
  3. **Quyền sở hữu (Authorization)**: Đơn hàng đó có thuộc về `user_id` của người dùng đang đăng nhập hay không (Tránh việc user A đổi ID trên URL để xem trộm đơn hàng của user B).
