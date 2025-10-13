<?php
// صفحه داشبورد مدیریت - نمایش حملات SQL Injection
// این صفحه فقط برای مدیران قابل دسترسی است

// اتصال به دیتابیس
require_once 'db_config.php';

// شروع سشن
session_start();

// بررسی دسترسی مدیر
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // اگر کاربر مدیر نباشد، به صفحه ورود هدایت می‌شود
    header('Location: login.php');
    exit;
}

// دریافت اطلاعات کاربر از سشن
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// اتصال به دیتابیس
$pdo = get_db_connection();

// حذف لاگ در صورت درخواست
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM injection_logs WHERE id = ?");
    $stmt->execute([$delete_id]);
    header('Location: dashboard.php');
    exit;
}

// حذف همه لاگ‌ها در صورت درخواست
if (isset($_GET['clear_all']) && $_GET['clear_all'] === '1') {
    $pdo->exec("TRUNCATE TABLE injection_logs");
    header('Location: dashboard.php');
    exit;
}

// دریافت آمار کلی
$total_logs = $pdo->query("SELECT COUNT(*) FROM injection_logs")->fetchColumn();
$unique_ips = $pdo->query("SELECT COUNT(DISTINCT ip_address) FROM injection_logs")->fetchColumn();
$latest_attack = $pdo->query("SELECT created_at FROM injection_logs ORDER BY created_at DESC LIMIT 1")->fetchColumn();
$latest_attack = $latest_attack ? $latest_attack : 'هیچ';

