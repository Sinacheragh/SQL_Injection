<?php
// فایل پیکربندی اتصال به دیتابیس
// این فایل برای مدیریت اتصال به دیتابیس PostgreSQL استفاده می‌شود

function get_db_connection() {
    $dsn_env = getenv('DATABASE_URL') ?: 'postgresql://sql_injection_xafp_user:OUjsmNfESr13fgzTtceZ0OVd0RYP5OHn@dpg-d3m12fc9c44c73eqerb0-a/sql_injection_xafp';
    $parts = parse_url($dsn_env);

    if (!$parts || !isset($parts['host'])) {
        http_response_code(500);
        echo "<h2>خطا: مقدار DATABASE_URL نامعتبر است.</h2>";
        exit;
    }

    $host = $parts['host'];
    $port = isset($parts['port']) ? $parts['port'] : 5432;
    $dbname = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
    $user = isset($parts['user']) ? $parts['user'] : '';
    $pass = isset($parts['pass']) ? $parts['pass'] : '';

    $sslmode = (strpos($host, '.oregon-postgres.render.com') !== false) ? 'require' : 'disable';
    $pdo_dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
    if ($sslmode === 'require') $pdo_dsn .= ";sslmode=require";

    try {
        $pdo = new PDO($pdo_dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo "<h2>خطا در اتصال به دیتابیس</h2>";
        echo "<pre>" . htmlspecialchars((string)$e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</pre>";
        exit;
    }
}

// تابع کمکی برای امن‌سازی خروجی HTML
function h($s) {
    if ($s === null) return '';
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// تابع برای ثبت حملات SQL Injection
function log_sql_injection($username, $query, $ip_address) {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("INSERT INTO injection_logs (username, query, ip_address, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$username, $query, $ip_address]);
    } catch (PDOException $e) {
        // در صورت خطا، فقط لاگ می‌کنیم و ادامه می‌دهیم
        error_log("خطا در ثبت حمله SQL Injection: " . $e->getMessage());
    }
}

// تابع برای ایجاد جدول‌های مورد نیاز اگر وجود نداشته باشند
function create_tables_if_not_exists() {
    $pdo = get_db_connection();
    
    // ایجاد جدول users اگر وجود نداشته باشد
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_plain VARCHAR(100) NOT NULL,
        role VARCHAR(20) NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // ایجاد جدول injection_logs برای ثبت حملات
    $pdo->exec("CREATE TABLE IF NOT EXISTS injection_logs (
        id SERIAL PRIMARY KEY,
        username VARCHAR(50),
        query TEXT NOT NULL,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // بررسی وجود کاربران پیش‌فرض و افزودن آنها در صورت نیاز
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // افزودن کاربران پیش‌فرض
        $pdo->exec("INSERT INTO users (username, password_plain, role) VALUES 
            ('admin', 'admin123', 'admin'),
            ('user1', 'password123', 'user'),
            ('manager', 'secure456', 'manager'),
            ('guest', 'guest789', 'guest')
        ");
    }
}
?>