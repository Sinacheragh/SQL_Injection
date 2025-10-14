<?php
// صفحه ورود با قابلیت SQL Injection
// این صفحه عمداً آسیب‌پذیر به حمله SQL Injection است

// اتصال به دیتابیس
require_once 'db_config.php';

// بررسی ارسال فرم
$error = '';
$username = '';
$is_logged_in = false;
$user_data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $error = 'لطفاً نام کاربری و رمز عبور را وارد کنید.';
    } else {
        // اتصال به دیتابیس
        $pdo = get_db_connection();
        
        // عمداً آسیب‌پذیر به SQL Injection
        // از ورودی کاربر مستقیماً در کوئری استفاده می‌شود
        $query = "SELECT * FROM users WHERE username = '$username' AND password_plain = '$password'";
        
        try {
            // ثبت تلاش ورود برای بررسی حملات احتمالی
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            log_sql_injection($username, $query, $ip_address);
            
            $result = $pdo->query($query);
            $user = $result->fetch();
            
            if ($user) {
                // ورود موفق
                $is_logged_in = true;
                $user_data = $user;
                
                // ذخیره اطلاعات کاربر در سشن
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // هدایت به صفحه اطلاعات کاربری
                header('Location: info.php');
                exit;
            } else {
                $error = 'نام کاربری یا رمز عبور اشتباه است.';
            }
        } catch (PDOException $e) {
            // در صورت خطا در کوئری، پیام خطا را نمایش می‌دهیم
            // این بخش برای آموزش است و در محیط واقعی نباید جزئیات خطا نمایش داده شود
            $error = 'خطا در اجرای کوئری: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به سیستم - دانشگاه صنعتی ارومیه</title>
    <style>
        :root {
            --primary: #0066cc;
            --secondary: #004080;
            --accent: #00a0e3;
            --light: #f5f5f5;
            --dark: #333;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: Tahoma, Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        
        .login-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
        }
        
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        h1 {
            font-size: 24px;
            color: var(--dark);
            margin-bottom: 5px;
        }
        
        .subtitle {
            color: #777;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--dark);
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: var(--accent);
            outline: none;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: var(--secondary);
        }
        
        .error {
            color: var(--danger);
            background-color: rgba(220, 53, 69, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .links {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }
        
        .links a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        .hint {
            margin-top: 30px;
            padding: 15px;
            background-color: rgba(23, 162, 184, 0.1);
            border-radius: 5px;
            font-size: 14px;
            color: var(--info);
        }
        
        .hint h3 {
            margin-bottom: 10px;
            color: var(--info);
        }
        
        .hint code {
            background-color: #f8f9fa;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="header">
                <div class="logo">
                    <img src="uut_logo.png" alt="لوگوی دانشگاه صنعتی ارومیه">
                </div>
                <h1>ورود به سیستم</h1>
                <p class="subtitle">فروشگاه آنلاین دانشگاه صنعتی ارومیه</p>
            </div>
            
            <?php if ($error): ?>
                <div class="error">
                    <?php echo h($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">نام کاربری:</label>
                    <input type="text" id="username" name="username" value="<?php echo h($username); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">رمز عبور:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn">ورود</button>
            </form>
            
            <div class="links">
                <a href="challenge.php">بازگشت به فروشگاه</a>
            </div>
            
            <div class="hint">
                <h3>راهنمای چالش:</h3>
                <p>این صفحه آسیب‌پذیر به حمله SQL Injection است. سعی کنید با استفاده از تکنیک‌های SQL Injection وارد سیستم شوید.</p>
                <p>برای مثال، می‌توانید از عبارات <code>' OR '1'='1</code> استفاده کنید.</p>
            </div>
        </div>
    </div>
</body>
</html>