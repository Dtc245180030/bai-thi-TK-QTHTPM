<?php
/**
 * index.php - Trang chủ Portfolio Cá nhân
 * Môn học: Triển khai và Quản trị Hệ thống Phần mềm - Đề tài 13
 */

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/db.php';

// Khởi tạo các biến dữ liệu mặc định
$profile = [
    'full_name' => 'Nguyễn Văn Bính',
    'title' => 'DevOps Engineer & System Administrator',
    'bio' => 'Chuyên gia triển khai hệ thống phần mềm, tự động hóa hạ tầng (IaC), CI/CD và kiến trúc container hóa với Docker & Nginx.',
    'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
    'email' => 'binh.devops@example.com',
    'phone' => '(+84) 987 654 321',
    'location' => 'Hà Nội, Việt Nam',
    'github_url' => 'https://github.com/binh123',
    'linkedin_url' => 'https://linkedin.com',
    'resume_url' => '#'
];

$skills = [];
$projects = [];
$categories = [];
$dbConnected = ($pdo !== null);

if ($dbConnected) {
    try {
        // 1. Lấy thông tin cá nhân
        $stmtProf = $pdo->query("SELECT * FROM profile LIMIT 1");
        if ($p = $stmtProf->fetch()) {
            $profile = array_merge($profile, $p);
        }

        // 2. Lấy danh sách kỹ năng
        $stmtSkills = $pdo->query("SELECT * FROM skills ORDER BY category, sort_order ASC");
        $rawSkills = $stmtSkills->fetchAll();
        foreach ($rawSkills as $s) {
            $skills[$s['category']][] = $s;
        }

        // 3. Lấy danh sách dự án
        $stmtProj = $pdo->query("SELECT * FROM projects ORDER BY sort_order ASC, created_at DESC");
        $projects = $stmtProj->fetchAll();

        // 4. Lấy danh sách thể loại phục vụ lọc dự án
        $categories = array_unique(array_column($projects, 'category'));
    } catch (PDOException $e) {
        $dbConnected = false;
        $dbError = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($profile['full_name']) ?> | DevOps Portfolio & System Management</title>
    <meta name="description" content="Portfolio chuyên ngành Triển khai và Quản trị Hệ thống Phần mềm - Docker, Nginx, PHP, MySQL">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        },
                        dark: {
                            900: '#0f172a',
                            800: '#1e293b',
                            700: '#334155',
                            600: '#475569'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-nav {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .gradient-text {
            background: linear-gradient(135deg, #38bdf8 0%, #818cf8 50%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gradient-border {
            position: relative;
            background: linear-gradient(to right, #0284c7, #6366f1);
            padding: 1px;
            border-radius: 1rem;
        }
        .code-glow {
            box-shadow: 0 0 50px -10px rgba(56, 189, 248, 0.15);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 font-sans selection:bg-indigo-500 selection:text-white min-h-screen">

    <!-- =======================================================================
         NAVBAR / HEADER
         ======================================================================= -->
    <header class="fixed top-0 left-0 right-0 z-50 glass-nav border-b border-slate-800/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center space-x-3 group">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-cyan-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-server text-lg"></i>
                </div>
                <div>
                    <span class="font-bold text-lg text-white group-hover:text-cyan-400 transition-colors block leading-tight">
                        <?= e($profile['full_name']) ?>
                    </span>
                    <span class="text-xs font-mono text-cyan-400 uppercase tracking-widest block">
                        Đề 13 • DevOps Portfolio
                    </span>
                </div>
            </a>

            <!-- Desktop Nav Menu -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-medium text-slate-300">
                <a href="#about" class="hover:text-cyan-400 transition-colors">Giới thiệu</a>
                <a href="#architecture" class="hover:text-cyan-400 transition-colors">Kiến trúc Hệ thống</a>
                <a href="#skills" class="hover:text-cyan-400 transition-colors">Kỹ năng</a>
                <a href="#projects" class="hover:text-cyan-400 transition-colors">Dự án</a>
                <a href="#contact" class="hover:text-cyan-400 transition-colors">Liên hệ</a>
            </nav>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                <a href="/grafana/" target="_blank" title="Mở trang giám sát Grafana Dashboard"
                   class="inline-flex items-center space-x-1.5 text-xs font-medium px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-orange-400 border border-slate-700 hover:border-orange-500/40 transition-all shadow-sm">
                    <i class="fa-solid fa-chart-line text-orange-400"></i>
                    <span class="hidden md:inline">Grafana</span>
                </a>
                <a href="/pma/" target="_blank" title="Mở trang phpMyAdmin quản trị CSDL"
                   class="hidden sm:inline-flex items-center space-x-1.5 text-xs font-medium px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-300 border border-slate-700 hover:border-amber-500/40 transition-all shadow-sm">
                    <i class="fa-solid fa-database text-amber-400"></i>
                    <span>phpMyAdmin</span>
                </a>
                <a href="admin.php" 
                   class="inline-flex items-center space-x-2 text-xs font-semibold px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white shadow-md shadow-indigo-500/20 hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-lock text-xs"></i>
                    <span>Admin</span>
                </a>
            </div>
        </div>
    </header>

    <main class="pt-24">
        <!-- Thông báo trạng thái cơ sở dữ liệu nếu có sự cố -->
        <?php if (!$dbConnected): ?>
            <div class="max-w-7xl mx-auto px-4 mt-6">
                <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 flex items-start space-x-3">
                    <i class="fa-solid fa-triangle-exclamation text-xl mt-0.5"></i>
                    <div>
                        <h4 class="font-bold text-sm">Cơ sở dữ liệu MySQL đang khởi động hoặc chưa sẵn sàng!</h4>
                        <p class="text-xs text-amber-200/80 mt-1">
                            Hệ thống Docker Compose đang khởi tạo container MySQL (`portfolio_db`). Vui lòng đợi vài giây và tải lại trang.
                            Chi tiết lỗi kết nối: <code class="font-mono text-xs"><?= e($dbError ?? 'Không thể kết nối DSN') ?></code>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- ===================================================================
             HERO SECTION
             =================================================================== -->
        <section id="about" class="relative overflow-hidden py-20 lg:py-28">
            <!-- Background Glow Elements -->
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[700px] h-[450px] bg-gradient-to-tr from-cyan-500/15 to-indigo-500/15 blur-3xl -z-10 rounded-full"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    <!-- Left: Profile Info -->
                    <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                        <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 text-xs font-semibold tracking-wide uppercase">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>Hệ thống Hoạt động 100% qua Docker Compose</span>
                        </div>

                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                            Xin chào, tôi là <br>
                            <span class="gradient-text"><?= e($profile['full_name']) ?></span>
                        </h1>

                        <p class="text-xl font-medium text-slate-300">
                            <?= e($profile['title']) ?>
                        </p>

                        <p class="text-slate-400 text-base leading-relaxed max-w-2xl mx-auto lg:mx-0">
                            <?= nl2br(e($profile['bio'])) ?>
                        </p>

                        <!-- CTA Buttons -->
                        <div class="pt-4 flex flex-wrap gap-4 justify-center lg:justify-start">
                            <a href="#projects" class="px-6 py-3.5 rounded-xl bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-semibold text-sm shadow-lg shadow-indigo-500/25 transition-all transform hover:-translate-y-0.5 flex items-center space-x-2">
                                <i class="fa-solid fa-rocket"></i>
                                <span>Khám phá Dự án</span>
                            </a>
                            <a href="#architecture" class="px-6 py-3.5 rounded-xl bg-slate-800/90 hover:bg-slate-700/90 border border-slate-700 text-slate-200 font-semibold text-sm transition-all transform hover:-translate-y-0.5 flex items-center space-x-2">
                                <i class="fa-solid fa-diagram-project text-cyan-400"></i>
                                <span>Kiến trúc Triển khai</span>
                            </a>
                            <a href="<?= e($profile['github_url']) ?>" target="_blank" class="p-3.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-all">
                                <i class="fa-brands fa-github text-lg"></i>
                            </a>
                            <a href="<?= e($profile['linkedin_url']) ?>" target="_blank" class="p-3.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-all">
                                <i class="fa-brands fa-linkedin text-lg"></i>
                            </a>
                        </div>

                        <!-- System Highlights Stats -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 border-t border-slate-800/80">
                            <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                <span class="text-2xl font-extrabold text-white block">4</span>
                                <span class="text-xs text-slate-400">Docker Services</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                <span class="text-2xl font-extrabold text-emerald-400 block">99.9%</span>
                                <span class="text-xs text-slate-400">Uptime SLA</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                <span class="text-2xl font-extrabold text-cyan-400 block"><?= count($projects) ?></span>
                                <span class="text-xs text-slate-400">Dự án Đã nạp</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                <span class="text-2xl font-extrabold text-indigo-400 block">Nginx</span>
                                <span class="text-xs text-slate-400">Reverse Proxy</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Avatar Card with Glass Effect -->
                    <div class="lg:col-span-5 flex justify-center">
                        <div class="relative group">
                            <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500 to-indigo-600 rounded-3xl blur-xl opacity-30 group-hover:opacity-60 transition duration-700"></div>
                            <div class="relative bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl max-w-sm w-full space-y-5 code-glow">
                                <div class="relative rounded-2xl overflow-hidden aspect-square bg-slate-800">
                                    <img src="<?= e($profile['avatar_url']) ?>" alt="<?= e($profile['full_name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    <div class="absolute bottom-3 left-3 right-3 bg-slate-950/80 backdrop-blur-md px-3 py-2 rounded-xl border border-slate-700/60 flex items-center justify-between text-xs">
                                        <span class="text-slate-300 font-mono"><i class="fa-solid fa-location-dot text-rose-400 mr-1.5"></i><?= e($profile['location']) ?></span>
                                        <span class="text-emerald-400 font-semibold"><i class="fa-solid fa-circle text-[8px] mr-1"></i>Sẵn sàng</span>
                                    </div>
                                </div>
                                <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800/80 font-mono text-xs text-slate-400 space-y-1">
                                    <div class="text-indigo-400 font-semibold">$ docker compose ps</div>
                                    <div class="text-emerald-400">✔ portfolio_nginx: Up (80:80)</div>
                                    <div class="text-emerald-400">✔ portfolio_web: Up (80)</div>
                                    <div class="text-emerald-400">✔ portfolio_phpmyadmin: Up (80)</div>
                                    <div class="text-emerald-400">✔ portfolio_db: Up (healthy)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ===================================================================
             SYSTEM ARCHITECTURE & DEVOPS SHOWCASE (ĐẶC TRƯNG MÔN HỌC)
             =================================================================== -->
        <section id="architecture" class="py-20 bg-slate-900/50 border-y border-slate-800/60 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-xs font-mono font-bold text-cyan-400 uppercase tracking-widest bg-cyan-950/60 border border-cyan-800/60 px-3 py-1 rounded-full">
                        Môn Triển khai & Quản trị Hệ thống
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-4">
                        Kiến Trúc Đề Tài 13: Docker Stack
                    </h2>
                    <p class="text-slate-400 text-sm mt-3">
                        Mô hình triển khai 4 dịch vụ cô lập trong mạng ảo bridge, Nginx đóng vai trò Cổng phân phối tải và bảo mật Header.
                    </p>
                </div>

                <!-- Architecture Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <!-- Node 1: Nginx -->
                    <div class="bg-slate-900 rounded-2xl p-6 border border-slate-800 hover:border-cyan-500/50 transition-all relative group">
                        <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">1. Nginx Reverse Proxy</h3>
                        <p class="text-xs text-slate-400 mb-4">
                            Lắng nghe cổng công khai <code class="text-cyan-400 font-mono">80</code>. Điều hướng request vào Web PHP hoặc phpMyAdmin. Tích hợp Security Headers.
                        </p>
                        <div class="font-mono text-[11px] bg-slate-950 p-2.5 rounded-lg border border-slate-800 text-slate-300 space-y-1">
                            <div>Port: <span class="text-cyan-400">0.0.0.0:80</span></div>
                            <div>Proxy: <span class="text-indigo-400">web:80</span> &amp; <span class="text-amber-400">/pma/</span></div>
                            <div>Headers: <span class="text-emerald-400">X-Frame, CSP</span></div>
                        </div>
                    </div>

                    <!-- Node 2: Web App PHP -->
                    <div class="bg-slate-900 rounded-2xl p-6 border border-slate-800 hover:border-indigo-500/50 transition-all relative group">
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-brands fa-php"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">2. Web App (PHP + Apache)</h3>
                        <p class="text-xs text-slate-400 mb-4">
                            Mã nguồn PHP thuần nhẹ, kết nối CSDL bằng PDO chuẩn hướng đối tượng. Hỗ trợ đầy đủ chức năng CRUD trang Admin.
                        </p>
                        <div class="font-mono text-[11px] bg-slate-950 p-2.5 rounded-lg border border-slate-800 text-slate-300 space-y-1">
                            <div>Image: <span class="text-indigo-400">php:8.2-apache</span></div>
                            <div>Driver: <span class="text-emerald-400">pdo_mysql</span></div>
                            <div>Network: <span class="text-slate-400">portfolio_net</span></div>
                        </div>
                    </div>

                    <!-- Node 3: phpMyAdmin -->
                    <div class="bg-slate-900 rounded-2xl p-6 border border-slate-800 hover:border-amber-500/50 transition-all relative group">
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-table"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">3. phpMyAdmin Tool</h3>
                        <p class="text-xs text-slate-400 mb-4">
                            Truy cập trực tiếp qua đường dẫn <code class="text-amber-400 font-mono">/pma/</code> mà không cần mở thêm cổng port mới trên máy chủ.
                        </p>
                        <div class="font-mono text-[11px] bg-slate-950 p-2.5 rounded-lg border border-slate-800 text-slate-300 space-y-1">
                            <div>URI: <span class="text-amber-400">/pma/</span></div>
                            <div>Target: <span class="text-indigo-400">portfolio_db</span></div>
                            <div>Auth: <span class="text-emerald-400">Cookie / DB User</span></div>
                        </div>
                    </div>

                    <!-- Node 4: MySQL 8.0 -->
                    <div class="bg-slate-900 rounded-2xl p-6 border border-slate-800 hover:border-emerald-500/50 transition-all relative group">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-database"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">4. MySQL Database</h3>
                        <p class="text-xs text-slate-400 mb-4">
                            Lưu trữ cơ sở dữ liệu `portfolio_db`, tự động nạp dữ liệu mẫu từ `init.sql`. Kèm kiểm tra sức khỏe Healthcheck.
                        </p>
                        <div class="font-mono text-[11px] bg-slate-950 p-2.5 rounded-lg border border-slate-800 text-slate-300 space-y-1">
                            <div>Engine: <span class="text-emerald-400">MySQL 8.0</span></div>
                            <div>Health: <span class="text-emerald-400">mysqladmin ping</span></div>
                            <div>Volume: <span class="text-slate-400">db_data (Persistent)</span></div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ===================================================================
             SKILLS SECTION
             =================================================================== -->
        <section id="skills" class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-xs font-mono font-bold text-indigo-400 uppercase tracking-widest bg-indigo-950/60 border border-indigo-800/60 px-3 py-1 rounded-full">
                        Năng lực Chuyên môn
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-4">
                        Kỹ Năng &amp; Công Nghệ Quản Trị
                    </h2>
                    <p class="text-slate-400 text-sm mt-3">
                        Dữ liệu được nạp động từ bảng <code class="font-mono text-cyan-400">skills</code> trong MySQL Database.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php if (empty($skills)): ?>
                        <div class="col-span-3 text-center py-12 text-slate-500">
                            <i class="fa-solid fa-spinner fa-spin text-3xl mb-3"></i>
                            <p>Đang chờ nạp danh sách kỹ năng từ cơ sở dữ liệu...</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($skills as $catName => $groupSkills): ?>
                            <div class="bg-slate-900/80 rounded-2xl p-6 border border-slate-800 shadow-xl space-y-5">
                                <div class="flex items-center space-x-3 pb-3 border-b border-slate-800">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-cyan-500 to-indigo-600 flex items-center justify-center text-white text-xs">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </div>
                                    <h3 class="font-bold text-white text-base"><?= e($catName) ?></h3>
                                </div>
                                <div class="space-y-4">
                                    <?php foreach ($groupSkills as $skill): ?>
                                        <div>
                                            <div class="flex justify-between items-center text-xs mb-1.5">
                                                <span class="font-medium text-slate-300 flex items-center space-x-2">
                                                    <i class="<?= e($skill['icon']) ?> text-cyan-400 w-4 text-center"></i>
                                                    <span><?= e($skill['name']) ?></span>
                                                </span>
                                                <span class="font-mono text-slate-400 font-semibold"><?= (int)$skill['level'] ?>%</span>
                                            </div>
                                            <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-cyan-500 to-indigo-500 rounded-full transition-all duration-1000" style="width: <?= (int)$skill['level'] ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- ===================================================================
             PROJECTS SECTION (DYNAMIC CRUD OUTPUT)
             =================================================================== -->
        <section id="projects" class="py-20 bg-slate-900/30 border-t border-slate-800/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                    <div>
                        <span class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-widest bg-emerald-950/60 border border-emerald-800/60 px-3 py-1 rounded-full">
                            Sản Phẩm &amp; Đề Tài
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-4">
                            Dự Án Tiêu Biểu
                        </h2>
                        <p class="text-slate-400 text-sm mt-2">
                            Quản lý thêm, sửa, xóa trực tiếp tại <a href="admin.php" class="text-cyan-400 hover:underline">Trang Admin</a>
                        </p>
                    </div>

                    <!-- Filter Buttons (Javascript Interactive) -->
                    <div class="mt-6 md:mt-0 flex flex-wrap gap-2" id="filter-container">
                        <button onclick="filterProjects('all')" class="filter-btn active px-4 py-2 rounded-xl text-xs font-semibold bg-cyan-500 text-white transition-all shadow-md">
                            Tất cả (<?= count($projects) ?>)
                        </button>
                        <?php foreach ($categories as $cat): ?>
                            <button onclick="filterProjects('<?= e(strtolower($cat)) ?>')" class="filter-btn px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">
                                <?= e($cat) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Project Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="projects-grid">
                    <?php if (empty($projects)): ?>
                        <div class="col-span-3 text-center py-16 bg-slate-900/60 rounded-2xl border border-slate-800">
                            <i class="fa-solid fa-folder-open text-4xl text-slate-600 mb-3"></i>
                            <h4 class="text-base font-bold text-slate-300">Chưa có dự án nào được tạo!</h4>
                            <p class="text-xs text-slate-500 mt-1 mb-4">Hãy đăng nhập vào trang quản trị để thêm dự án đầu tiên của bạn.</p>
                            <a href="admin.php" class="inline-flex items-center space-x-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium">
                                <i class="fa-solid fa-plus"></i>
                                <span>Thêm Dự Án Mới</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($projects as $proj): ?>
                            <div class="project-card bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 hover:border-slate-700 transition-all duration-300 flex flex-col group shadow-lg"
                                 data-category="<?= e(strtolower($proj['category'])) ?>">
                                
                                <!-- Project Image Preview -->
                                <div class="relative h-48 w-full overflow-hidden bg-slate-800">
                                    <img src="<?= !empty($proj['image_url']) ? e($proj['image_url']) : 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=800&q=80' ?>" 
                                         alt="<?= e($proj['title']) ?>" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
                                    
                                    <!-- Badges -->
                                    <div class="absolute top-3 left-3 flex space-x-2">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold font-mono tracking-wider uppercase bg-cyan-950/80 border border-cyan-800/80 text-cyan-300 backdrop-blur-md">
                                            <?= e($proj['category']) ?>
                                        </span>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold font-mono uppercase bg-slate-950/80 border border-emerald-500/40 text-emerald-400 backdrop-blur-md flex items-center space-x-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span><?= e($proj['status'] ?? 'Active') ?></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Project Body -->
                                <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                                    <div class="space-y-2">
                                        <h3 class="text-lg font-bold text-white group-hover:text-cyan-400 transition-colors line-clamp-1">
                                            <?= e($proj['title']) ?>
                                        </h3>
                                        <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed">
                                            <?= e($proj['description']) ?>
                                        </p>
                                    </div>

                                    <!-- Tech Stack Badges -->
                                    <div class="space-y-3">
                                        <div class="flex flex-wrap gap-1.5">
                                            <?php 
                                                $techList = array_map('trim', explode(',', $proj['tech_stack']));
                                                foreach ($techList as $tech): 
                                            ?>
                                                <span class="px-2 py-0.5 rounded bg-slate-800/90 text-slate-300 text-[11px] font-mono border border-slate-700/60">
                                                    <?= e($tech) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>

                                        <!-- Card Footer Links -->
                                        <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs font-semibold">
                                            <?php if (!empty($proj['github_url']) && $proj['github_url'] !== '#'): ?>
                                                <a href="<?= e($proj['github_url']) ?>" target="_blank" class="text-slate-300 hover:text-white flex items-center space-x-1.5 transition-colors">
                                                    <i class="fa-brands fa-github text-sm"></i>
                                                    <span>Mã nguồn</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-slate-500 text-[11px]"><i class="fa-solid fa-lock mr-1"></i>Repo nội bộ</span>
                                            <?php endif; ?>

                                            <?php if (!empty($proj['demo_url']) && $proj['demo_url'] !== '#'): ?>
                                                <a href="<?= e($proj['demo_url']) ?>" target="_blank" class="text-cyan-400 hover:text-cyan-300 flex items-center space-x-1.5 transition-colors">
                                                    <span>Xem Trực Tiếp</span>
                                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="admin.php?edit=<?= (int)$proj['id'] ?>" class="text-indigo-400 hover:text-indigo-300 text-[11px]">
                                                    <i class="fa-solid fa-pen-to-square mr-1"></i>Sửa trong Admin
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </section>

        <!-- ===================================================================
             CONTACT & FOOTER
             =================================================================== -->
        <section id="contact" class="py-20 border-t border-slate-800/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-r from-slate-900 via-indigo-950/40 to-slate-900 rounded-3xl p-8 sm:p-12 border border-slate-800 shadow-2xl">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                        <div class="space-y-4">
                            <span class="text-xs font-mono font-bold text-cyan-400 uppercase tracking-widest">
                                Kết Nối Trực Tiếp
                            </span>
                            <h2 class="text-3xl font-extrabold text-white">
                                Sẵn Sàng Cho Các Thử Thách Vận Hành Hệ Thống Mới
                            </h2>
                            <p class="text-slate-400 text-sm">
                                Liên hệ để trao đổi thêm về các phương án tối ưu hóa hạ tầng Cloud Native, thiết lập đường ống CI/CD hoặc phát triển ứng dụng Full-Stack.
                            </p>
                            <div class="pt-2 space-y-2 text-sm text-slate-300">
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-envelope text-cyan-400 w-5"></i>
                                    <span><?= e($profile['email']) ?></span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-phone text-emerald-400 w-5"></i>
                                    <span><?= e($profile['phone']) ?></span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-location-dot text-rose-400 w-5"></i>
                                    <span><?= e($profile['location']) ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-950/80 p-6 rounded-2xl border border-slate-800 space-y-4">
                            <h3 class="text-base font-bold text-white flex items-center space-x-2">
                                <i class="fa-solid fa-terminal text-cyan-400"></i>
                                <span>Cổng Truy Cập Hệ Thống Quản Trị</span>
                            </h3>
                            <p class="text-xs text-slate-400">
                                Giáo viên và Người chấm thi có thể truy cập các đường dẫn bên dưới để kiểm tra toàn diện đề tài:
                            </p>
                            <div class="space-y-2 pt-1">
                                <a href="/grafana/" target="_blank" class="flex items-center justify-between p-3 rounded-xl bg-slate-900 hover:bg-slate-850 border border-slate-800 hover:border-orange-500/50 transition-all group">
                                    <div class="flex items-center space-x-3 text-xs">
                                        <div class="w-8 h-8 rounded-lg bg-orange-500/20 text-orange-400 flex items-center justify-center font-bold">
                                            <i class="fa-solid fa-chart-line"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white group-hover:text-orange-400">Hệ Thống Giám Sát Grafana (/grafana/)</div>
                                            <div class="text-[11px] text-slate-400">Prometheus + cAdvisor + Node Exporter (Tài khoản: admin / admin123)</div>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-orange-400 group-hover:translate-x-1 transition-all"></i>
                                </a>

                                <a href="admin.php" class="flex items-center justify-between p-3 rounded-xl bg-slate-900 hover:bg-slate-850 border border-slate-800 hover:border-indigo-500/50 transition-all group">
                                    <div class="flex items-center space-x-3 text-xs">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold">
                                            <i class="fa-solid fa-gauge-high"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white group-hover:text-cyan-400">Trang Admin Quản Trị (/admin.php)</div>
                                            <div class="text-[11px] text-slate-400">CRUD thêm, sửa, xóa dự án (Tài khoản: admin / admin123)</div>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-cyan-400 group-hover:translate-x-1 transition-all"></i>
                                </a>

                                <a href="/pma/" target="_blank" class="flex items-center justify-between p-3 rounded-xl bg-slate-900 hover:bg-slate-850 border border-slate-800 hover:border-amber-500/50 transition-all group">
                                    <div class="flex items-center space-x-3 text-xs">
                                        <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold">
                                            <i class="fa-solid fa-database"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white group-hover:text-amber-400">Giao diện phpMyAdmin (/pma/)</div>
                                            <div class="text-[11px] text-slate-400">Xem bảng dữ liệu MySQL (User: portfolio_user / Pass: portfolio_secure_pass_2026)</div>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-amber-400 group-hover:translate-x-1 transition-all"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="mt-12 text-center text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-800/60 pt-8">
                    <div>
                        &copy; <?= date('Y') ?> <?= e($profile['full_name']) ?>. Đề 13 - Triển khai và Quản trị Hệ thống Phần mềm.
                    </div>
                    <div class="font-mono text-[11px] text-slate-400 flex items-center space-x-3">
                        <span>PHP <?= PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION ?> Apache</span>
                        <span>•</span>
                        <span>MySQL 8.0</span>
                        <span>•</span>
                        <span>Nginx Reverse Proxy</span>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- JavaScript Interactive Filter -->
    <script>
        function filterProjects(category) {
            const cards = document.querySelectorAll('.project-card');
            const buttons = document.querySelectorAll('.filter-btn');

            buttons.forEach(btn => {
                btn.classList.remove('bg-cyan-500', 'text-white', 'shadow-md');
                btn.classList.add('bg-slate-800', 'text-slate-300');
            });

            event.target.classList.remove('bg-slate-800', 'text-slate-300');
            event.target.classList.add('bg-cyan-500', 'text-white', 'shadow-md');

            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                if (category === 'all' || cardCat === category) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
