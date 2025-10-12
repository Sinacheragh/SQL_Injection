<?php
// migrate_create_users.php
// اجرا: از طریق مرورگر یا CLI (php migrate_create_users.php)
// بعد از اجرا حذف شود!

// === تنظیمات پیش‌فرض از اطلاعاتی که دادی ===
// چون می‌گی فایل رو داخل Render باز می‌کنی، default روی internal URL قرار داده شده.
// در صورتی که می‌خوای از بیرون وصل بشی، از external استفاده کن یا متغیر محیطی DATABASE_URL را ست کن.
$default_dsn = 'postgresql://sql_injection_xafp_user:OUjsmNfESr13fgzTtceZ0OVd0RYP5OHn@dpg-d3m12fc9c44c73eqerb0-a/sql_injection_xafp';

// اگر متغیر محیطی تنظیم شده باشه او را استفاده کن (پیشنهاد امن)
$dsn_env = getenv('DATABASE_URL') ?: getenv('DATABASE_URL_POSTGRES') ?: $default_dsn;

// پارس کردن DSN مثل: postgresql://user:pass@host:port/dbname
$parts = parse_url($dsn_env);
if (!$parts || !isset($parts['host'])) {
    die("Invalid DATABASE_URL: $dsn_env");
}

$host = $parts['host'];
$port = isset($parts['port']) ? $parts['port'] : 5432;
$user = isset($parts['user']) ? $parts['user'] : '';
$pass = isset($parts['pass']) ? $parts['pass'] : '';
$dbname = isset($parts['path']) ? ltrim($parts['path'], '/') : '';

/*
 Optional: اگر می‌خواهی اتصال SSL صریحاً فعال باشد (برای external hostها ممکن لازم باشد)
 می‌توانیم sslmode=require اضافه کنیم. Render internal معمولاً نیازی ندارد.
*/
$sslmode = (strpos($host, '.oregon-postgres.render.com') !== false) ? 'require' : 'disable';

// ساخت DSN برای PDO
$pdo_dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
if ($sslmode === 'require') {
    $pdo_dsn .= ";sslmode=require";
}

// اتصال با PDO
try {
    $pdo = new PDO($pdo_dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo "<h2>خطا در اتصال به دیتابیس</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    exit;
}

// تابعی برای چاپ امن خروجی (html)
function h($s){ return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

echo "<h2>اجرای مهاجرت: ایجاد جدول users و درج کاربران نمونه</h2>";
echo "<p>اتصال به: <code>" . h($host . ":" . $port . "/" . $dbname) . "</code></p>";

// 1) ساخت جدول اگر وجود ندارد
try {
    $create_sql = "
    CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) NOT NULL CHECK (role IN ('admin', 'customer')),
        created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $pdo->exec($create_sql);
    echo "<p>✅ جدول <code>users</code> ایجاد یا از قبل موجود است.</p>";
} catch (PDOException $e) {
    echo "<p style='color:crimson;'>خطا در ایجاد جدول: " . h($e->getMessage()) . "</p>";
    exit;
}

// 2) کاربران نمونه را فقط اگر وجود ندارند درج کن
$sample_users = [
    ['admin1', 'adminpass123', 'admin'],
    ['customer1', 'custpass123', 'customer']
];

$check_stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$insert_stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");

foreach ($sample_users as $u) {
    list($username, $plain_pass, $role) = $u;

    try {
        $check_stmt->execute([$username]);
        $exists = $check_stmt->fetch(PDO::FETCH_ASSOC);
        if ($exists) {
            echo "<p>ℹ️ کاربر <strong>" . h($username) . "</strong> از قبل وجود دارد.</p>";
            continue;
        }

        // هش کردن امن رمز
        $hash = password_hash($plain_pass, PASSWORD_DEFAULT);
        $insert_stmt->execute([$username, $hash, $role]);
        echo "<p>✅ کاربر <strong>" . h($username) . "</strong> با نقش <code>" . h($role) . "</code> درج شد.</p>";
    } catch (PDOException $e) {
        echo "<p style='color:crimson;'>خطا برای کاربر " . h($username) . ": " . h($e->getMessage()) . "</p>";
    }
}

// 3) نمایش خلاصه اطلاعات (بدون نشان دادن پسورد)
try {
    $rows = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>لیست کاربران (بدون پسورد)</h3>";
    echo "<table border='1' cellpadding='6'><tr><th>id</th><th>username</th><th>role</th><th>created_at</th></tr>";
    foreach ($rows as $r) {
        echo "<tr><td>" . h($r['id']) . "</td><td>" . h($r['username']) . "</td><td>" . h($r['role']) . "</td><td>" . h($r['created_at']) . "</td></tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "<p style='color:crimson;'>خطا در خواندن کاربران: " . h($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p style='color:orange;'><strong>نکته مهم امنیتی:</strong> این فایل حاوی منطق مهاجرت/درج است — پس از اجرای موفق <strong>حتماً</strong> فایل را حذف کن تا از دسترسی ناخواسته جلوگیری شود.</p>";
echo "<p>اگر می‌خوای، بعد از حذف فایل می‌تونم یک endpoint ساده برای ثبت کاربر امن یا صفحه لاگین هم برات آماده کنم.</p>";
?>
