<?php
// صفحه چالش - فروشگاه استاتیک
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فروشگاه آنلاین دانشگاه صنعتی ارومیه</title>
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
            padding: 0 15px;
        }
        
        header {
            background-color: var(--primary);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo img {
            height: 50px;
            margin-left: 10px;
        }
        
        .logo h1 {
            font-size: 1.5rem;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-right: 20px;
        }
        
        nav ul li:last-child {
            margin-right: 0;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        
        nav ul li a:hover {
            color: var(--warning);
        }
        
        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://via.placeholder.com/1200x400');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 4rem 0;
            margin-bottom: 2rem;
        }
        
        .hero h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        
        .btn {
            display: inline-block;
            background-color: var(--accent);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: var(--secondary);
        }
        
        .products {
            padding: 2rem 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--dark);
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .product-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-image {
            height: 200px;
            background-color: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .product-image img {
            max-width: 100%;
            max-height: 100%;
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-title {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        
        .product-price {
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .product-actions {
            display: flex;
            justify-content: space-between;
        }
        
        footer {
            background-color: var(--dark);
            color: white;
            padding: 2rem 0;
            margin-top: 2rem;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .footer-section {
            flex: 1;
            min-width: 200px;
            margin-bottom: 1rem;
        }
        
        .footer-section h3 {
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 0.5rem;
            display: inline-block;
        }
        
        .footer-section ul {
            list-style: none;
        }
        
        .footer-section ul li {
            margin-bottom: 0.5rem;
        }
        
        .footer-section ul li a {
            color: #ddd;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-section ul li a:hover {
            color: var(--accent);
        }
        
        .copyright {
            text-align: center;
            padding-top: 1rem;
            margin-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
            }
            
            nav ul {
                margin-top: 1rem;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
            
            .footer-content {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">
                <img src="uut_logo.png" alt="لوگوی دانشگاه صنعتی ارومیه">
                <h1>فروشگاه آنلاین دانشگاه</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#">صفحه اصلی</a></li>
                    <li><a href="#">محصولات</a></li>
                    <li><a href="#">درباره ما</a></li>
                    <li><a href="#">تماس با ما</a></li>
                    <li><a href="login.php" class="btn">ورود / ثبت نام</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <section class="hero">
        <div class="container">
            <h2>به فروشگاه آنلاین دانشگاه صنعتی ارومیه خوش آمدید</h2>
            <p>محصولات با کیفیت و قیمت مناسب برای دانشجویان و اساتید</p>
            <a href="login.php" class="btn">همین حالا وارد شوید</a>
        </div>
    </section>
    
    <section class="products">
        <div class="container">
            <h2 class="section-title">محصولات پرفروش</h2>
            <div class="product-grid">
                <!-- محصول 1 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/200" alt="لپ تاپ">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">لپ تاپ دانشجویی</h3>
                        <p class="product-price">۱۵,۰۰۰,۰۰۰ تومان</p>
                        <div class="product-actions">
                            <a href="#" class="btn">افزودن به سبد</a>
                        </div>
                    </div>
                </div>
                
                <!-- محصول 2 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/200" alt="کتاب">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">کتاب امنیت شبکه</h3>
                        <p class="product-price">۱۲۰,۰۰۰ تومان</p>
                        <div class="product-actions">
                            <a href="#" class="btn">افزودن به سبد</a>
                        </div>
                    </div>
                </div>
                
                <!-- محصول 3 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/200" alt="هدفون">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">هدفون بی‌سیم</h3>
                        <p class="product-price">۸۰۰,۰۰۰ تومان</p>
                        <div class="product-actions">
                            <a href="#" class="btn">افزودن به سبد</a>
                        </div>
                    </div>
                </div>
                
                <!-- محصول 4 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/200" alt="ماوس">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">ماوس گیمینگ</h3>
                        <p class="product-price">۳۵۰,۰۰۰ تومان</p>
                        <div class="product-actions">
                            <a href="#" class="btn">افزودن به سبد</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <footer>
        <div class="container footer-content">
            <div class="footer-section">
                <h3>دسترسی سریع</h3>
                <ul>
                    <li><a href="#">صفحه اصلی</a></li>
                    <li><a href="#">محصولات</a></li>
                    <li><a href="#">درباره ما</a></li>
                    <li><a href="#">تماس با ما</a></li>
                    <li><a href="login.php">ورود / ثبت نام</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>خدمات مشتریان</h3>
                <ul>
                    <li><a href="#">راهنمای خرید</a></li>
                    <li><a href="#">شیوه‌های پرداخت</a></li>
                    <li><a href="#">پیگیری سفارش</a></li>
                    <li><a href="#">بازگشت کالا</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>تماس با ما</h3>
                <ul>
                    <li>آدرس: ارومیه، دانشگاه صنعتی ارومیه</li>
                    <li>تلفن: ۰۴۴-۳۱۹۸۰۰۰۰</li>
                    <li>ایمیل: info@uut.ac.ir</li>
                </ul>
            </div>
        </div>
        
        <div class="container copyright">
            <p>&copy; ۱۴۰۳ - تمامی حقوق برای دانشگاه صنعتی ارومیه محفوظ است.</p>
        </div>
    </footer>
</body>
</html>