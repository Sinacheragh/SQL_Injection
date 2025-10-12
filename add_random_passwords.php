<?php
// add_random_passwords.php
//  - اگر ستون password_plain وجود نداشته باشد، آن را اضافه می‌کند.
//  - برای هر ردیف یک پسورد رندوم (قابل خواندن) تولید و در password_plain قرار می‌دهد.
//  - سپس جدول را نمایش می‌دهد.
//  بعد از اجرا: حتماً فایل را حذف کن.

// === تنظیم اتصال ===
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

// sslmode در صورت نیاز
$sslmode = (strpos($host, '.oregon-postgres.render.com') !== false) ? 'require' : 'disable';
$pdo_dsn = "pgsql:host={$host};port={$port};dbname={$dbname}" . ($sslmode === 'require' ? ";sslmode=require" : "");

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

// helper برای html-safe
function h($s){ return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

// تابع تولید پسورد رندوم خوانا (مثلاً 12 کاراکتری با حروف و اعداد)
function random_password($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $max = strlen($chars) - 1;
    $res = '';
    // از random_int برای رمزنگاری بهتر استفاده می‌کنیم
    for ($i = 0; $i < $length; $i++) {
        $res .= $chars[random_int(0, $max)];
    }
    return $res;
}

echo "<h2>اضافه کردن ستون password_plain و تولید پسوردهای رندوم</h2>";
echo "<p>اتصال به: <code>" . h("{$host}:{$port}/{$dbname}") . "</code></p>";

// 1) بررسی وجود ستون password_plain
try {
    $colCheck = $pdo->prepare("
        SELECT column_name
        FROM information_schema.columns
        WHERE table_name = 'users' AND column_name = 'password_plain'
    ");
    $colCheck->execute();
    $exists = $colCheck->fetch();

    if (!$exists) {
        // اضافه کردن ستون
        $pdo->exec("ALTER TABLE users ADD COLUMN password_plain VARCHAR(255)");
        echo "<p>✅ ستون <code>password_plain</code> اضافه شد.</p>";
    } else {
        echo "<p>ℹ️ ستون <code>password_plain</code> از قبل وجود دارد.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>خطا در بررسی/اضافه کردن ستون: " . h($e->getMessage()) . "</p>";
    exit;
}

// 2) تولید و آپدیت پسورد رندوم برای هر ردیف که password_plain خالی یا NULL داشته باشد
try {
    // گرفتن همه کاربران
    $rows = $pdo->query("SELECT id, username, password_plain FROM users ORDER BY id")->fetchAll();

    $updateStmt = $pdo->prepare("UPDATE users SET password_plain = ? WHERE id = ?");

    $countUpdated = 0;
    foreach ($rows as $r) {
        // اگر ستون خالی یا NULL یا طول صفر است، مقدار رندوم می‌گذاریم
        $need = (!isset($r['password_plain']) || $r['password_plain'] === '');
        if ($need) {
            $rand = random_password(12);
            $updateStmt->execute([$rand, $r['id']]);
            $countUpdated++;
        }
    }

    echo "<p>✅ تعداد ردیف‌های به‌روز شده: " . h($countUpdated) . "</p>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>خطا در آپدیت پسوردها: " . h($e->getMessage()) . "</p>";
    exit;
}

// 3) نمایش جدول نهایی (بدون نشان دادن ستون password اصلی اگر نمی‌خواهی، اما اینجا password_plain را نشان می‌دهیم)
try {
    $final = $pdo->query("SELECT id, username, role, created_at, password_plain FROM users ORDER BY id")->fetchAll();
} catch (PDOException $e) {
    echo "<p style='color:red;'>خطا در خواندن نهایی: " . h($e->getMessage()) . "</p>";
    exit;
}

// نمایش زیبا
?>
<!doctype html>
<html lang="fa">
<head>
  <meta charset="utf-8">
  <title>password_plain — users</title>
  <style>
    body{font-family: Tahoma, Arial; direction: rtl; padding:20px; background:#fff;}
    table{border-collapse:collapse; width:100%; max-width:1000px; margin:12px 0; background:#fff;}
    th, td{border:1px solid #ddd; padding:10px; text-align:left; font-size:14px;}
    th{background:#f0f0f0;}
    caption{font-size:1.1em; margin-bottom:8px; font-weight:bold;}
    .meta{color:#666; margin-bottom:8px;}
    .note{color:#a33; margin-top:14px; font-weight:bold;}
    .mono{font-family:monospace; font-size:13px; background:#f6f6f6; padding:6px; display:inline-block;}
  </style>
</head>
<body>
  <h1>محتوای جدول <code>users</code> (شامل <code>password_plain</code>)</h1>
  <div class="meta">اتصال: <code><?php echo h("{$host}:{$port}/{$dbname}"); ?></code></div>

  <?php if (empty($final)): ?>
    <p>هیچ رکوردی وجود ندارد.</p>
  <?php else: ?>
    <table>
      <caption>لیست کاربران</caption>
      <thead>
        <tr><th>id</th><th>username</th><th>role</th><th>created_at</th><th>password_plain</th></tr>
      </thead>
      <tbody>
        <?php foreach ($final as $r): ?>
          <tr>
            <td><?php echo h($r['id']); ?></td>
            <td><?php echo h($r['username']); ?></td>
            <td><?php echo h($r['role']); ?></td>
            <td><?php echo h($r['created_at']); ?></td>
            <td class="mono"><?php echo h($r['password_plain']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <p class="note">تذکر امنیتی: این فایل حاوی پسوردهای خوانا است — پس از مشاهده حتماً فایل را حذف یا غیرفعال کن.</p>
</body>
</html>
