<!doctype html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>بوت‌کمپ SQL Injection — دانشگاه صنعتی ارومیه</title>
  <style>
    :root{
      --bg:#0b0f13; --card:#0f1720; --muted:#9aa5b1; --accent:#00ff99;
      --danger:#ff4d6d; --glass: rgba(255,255,255,0.03); --glass-2: rgba(255,255,255,0.02);
      font-family: "Segoe UI", Tahoma, "Helvetica Neue", Arial, sans-serif;
    }
    *{box-sizing:border-box}
    html,body{height:100%;margin:0;background:linear-gradient(180deg,#071017 0%, #0b0f13 100%);color:#e6eef3}
    a{color:inherit;text-decoration:none}
    .container{max-width:1120px;margin:28px auto;padding:20px}
    header{display:flex;align-items:center;gap:14px;margin-bottom:18px}
    .logo-wrap{display:flex;align-items:center;gap:12px;flex:1}
    .logo-group{width:64px;height:64px;flex-shrink:0;border-radius:12px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, rgba(0,255,153,0.08), rgba(0,199,255,0.04));border:1px solid rgba(0,255,153,0.06)}
    .logo-group img{max-width:56px;max-height:56px;display:block;border-radius:8px}
    .logo-uni img{width:40px;height:40px;display:block}
    h1{font-size:18px;margin:0}
    .lead{margin-top:6px;color:var(--muted);font-size:13px}

    .grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
    .card{background:linear-gradient(180deg,var(--glass),var(--glass-2));padding:16px;border-radius:12px;border:1px solid rgba(255,255,255,0.02);box-shadow:0 8px 30px rgba(2,6,23,0.6)}
    .card h2{margin:0 0 8px;font-size:15px}
    .muted{color:var(--muted);font-size:13px}

    .terminal{background:#040507;border-radius:10px;padding:12px;font-family: "Courier New", monospace;height:120px;color:#cceedd;overflow:auto;border:1px solid rgba(0,255,153,0.04)}
    pre.code{background:#071015;border-radius:8px;padding:12px;overflow:auto;font-family:monospace;margin:0;border:1px solid rgba(255,255,255,0.02)}
    .code-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
    .btn{background:transparent;border:1px solid rgba(255,255,255,0.06);padding:8px 10px;border-radius:8px;color:var(--muted);cursor:pointer}
    .btn:hover{border-color:rgba(0,255,153,0.14);color:var(--accent)}
    .badge{background:rgba(0,0,0,0.35);padding:5px 8px;border-radius:8px;font-size:12px;color:var(--muted)}

    .outbox{background:#061018;border-radius:8px;padding:10px;font-family:monospace;color:#bfe6c9;min-height:50px}

    footer{margin-top:20px;color:var(--muted);font-size:13px;text-align:center}
    @media(max-width:920px){.grid{grid-template-columns:1fr}header{flex-direction:column;align-items:flex-start;gap:10px}}
    .row{display:flex;gap:8px;align-items:center}
    input[type="text"]{width:100%;padding:8px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);background:transparent;color:inherit}
    .actions{display:flex;gap:8px;flex-wrap:wrap}
    form{margin:0}
  </style>
</head>
<body>
  <div class="container">
    <!-- HEADER -->
    <header>
      <div class="logo-wrap">
        <div class="logo-group" aria-hidden>
          <img src="group_logo.png" alt="لوگوی گروه بوت‌کمپ" onerror="this.style.display='none'">
        </div>
        <div>
          <h1>بوت‌کمپ: بررسی و پیشگیری از SQL Injection</h1>
          <div class="lead">دانشگاه صنعتی ارومیه — دانشکدهٔ مهندسی کامپیوتر</div>
        </div>
      </div>
      <div style="margin-inline-start:12px;flex-shrink:0;display:flex;align-items:center;">
        <a href="https://www.urmia.ac.ir" target="_blank" rel="noopener" title="دانشگاه صنعتی ارومیه">
          <img src="uni_logo.svg" alt="لوگوی دانشگاه صنعتی ارومیه" class="logo-uni" onerror="this.style.display='none'">
        </a>
      </div>
    </header>

    <!-- MAIN -->
    <main class="grid">
      <section>
        <div class="card">
          <h2>معرفی مختصر</h2>
          <p class="muted">این بوت‌کمپ به روش‌های کشف و پیشگیری از SQL Injection می‌پردازد. تمامی تمرین‌ها ایزوله و امن اجرا می‌شوند.</p>
        </div>

        <div class="card" style="margin-top:12px">
          <div class="code-header">
            <div>
              <h2>نمونهٔ امن — PHP (PDO)</h2>
              <div class="muted">نمونهٔ نمایشی؛ همیشه از prepared statements استفاده کنید.</div>
            </div>
            <div class="badge">نمونهٔ امن</div>
          </div>
          <div style="margin-top:10px;display:flex;gap:8px;justify-content:flex-end">
            <button class="btn" id="copyCode">کپی کد</button>
            <button class="btn" id="showWarning">هشدار ایمنی</button>
          </div>
        </div>

        <div class="card" style="margin-top:12px">
          <h2>روش‌های جلوگیری (خلاصه)</h2>
          <ul class="muted">
            <li>Prepared Statements / ORM</li>
            <li>اعتبارسنجی و whitelist ورودی‌ها</li>
            <li>least privilege برای حساب DB</li>
            <li>WAF و بررسی لاگ‌ها</li>
            <li>SAST/DAST و تست دوره‌ای</li>
          </ul>

          <div style="margin-top:12px" class="actions">
            <a class="btn" href="#" id="downloadSlides">دانلود اسلاید (PPT)</a>
            <a class="btn" href="#" id="repoLink">نمایش ریپو در گیت‌هاب</a>
          </div>
        </div>
      </section>

      <aside class="card">
        <h2>پنل مدرس / دموی شبیه‌سازی</h2>
        <p class="muted">برای امنیت، هیچ‌چیز مستقیم به دیتابیس ارسال نمی‌شود — اینجا فقط خروجی شبیه‌سازی می‌شود.</p>

        <!-- فرم ارسال به سرور (POST) -->
        <form method="post" novalidate>
          <div style="margin-top:12px">
            <label class="muted">Username (ورودی کاربر):</label>
            <input id="username" name="username" type="text" placeholder="مثال: ali یا elahe" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8') : ''; ?>"/>
          </div>

          <div style="margin-top:10px">
            <button class="btn" type="submit" style="width:100%">ارسال و نمایش (ایمن)</button>
          </div>
        </form>

        <div style="margin-top:12px">
          <div class="muted">خروجی:</div>
          <div class="outbox" id="outbox"><?php echo $resultMessage ?: 'هنوز چیزی اجرا نشده.'; ?></div>
        </div>

        <hr style="border:none;border-top:1px dashed rgba(255,255,255,0.03);margin:12px 0">

        <h3 style="margin:6px 0">نکات اجرای امن آزمایشگاه</h3>
        <div class="muted" style="font-size:13px">
          ۱) همه تمرین‌ها در VM یا Docker اجرا شوند (publicly inaccessible).<br>
          ۲) از دیتابیس واقعی استفاده نکنید — فقط نمونهٔ تست لوکال.<br>
          ۳) قبل و بعد از هر جلسه snapshot بگیرید.<br>
          ۴) نمایش payload فقط در اسلاید/متن، اجرای payload در شبکهٔ عمومی ممنوع.
        </div>
      </aside>
    </main>

    <footer>
      دانشگاه صنعتی ارومیه — بوت‌کمپ SQL Injection | منابع: OWASP, PHP.net, MDN
    </footer>
  </div>

  <script>
    // client-side interactivity (copy, alert)
    document.getElementById('copyCode').addEventListener('click', async () => {
      const code = document.getElementById('codeBlock').innerText;
      try { await navigator.clipboard.writeText(code); alert('کد در کلیپ‌بورد کپی شد.'); }
      catch (e) { prompt('این کد را کپی کنید:', code); }
    });

    document.getElementById('showWarning').addEventListener('click', () => {
      alert('هشدار: هرگز کدهای آسیب‌پذیر را در محیط عمومی اجرا نکنید. تمرین‌ها را در VM/Docker ایزوله انجام دهید.');
    });

    // وقتی سرور پاسخی داده، می‌خواهیم آن را به صورت قابل scroll نمایش دهیم
    (function(){
      const out = document.getElementById('outbox');
      if(out && out.innerText && out.innerText.trim() !== 'هنوز چیزی اجرا نشده.') {
        // کوچک کردن احتمال XSS: خروجی سرور از htmlspecialchars عبور کرده
        out.scrollIntoView({behavior:'smooth'});
      }
    })();
  </script>
</body>
</html>
