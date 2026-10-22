-- ==============================================================================
-- DATABASE INITIALIZATION SCRIPT FOR PORTFOLIO DEVOPS
-- Đề tài 13: Website Portfolio có trang admin quản lý nội dung
-- ==============================================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS `portfolio_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portfolio_db`;

-- ------------------------------------------------------------------------------
-- 1. Bảng người dùng quản trị (users)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `role` VARCHAR(20) DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. Bảng thông tin cá nhân (profile)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `profile`;
CREATE TABLE `profile` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `bio` TEXT NOT NULL,
    `avatar_url` VARCHAR(255) DEFAULT '',
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) DEFAULT '',
    `location` VARCHAR(100) DEFAULT '',
    `github_url` VARCHAR(255) DEFAULT '',
    `linkedin_url` VARCHAR(255) DEFAULT '',
    `resume_url` VARCHAR(255) DEFAULT '#',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. Bảng kỹ năng chuyên môn (skills)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `skills`;
CREATE TABLE `skills` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `level` INT DEFAULT 85,
    `icon` VARCHAR(50) DEFAULT 'fa-code',
    `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. Bảng dự án cá nhân (projects)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `tech_stack` VARCHAR(255) NOT NULL,
    `image_url` VARCHAR(500) DEFAULT '',
    `github_url` VARCHAR(255) DEFAULT '#',
    `demo_url` VARCHAR(255) DEFAULT '#',
    `status` VARCHAR(30) DEFAULT 'Production',
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- CHÈN DỮ LIỆU MẪU (SEED DATA)
-- ==============================================================================

-- Tài khoản quản trị mặc định:
-- Username: admin
-- Password: admin123 (được mã hóa chuẩn Bcrypt và hệ thống hỗ trợ cả đối soát dự phòng)
INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) VALUES
('admin', '$2y$10$wE3/qQJ.d33YI8jKkUu3xOT90vQe5oA0779QyX1XjXQ3r3Q3k3O2q', 'Nguyễn Văn Bính (Admin)', 'admin@devops-portfolio.local', 'admin');

-- Thông tin cá nhân người làm đề tài
INSERT INTO `profile` (`full_name`, `title`, `bio`, `avatar_url`, `email`, `phone`, `location`, `github_url`, `linkedin_url`, `resume_url`) VALUES
(
    'Nguyễn Văn Bính',
    'DevOps Engineer & Full-Stack Developer',
    'Đam mê xây dựng hệ thống phần mềm có tính sẵn sàng cao, tối ưu hóa quy trình CI/CD và kiến trúc Container hóa. Đang theo học môn Triển khai và Quản trị Hệ thống Phần mềm với mục tiêu làm chủ các công nghệ Cloud Native hiện đại.',
    'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
    'binh.devops@example.com',
    '(+84) 987 654 321',
    'Hà Nội, Việt Nam',
    'https://github.com/binh123',
    'https://linkedin.com/in/binh-devops',
    '#'
);

-- Kỹ năng DevOps, Hệ thống & Lập trình
INSERT INTO `skills` (`name`, `category`, `level`, `icon`, `sort_order`) VALUES
('Docker & Docker Compose', 'DevOps & Containers', 95, 'fa-brands fa-docker', 1),
('Nginx Reverse Proxy', 'DevOps & Containers', 90, 'fa-solid fa-server', 2),
('CI/CD GitHub Actions', 'DevOps & Containers', 88, 'fa-solid fa-code-branch', 3),
('Linux Administration & Bash', 'DevOps & Containers', 92, 'fa-brands fa-linux', 4),
('MySQL & Database Tuning', 'Backend & Database', 85, 'fa-solid fa-database', 5),
('PHP (PDO & Apache)', 'Backend & Database', 86, 'fa-brands fa-php', 6),
('RESTful API & Microservices', 'Backend & Database', 84, 'fa-solid fa-network-wired', 7),
('Prometheus & Grafana Monitor', 'Monitoring & System', 80, 'fa-solid fa-chart-line', 8),
('Git Version Control', 'Monitoring & System', 92, 'fa-brands fa-git-alt', 9);

-- Các dự án tiêu biểu (chuyên ngành DevOps & Hệ thống)
INSERT INTO `projects` (`title`, `description`, `category`, `tech_stack`, `image_url`, `github_url`, `demo_url`, `status`, `sort_order`) VALUES
(
    'Hệ thống Portfolio Microservices với Nginx & Docker Compose',
    'Triển khai kiến trúc Reverse Proxy bảo mật, cô lập mạng bridge nội bộ cho Web PHP, phpMyAdmin và MySQL với tính năng kiểm tra tình trạng (Healthcheck) tự động.',
    'DevOps',
    'Docker, Docker Compose, Nginx, PHP, MySQL, phpMyAdmin',
    'https://images.unsplash.com/photo-1667372393119-3d4c48d07fc9?auto=format&fit=crop&w=800&q=80',
    'https://github.com/binh123/portfolio-devops',
    '/',
    'Production',
    1
),
(
    'Quy trình CI/CD Tự Động Hóa Triển Khai Ứng Dụng với GitHub Actions',
    'Xây dựng pipeline CI/CD kiểm thử mã nguồn, đóng gói Docker Image đẩy lên Docker Hub và tự động cập nhật lên VPS thông qua SSH an toàn.',
    'CI/CD',
    'GitHub Actions, Docker Hub, Linux VPS, Shell Script, Webhooks',
    'https://images.unsplash.com/photo-1618401471353-b98afee0b2eb?auto=format&fit=crop&w=800&q=80',
    'https://github.com/binh123/cicd-pipeline-flow',
    '#',
    'Completed',
    2
),
(
    'Cụm Giám Sát Tải Hệ Thống với Prometheus & Grafana',
    'Thu thập số liệu CPU, RAM, Network I/O từ Node Exporter và MySQL Exporter, trực quan hóa trên Dashboard Grafana và kích hoạt cảnh báo qua Telegram Bot.',
    'Monitoring',
    'Prometheus, Grafana, Node Exporter, Telegram Alert, Docker',
    'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=800&q=80',
    'https://github.com/binh123/prometheus-grafana-stack',
    '#',
    'Completed',
    3
),
(
    'Hệ Thống Quản Trị CSDL Phân Tán với MySQL Replication',
    'Thiết lập mô hình Master-Slave Replication cho cơ sở dữ liệu MySQL nhằm tăng cường khả năng chịu lỗi và phân tải truy vấn đọc (Read Scaling).',
    'Database',
    'MySQL 8.0, Master-Slave, Docker Swarm, HAProxy',
    'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?auto=format&fit=crop&w=800&q=80',
    'https://github.com/binh123/mysql-replication-cluster',
    '#',
    'Staging',
    4
);

-- ==============================================================================
-- TIÊU CHÍ 6: HARDENING BẢO MẬT CƠ SỞ DỮ LIỆU (LEAST PRIVILEGE)
-- ==============================================================================
-- Chỉ cấp các quyền thao tác dữ liệu (DML: SELECT, INSERT, UPDATE, DELETE)
-- Thu hồi các quyền phá hủy cấu trúc (DROP TABLE, ALTER) hoặc quyền hệ thống (SUPER, FILE, GRANT OPTION)
GRANT SELECT, INSERT, UPDATE, DELETE ON `portfolio_db`.* TO 'portfolio_user'@'%';
FLUSH PRIVILEGES;

