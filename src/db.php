<?php
/**
 * db.php - Quản lý kết nối Cơ sở dữ liệu MySQL qua PDO và các tiện ích dùng chung
 * Môn học: Triển khai và Quản trị Hệ thống Phần mềm
 */

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Đọc thông số kết nối từ biến môi trường (hoặc giá trị mặc định)
$dbHost = getenv('DB_HOST') ?: 'db';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'portfolio_db';
$dbUser = getenv('DB_USER') ?: 'portfolio_user';
$dbPass = getenv('DB_PASSWORD') ?: 'portfolio_secure_pass_2026';

$dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

$pdo = null;
$dbError = null;

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    $dbError = $e->getMessage();
}

/**
 * Hàm lấy đối tượng kết nối PDO
 * @return PDO|null
 */
function getDB() {
    global $pdo;
    return $pdo;
}

/**
 * Hàm lọc dữ liệu an toàn tránh tấn công XSS (Cross-Site Scripting)
 * @param string|null $data
 * @return string
 */
function e($data) {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Thiết lập thông báo tạm thời (Flash Message)
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Lấy thông báo tạm thời và xóa khỏi session
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Kiểm tra người dùng đã đăng nhập trang quản trị chưa
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Bảo vệ route admin yêu cầu đăng nhập
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: admin.php');
        exit;
    }
}
