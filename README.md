# BÁO CÁO BÀI THI: TRIỂN KHAI VÀ QUẢN TRỊ HỆ THỐNG PHẦN MỀM
## ĐỀ TÀI 13: WEBSITE PORTFOLIO CÓ TRANG ADMIN QUẢN LÝ NỘI DUNG
**Kiến trúc:** Docker Compose Microservices (Nginx Reverse Proxy + PHP Apache PDO + MySQL 8.0 + phpMyAdmin + Prometheus & Grafana + Loki & Promtail + System Hardening)

---

## 1. Hướng Dẫn Khởi Chạy Toàn Bộ Hệ Thống

Chỉ cần một lệnh duy nhất từ thư mục gốc của dự án:
```bash
docker compose up -d --build
```

Kiểm tra trạng thái sẵn sàng của 12 container:
```bash
docker compose ps
```
> **Lưu ý:** Cơ sở dữ liệu MySQL được trang bị **Healthcheck**. Khi container `portfolio_db` chuyển sang trạng thái `healthy`, toàn bộ ứng dụng web, phpMyAdmin và các exporter sẽ tự động kết nối ổn định.

Để xem log hệ thống theo thời gian thực:
```bash
docker compose logs -f
```

Để dừng toàn bộ hệ thống khi kết thúc chấm thi:
```bash
docker compose down
# (Tùy chọn) Xóa volumes nếu muốn làm sạch dữ liệu:
docker compose down -v
```

---

## 2. Bảng Tài Khoản & Đường Dẫn Mặc Định (Địa Chỉ IP Thật)

> **Địa chỉ IP thật của máy chủ hiện tại:** `192.168.28.128` (Card mạng `ens33`).  
> Người dùng hoặc giáo viên chấm thi từ các máy tính khác trong cùng mạng LAN / máy tính host có thể truy cập trực tiếp bằng địa chỉ IP thật bên dưới:

| Dịch vụ | URL Truy Cập Địa Chỉ IP Thật | URL Localhost | Tài Khoản | Mật Khẩu | Ghi Chú |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Website Portfolio** | `http://192.168.28.128` | `http://localhost` | *(Công khai)* | *(Không)* | Giao diện Tailwind CSS, dữ liệu động |
| **Trang Quản Trị (Admin)** | `http://192.168.28.128/admin.php` | `http://localhost/admin.php` | `admin` | `admin123` | CRUD Dự án, sửa Profile |
| **phpMyAdmin CSDL** | `http://192.168.28.128/pma/` | `http://localhost/pma/` | `portfolio_user` | `portfolio_secure_pass_2026` | Quản trị CSDL MySQL |
| **Grafana Dashboard** | `http://192.168.28.128/grafana/` *(hoặc :3001)* | `http://localhost/grafana/` *(hoặc :3001)* | `admin` | `admin123` | Nạp sẵn Prometheus + Loki |
| **Prometheus UI** | `http://192.168.28.128:9090` | `http://localhost:9090` | *(Nội bộ)* | *(Không)* | Kiểm tra 5 scrape targets |

---

## 3. Cấu Trúc Phân Tách Mạng & Danh Sách 12 Containers

Hệ thống tuân thủ mô hình **Phân vùng Mạng Đa Tầng (Multi-tier Network Isolation)**:
- **`frontend-net`**: Vùng DMZ chỉ dành cho Nginx tiếp nhận request bên ngoài và chuyển tiếp vào Web/Grafana.
- **`backend-net`**: Vùng mạng nội bộ cô lập tuyệt đối; cơ sở dữ liệu MySQL, cAdvisor, Node Exporter, Loki, Promtail hoàn toàn không mở cổng ra Internet.

```text
                  [ Internet / Khách duyệt Web ]
                                | (Port 80)
                                v
               +----------------------------------+
               |      Nginx Reverse Proxy         |
               +----------------+-----------------+
                                |
     +--------------------------+--------------------------+
     | (frontend-net)                                      | (frontend-net)
     v                                                     v
+------------+                                      +--------------+
|  Web (PHP) |                                      |   Grafana    |
+-----+------+                                      +-------+------+
      |                                                     |
======|=====================================================|======
      | (backend-net)                                       | (backend-net)
      +-----------------+-------------------+---------------+
                        |                   |
                        v                   v
                +---------------+   +---------------+
                |  MySQL 8.0 DB |   |  Loki + Logs  |
                +---------------+   +---------------+
                        ^                   ^
                        |                   |
       [ mysql-exporter, cadvisor,   [ Promtail thu thập ]
         node-exporter, prometheus ]
```

---

## 4. Danh Sách Câu Truy Vấn LogQL Mẫu (Tiêu Chí 5: Loki + Promtail)

Trong giao diện **Grafana**, vào mục **Explore** ➔ Chọn Datasource **Loki** ➔ Dán các câu truy vấn sau vào ô LogQL:

### 1. Lọc các request gặp lỗi HTTP Client hoặc Server (Mã lỗi 4xx và 5xx của Nginx):
```logql
{job="nginx"} |~ "HTTP/[0-9.]+\" [45][0-9]{2}"
```
*Mục đích:* Phát hiện các cuộc tấn công quét đường dẫn trái phép (404, 403) hoặc lỗi sập ứng dụng (500, 502).