// دریافت لیست حملات
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$stmt = $pdo->prepare("SELECT * FROM injection_logs ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $per_page, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll();

// محاسبه تعداد کل صفحات
$total_pages = ceil($total_logs / $per_page);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد مدیریت - دانشگاه صنعتی ارومیه</title>
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
            max-width: 1200px;
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
        
        .btn-warning {
            background-color: var(--warning);
            color: #212529;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 20px;
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .stat-card .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: var(--dark);
        }
        
        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .card-title {
            font-size: 18px;
            color: var(--dark);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .query-cell {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .query-cell:hover {
            white-space: normal;
            word-break: break-all;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        
        .badge-danger {
            background-color: var(--danger);
        }
        
        .badge-warning {
            background-color: var(--warning);
            color: #212529;
        }
        
        .badge-info {
            background-color: var(--info);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            margin-top: 20px;
        }
        
        .pagination li {
            margin: 0 5px;
        }
        
        .pagination a {
            display: block;
            padding: 8px 12px;
            background-color: white;
            border: 1px solid #ddd;
            color: var(--primary);
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .pagination a:hover {
            background-color: #f8f9fa;
        }
        
        .pagination .active a {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .pagination .disabled a {
            color: #6c757d;
            pointer-events: none;
            cursor: default;
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
        
        .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.2);
            color: #856404;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .modal-title {
            font-size: 18px;
            color: var(--dark);
        }
        
        .close {
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .modal-body {
            margin-bottom: 20px;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        
        .modal-footer .btn {
            margin-right: 10px;
        }
        
        .modal-footer .btn:last-child {
            margin-right: 0;
        }
        
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-family: monospace;
            white-space: pre-wrap;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="uut_logo.png" alt="لوگوی دانشگاه صنعتی ارومیه">
                <div class="logo-text">
                    <h1>داشبورد مدیریت</h1>
                    <p>دانشگاه صنعتی ارومیه - چالش SQL Injection</p>
                </div>
            </div>
            <div class="user-actions">
                <a href="info.php" class="btn" style="margin-left: 10px;">پروفایل کاربری</a>
                <a href="challenge.php" class="btn" style="margin-left: 10px;">بازگشت به فروشگاه</a>
                <a href="info.php?logout=1" class="btn btn-danger">خروج</a>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <div class="stat-card">
                <h3>تعداد کل حملات</h3>
                <div class="stat-value"><?php echo h($total_logs); ?></div>
            </div>
            <div class="stat-card">
                <h3>آدرس‌های IP منحصر به فرد</h3>
                <div class="stat-value"><?php echo h($unique_ips); ?></div>
            </div>
            <div class="stat-card">
                <h3>آخرین حمله</h3>
                <div class="stat-value" style="font-size: 18px;"><?php echo h($latest_attack); ?></div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">گزارش حملات SQL Injection</h2>
                <div>
                    <a href="show_users.php" class="btn btn-success" style="margin-left: 10px;">مشاهده کاربران</a>
                    <?php if ($total_logs > 0): ?>
                    <a href="?clear_all=1" class="btn btn-danger" onclick="return confirm('آیا از حذف تمام لاگ‌ها اطمینان دارید؟');">حذف همه لاگ‌ها</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (count($logs) === 0): ?>
            <div class="alert alert-info">
                <p>هیچ حمله SQL Injection ثبت نشده است.</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>نام کاربری</th>
                            <th>کوئری</th>
                            <th>آدرس IP</th>
                            <th>تاریخ</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo h($log['id']); ?></td>
                            <td><?php echo h($log['username']); ?></td>
                            <td class="query-cell" title="<?php echo h($log['query']); ?>"><?php echo h($log['query']); ?></td>
                            <td><?php echo h($log['ip_address']); ?></td>
                            <td><?php echo h($log['created_at']); ?></td>
                            <td>
                                <a href="#" class="btn btn-warning btn-sm view-query" data-query="<?php echo h($log['query']); ?>" data-username="<?php echo h($log['username']); ?>">بررسی</a>
                                <a href="?delete=<?php echo h($log['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('آیا از حذف این لاگ اطمینان دارید؟');">حذف</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <ul class="pagination">
                <li class="<?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a href="<?php echo $page <= 1 ? '#' : '?page='.($page-1); ?>">قبلی</a>
                </li>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="<?php echo $page == $i ? 'active' : ''; ?>">
                    <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                
                <li class="<?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a href="<?php echo $page >= $total_pages ? '#' : '?page='.($page+1); ?>">بعدی</a>
                </li>
            </ul>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">راهنمای تشخیص حملات SQL Injection</h2>
            </div>
            <div class="alert alert-warning">
                <p>حملات SQL Injection معمولاً با استفاده از کاراکترهای خاص مانند <code>'</code>، <code>"</code>، <code>--</code>، <code>;</code> و عباراتی مانند <code>OR 1=1</code> انجام می‌شوند.</p>
                <p>در این داشبورد، تمام تلاش‌های ورود به سیستم ثبت می‌شوند تا بتوانید حملات احتمالی را شناسایی کنید.</p>
            </div>
            <div>
                <h3 style="margin: 15px 0;">نمونه‌های رایج حملات SQL Injection:</h3>
                <ul style="padding-right: 20px;">
                    <li><code>' OR '1'='1</code> - برای دور زدن احراز هویت</li>
                    <li><code>' OR 1=1 --</code> - برای دور زدن احراز هویت با کامنت کردن بقیه کوئری</li>
                    <li><code>' UNION SELECT username, password FROM users --</code> - برای استخراج اطلاعات از جداول دیگر</li>
                    <li><code>'; DROP TABLE users; --</code> - برای حذف جداول</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p>چالش SQL Injection - دانشگاه صنعتی ارومیه</p>
        </div>
    </div>
    
    <!-- مودال بررسی کوئری -->
    <div id="queryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">بررسی کوئری SQL Injection</h3>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <p><strong>نام کاربری:</strong> <span id="modal-username"></span></p>
                <h4 style="margin: 15px 0 10px;">کوئری اجرا شده:</h4>
                <pre id="modal-query"></pre>
            </div>
            <div class="modal-footer">
                <button class="btn" id="closeModal">بستن</button>
            </div>
        </div>
    </div>
    
    <script>
        // اسکریپت برای مودال بررسی کوئری
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('queryModal');
            var closeBtn = document.getElementsByClassName('close')[0];
            var closeModalBtn = document.getElementById('closeModal');
            var viewQueryBtns = document.getElementsByClassName('view-query');
            
            // نمایش مودال با کلیک روی دکمه بررسی
            for (var i = 0; i < viewQueryBtns.length; i++) {
                viewQueryBtns[i].addEventListener('click', function(e) {
                    e.preventDefault();
                    var query = this.getAttribute('data-query');
                    var username = this.getAttribute('data-username');
                    
                    document.getElementById('modal-query').textContent = query;
                    document.getElementById('modal-username').textContent = username;
                    
                    modal.style.display = 'block';
                });
            }
            
            // بستن مودال با کلیک روی دکمه بستن
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            closeModalBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            // بستن مودال با کلیک خارج از محتوای مودال
            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>