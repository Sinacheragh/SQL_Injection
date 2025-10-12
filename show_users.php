<?php
// show_users.php
// نمایش جدول users بدون ورودی/سشن — فقط خواندن و نمایش.
// پس از مشاهده حذف شود.

// اگر DATABASE_URL در Environment ست شده باشد از آن استفاده می‌کنیم.
// در غیر این صورت از مقادیر پیش‌فرض (internal) که توی Render گفتی استفاده می‌کنیم.
$dsn_env = getenv('DATABASE_URL') ?: 'postgresql://sql_injection_xafp_user:OUjsmNfESr13fgzTtceZ0OVd0RYP5OHn@dpg-d3m12fc9c44c73eqerb0-a/sql_injection_xafp';
$parts = parse_url($dsn_env);

// اگر parse درست نبود، خطا بده
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

// اگر host شامل domain کامل (اکسترنال) بود sslmode=require می‌گذاریم
$sslmode = (strpos($host, '.oregon-postgres.render.com') !== false) ? 'require' : 'disable';

$pdo_dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
if ($sslmode === 'require') {
    $pdo_dsn .= ";sslmode=require";
}

try {
    $pdo = new PDO($pdo_dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo "<h2>خطا در اتصال به دیتابیس</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    exit;
}

// خواندن رکوردها
try {
    $stmt = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY id");
    $rows = $stmt->fetchAll();
} catch (PDOException $e) {
    http_response_code(500);
    echo "<h2>خطا در اجرای کوئری</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    exit;
}

// تابع کمکی برای امن نمایش متن در HTML
function h($s){ return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

?>
<!doctype html>
<html lang="fa">
<head>
  <meta charset="utf-8">
  <title>نمایش جدول users</title>
  <style>
    body{font-family: Tahoma, Arial; direction: rtl; padding:20px; background:#f9f9f9;}
    table{border-collapse:collapse; width:100%; max-width:900px; margin:12px 0; background:#fff;}
    th, td{border:1px solid #ddd; padding:10px; text-align:left;}
    th{background:#f0f0f0;}
    caption{font-size:1.2em; margin-bottom:8px; font-weight:bold;}
    .meta{color:#666; margin-bottom:8px;}
    .note{color:#a33; margin-top:14px; font-weight:bold;}
  </style>
</head>
<body>
  <h1>نمایش جدول <code>users</code></h1>
  <div class="meta">اتصال به: <code><?php echo h("{$host}:{$port}/{$dbname}"); ?></code></div>

  <?php if (count($rows) === 0): ?>
    <p>هیچ رکوردی یافت نشد.</p>
  <?php else: ?>
    <table>
      <caption>لیست کاربران (پسورد نمایش داده نشده)</caption>
      <thead>
        <tr><th>id</th><th>username</th><th>role</th><th>created_at</th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php echo h($r['id']); ?></td>
            <td><?php echo h($r['username']); ?></td>
            <td><?php echo h($r['role']); ?></td>
            <td><?php echo h($r['created_at']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <p class="note">تذکر امنیتی: این فایل مستقیم به دیتابیس وصل می‌شود. پس از دیدن نتایج حتماً آن را حذف کن.</p>
</body>
</html>