### 2. Giám sát các lượt gửi dữ liệu POST đăng nhập vào trang quản trị:
```logql
{job="nginx"} |= "POST /admin.php"
```
*Mục đích:* Theo dõi lịch sử đăng nhập admin và phát hiện hành vi Brute-Force mật khẩu.

### 3. Tìm kiếm toàn bộ log cảnh báo hoặc lỗi trong tất cả Docker Containers:
```logql
{job="docker"} |~ "(?i)(error|failed|fatal|exception)"
```
*Mục đích:* Bắt lỗi runtime của container PHP Apache hoặc MySQL database trong một dòng lệnh duy nhất.

### 4. Đếm số lượng request đến Nginx theo thời gian (Tần suất Request Rate):
```logql
rate({job="nginx"}[1m])
```
*Mục đích:* Chuyển đổi dữ liệu Log thành dạng biểu đồ chuỗi thời gian (Log-to-Metric).

---

## 5. Danh Sách Các Biện Pháp Hardening Đã Áp Dụng (Tiêu Chí 6)

### 1. Cô Lập Mạng Tuyệt Đối (Network Isolation)
- Chia tách thành 2 mạng bridge độc lập: `frontend-net` và `backend-net`.
- **Database Hardening:** MySQL (`portfolio_db`) **KHÔNG** hề bind cổng `3306` ra ngoài máy chủ host (`ports` bị cấm). Khách ngoài Internet không thể kết nối trực tiếp vào MySQL.
- Web container đóng vai trò cổng trung chuyển hai chiều duy nhất (`frontend-net` kết nối Nginx, `backend-net` kết nối MySQL).

### 2. Chạy Container Dưới Quyền Người Dùng Non-Root
- Áp dụng cấu hình `user` không đặc quyền:
  - `node-exporter`: `user: "65534:65534"` (user `nobody`).
  - `prometheus`: `user: "65534:65534"`.
  - `grafana`: `user: "472:472"`.
  - `nginx-exporter` & `mysql-exporter`: `user: "1000:1000"`.
- Thiết lập directive bảo mật `security_opt: [ "no-new-privileges:true" ]` trên toàn bộ các container nhằm ngăn ngừa việc nâng quyền trái phép (Privilege Escalation).

### 3. Nguyên Tắc Đặc Quyền Tối Thiểu Trên CSDL (Database Least Privilege)
- Cấu hình trong `init.sql`:
  ```sql
  GRANT SELECT, INSERT, UPDATE, DELETE ON `portfolio_db`.* TO 'portfolio_user'@'%';
  FLUSH PRIVILEGES;
  ```
- User thường `portfolio_user` tuyệt đối **không** được cấp các quyền hệ thống nguy hiểm như `SUPER`, `FILE`, `DROP DATABASE`, `GRANT OPTION`.

### 4. Hardening Toàn Diện Trên Nginx Reverse Proxy
- **Ẩn thông tin phiên bản:** Khai báo `server_tokens off;` để che giấu phiên bản Nginx trong headers và trang lỗi, ngăn chặn kẻ tấn công dò quét lỗ hổng theo CVE.
- **Giới hạn kích thước Buffer & Timeout:** Cấu hình `client_body_buffer_size 128k`, `client_header_buffer_size 1k`, `large_client_header_buffers 4 8k`, `client_body_timeout 15s`, `client_header_timeout 15s` nhằm phòng vệ trước các cuộc tấn công DoS, tràn bộ đệm (Buffer Overflow) và Slowloris.
- **Chặn truy cập file nhạy cảm:**
  - Chặn toàn bộ file/thư mục ẩn bắt đầu bằng dấu chấm (`.git`, `.env`, `.htaccess`).
  - Chặn trực tiếp các phần mở rộng nguy hiểm: `.sql`, `.env`, `.bak`, `.config`, `.yaml`, `.yml`, `.md`, `.log`, `.ini`.
- **Hệ thống Security Headers đạt chuẩn A+:**
  - `X-Frame-Options: SAMEORIGIN` (Chống Clickjacking).
  - `X-Content-Type-Options: nosniff` (Chống MIME Confusion).
  - `X-XSS-Protection: 1; mode=block` (Chống Cross-Site Scripting).
  - `Referrer-Policy: strict-origin-when-cross-origin`.
  - `Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()`.
  - `Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: blob: ws: wss:;`.

### 5. Bảo Mật Tầng Ứng Dụng (Web Application Security)
- **Chống SQL Injection:** 100% truy vấn dữ liệu trong `db.php` và `admin.php` dùng **PDO Prepared Statements** kết hợp tham số ẩn danh `?`.
- **Chống XSS:** Mọi dữ liệu xuất ra màn hình đều qua hàm lọc `e()` tương đương `htmlspecialchars($data, ENT_QUOTES, 'UTF-8')`.
- **Bảo mật Quản trị:** Tự động tạo lại Session ID (`session_regenerate_id(true)`) ngay khi đăng nhập thành công chống tấn công Session Fixation; hỗ trợ băm mật khẩu chuẩn Bcrypt.
