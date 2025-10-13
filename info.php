<?php
// صفحه نمایش اطلاعات کاربری
// این صفحه اطلاعات کاربر وارد شده به سیستم را نمایش می‌دهد

// اتصال به دیتابیس
require_once 'db_config.php';

// شروع سشن
session_start();

// بررسی وضعیت ورود کاربر
if (!isset($_SESSION['user_id'])) {
    // اگر کاربر وارد نشده باشد، به صفحه ورود هدایت می‌شود
    header('Location: login.php');
    exit;
}

// دریافت اطلاعات کاربر از سشن
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// دریافت اطلاعات کامل کاربر از دیتابیس
$pdo = get_db_connection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// تنظیم متغیرهای نمایشی بر اساس نقش کاربر
$is_admin = ($role === 'admin');
$welcome_message = $is_admin ? 'مدیر گرامی، خوش آمدید!' : 'کاربر گرامی، خوش آمدید!';
$role_display = $is_admin ? 'مدیر سیستم' : 'کاربر عادی';

// پردازش درخواست خروج
if (isset($_GET['logout']) && $_GET['logout'] === '1') {
    // پاک کردن سشن و هدایت به صفحه ورود
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پروفایل کاربری - دانشگاه صنعتی ارومیه</title>
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
        }
        
        .container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #ddd;
            margin-bottom: 30px;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo img {
            width: 60px;
            height: 60px;
            margin-left: 15px;
        }
        
        .logo-text h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .logo-text p {
            font-size: 14px;
            color: #777;
        }
        
        .user-actions {
            display: flex;
            align-items: center;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: var(--secondary);
        }
        
        .btn-danger {
            background-color: var(--danger);
        }
        
        .btn-danger:hover {
            background-color: #bd2130;
        }
        
        .btn-success {
            background-color: var(--success);
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .profile-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .profile-sidebar {
            flex: 1;
            min-width: 250px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 20px;
        }
        
        .profile-main {
            flex: 2;
            min-width: 300px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 20px;
        }
        
        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: #e9ecef;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #adb5bd;
            overflow: hidden;
        }
        
        .profile-image span {
            text-transform: uppercase;
        }
        
        .profile-info {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .profile-info h2 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .profile-info p {
            color: #6c757d;
            font-size: 14px;
        }
        
        .profile-menu {
            list-style: none;
            margin-top: 20px;
        }
        
        .profile-menu li {
            margin-bottom: 10px;
        }
        
        .profile-menu a {
            display: block;
            padding: 10px;
            color: var(--dark);
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        
        .profile-menu a:hover {
            background-color: #f8f9fa;
        }
        
        .profile-menu a.active {
            background-color: var(--primary);
            color: white;
        }
        
        .section-title {
            font-size: 18px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .info-group {
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 15px;
        }
        
        .info-label {
            width: 150px;
            font-weight: bold;
            color: #495057;
        }
        
        .info-value {
            flex: 1;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        
        .badge-primary {
            background-color: var(--primary);
        }
        
        .badge-success {
            background-color: var(--success);
        }
        
        .badge-warning {
            background-color: var(--warning);
            color: #212529;
        }
        
        .badge-danger {
            background-color: var(--danger);
        }
        
        .admin-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-info {
            background-color: rgba(23, 162, 184, 0.1);
            border: 1px solid rgba(23, 162, 184, 0.2);
            color: var(--info);
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="uut_logo.png" alt="لوگوی دانشگاه صنعتی ارومیه">
                <div class="logo-text">
                    <h1>پروفایل کاربری</h1>
                    <p>دانشگاه صنعتی ارومیه</p>
                </div>
            </div>
            <div class="user-actions">
                <?php if ($is_admin): ?>
                    <a href="dashboard.php" class="btn btn-success" style="margin-left: 10px;">پنل مدیریت</a>
                <?php endif; ?>
                <a href="challenge.php" class="btn" style="margin-left: 10px;">بازگشت به فروشگاه</a>
                <a href="?logout=1" class="btn btn-danger">خروج</a>
            </div>
        </div>
        
        <div class="profile-container">
            <div class="profile-sidebar">
                <div class="profile-image">
                    <span><?php echo substr($username, 0, 1); ?></span>
                </div>
                <div class="profile-info">
                    <h2><?php echo h($username); ?></h2>
                    <p><?php echo h($role_display); ?></p>
                </div>
                <ul class="profile-menu">
                    <li><a href="#" class="active">اطلاعات حساب کاربری</a></li>
                    <li><a href="#">سفارش‌های من</a></li>
                    <li><a href="#">علاقه‌مندی‌ها</a></li>
                    <li><a href="#">تنظیمات</a></li>
                </ul>
            </div>
            
            <div class="profile-main">
                <h3 class="section-title">اطلاعات حساب کاربری</h3>
                
                <div class="info-group">
                    <div class="info-item">
                        <div class="info-label">نام کاربری:</div>
                        <div class="info-value"><?php echo h($username); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">نقش کاربری:</div>
                        <div class="info-value">
                            <span class="badge <?php echo $is_admin ? 'badge-danger' : 'badge-primary'; ?>">
                                <?php echo h($role_display); ?>
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">شناسه کاربری:</div>
                        <div class="info-value"><?php echo h($user_id); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">تاریخ عضویت:</div>
                        <div class="info-value"><?php echo h($user['created_at']); ?></div>
                    </div>
                    <?php if ($is_admin): ?>
                    <div class="info-item">
                        <div class="info-label">رمز عبور (متن ساده):</div>
                        <div class="info-value"><?php echo h($user['password_plain']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($is_admin): ?>
                <div class="admin-section">
                    <h3 class="section-title">بخش مدیریت</h3>
                    <div class="alert alert-info">
                        <p>مدیر گرامی، شما دسترسی کامل به سیستم دارید. برای مشاهده گزارش حملات SQL Injection به پنل مدیریت مراجعه کنید.</p>
                    </div>
                    <a href="dashboard.php" class="btn btn-success">مشاهده پنل مدیریت</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer">
            <p>چالش SQL Injection - دانشگاه صنعتی ارومیه</p>
        </div>
    </div>
</body>
</html>