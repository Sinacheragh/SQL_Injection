<?php
// show_users_safe.php (جایگزین show_users.php کن)
// نمایش جدول users بدون ورودی/سشن — اما بدون Warning/Deprecated.
// پس از مشاهده حذف شود.

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
} catch (PDOException $e) {
    http_response_code(500);
    echo "<h2>خطا در اتصال به دیتابیس</h2>";
    echo "<pre>" . htmlspecialchars((string)$e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</pre>";
    exit;
}

// خواندن رکوردها
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id");
    $rows = $stmt->fetchAll();
} catch (PDOException $e) {
    http_response_code(500);
    echo "<h2>خطا در اجرای کوئری</h2>";
    echo "<pre>" . htmlspecialchars((string)$e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</pre>";
    exit;
}

// helper: امن کردن خروجی html (null => '')
function h($s) {
    if ($s === null) return '';
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// بررسی اینکه ستون password_plain وجود دارد یا نه
$has_password_plain = false;
if (!empty($rows)) {
    // نگاه می‌کنیم کلید password_plain برای اولین ردیف وجود دارد یا نه
    $first = $rows[0];
    $has_password_plain = array_key_exists('password_plain', $first);
}

?>
<!doctype html>
<html lang="fa">
<head>
  <meta charset="utf-8">
  <title>نمایش جدول users (safe)</title>
  <style>
    body{font-family: Tahoma, Arial; direction: rtl; padding:20px; background:#f9f9f9;}
    table{border-collapse:collapse; width:100%; max-width:1000px; margin:12px 0; background:#fff;}
    th, td{border:1px solid #ddd; padding:10px; text-align:left;}
    th{background:#f0f0f0;}
    caption{font-size:1.2em; margin-bottom:8px; font-weight:bold;}
    .meta{color:#666; margin-bottom:8px;}
    .note{color:#a33; margin-top:14px; font-weight:bold;}
    .mono{font-family:monospace; font-size:13px; background:#f6f6f6; padding:6px; display:inline-block;}
  </style>
</head>
<body>
  <h1>نمایش جدول <code>users</code></h1>
  <div class="meta">اتصال: <code><?php echo h("{$host}:{$port}/{$dbname}"); ?></code></div>

  <?php if (empty($rows)): ?>
    <p>هیچ رکوردی یافت نشد.</p>
  <?php else: ?>
    <table>
      <caption>لیست کاربران (ستون password_plain فقط در صورت موجود بودن نمایش داده می‌شود)</caption>
      <thead>
        <tr>
          <th>id</th>
          <th>username</th>
          <th>role</th>
          <th>created_at</th>
          <?php if ($has_password_plain): ?><th>password_plain</th><?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php echo h($r['id'] ?? ''); ?></td>
            <td><?php echo h($r['username'] ?? ''); ?></td>
            <td><?php echo h($r['role'] ?? ''); ?></td>
            <td><?php echo h($r['created_at'] ?? ''); ?></td>
            <?php if ($has_password_plain): ?>
              <td class="mono"><?php echo h($r['password_plain'] ?? ''); ?></td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <p class="note">تذکر امنیتی: این فایل مستقیم به دیتابیس وصل می‌شود. پس از مشاهده حذف شود.</p>
</body>
</html>
