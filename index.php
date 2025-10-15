<!doctype html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>بوت‌کمپ SQL Injection ، دانشگاه صنعتی ارومیه</title>
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
    .logo-group{width:80px;height:80px;flex-shrink:0;border-radius:12px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, rgba(0,255,153,0.08), rgba(0,199,255,0.04));border:1px solid rgba(0,255,153,0.06)}
    .logo-group img{max-width:56px;max-height:56px;display:block;border-radius:8px}
    .logo-uni img{width:40px;height:40px;display:block}
    h1{font-size:30px;margin:0}
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

    /* ===== HEADER hover ===== */
    .logo-group{
      transition: transform .18s ease, box-shadow .18s ease;
      will-change: transform;
    }
    .logo-group:hover{
      transform: translateY(-6px) scale(1.04);
      box-shadow: 0 14px 40px rgba(0,255,153,0.06);
    }

    h1,h2,h3{
      transition: color .18s ease, transform .18s ease;
    }
    h2:hover,h3:hover,h1:hover{
          color: var(--accent);
          transform: translateY(-2px);

    }

    @media(max-width:920px){
      header{padding-bottom:6px}
      .logo-group{width:56px;height:56px}
      h1{font-size:24px}
    }

    /* ===== CARD hover ===== */
    .card {
      transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }
    .card:hover {
      transform: translateY(-6px);
      border-color: rgba(142, 217, 214, 0.1);
      background: linear-gradient(145deg, rgba(255,255,255,0.2), rgba(0,0,0,0.2)); backdrop-filter: blur(6px);

      box-shadow: 0 12px 28px rgba(0,255,153,0.05);
    }

    /* ===== سه کارت ستونی ===== */
    .triple-cards {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      margin-top: 40px;
    }

    .triple-card {
      background: linear-gradient(180deg, var(--glass), var(--glass-2));
      border: 1px solid rgba(255,255,255,0.02);
      border-radius: 12px;
      padding: 18px;
      text-align: center;
      transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .triple-card:hover {
      transform: translateY(-6px);
      border-color: rgba(142,217,214,0.1);
      box-shadow: 0 12px 28px rgba(0,255,153,0.05);
      background: linear-gradient(145deg, rgba(255,255,255,0.2), rgba(0,0,0,0.2)); backdrop-filter: blur(6px);



    }

    .triple-card img {
      width: 100px;
      height: 100px;
      border-radius: 12px;
      object-fit: cover;
      margin-bottom: 10px;
    }

    @media (max-width: 920px) {
      .triple-cards { grid-template-columns: 1fr; }
      .triple-card img { width: 80px; height: 80px; }
    }
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
          <h1>بوت‌کمپ: انجام و پیشگیری از SQL Injection</h1>
          <div class="lead">دانشگاه صنعتی ارومیه — دانشکدهٔ فناوری های صنعتی</div>
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
          <p class="muted">این بوت‌کمپ به روش‌های انجام و پیشگیری از SQL Injection می‌پردازد. تمامی تمرین‌ها ایزوله و امن اجرا می‌شوند.</p>
        </div>

        <div class="card" style="margin-top:12px">
          <div class="code-header">
            <div>
              <h2>نمونهٔ امن — PHP (PDO)</h2>
              <div class="muted">نمونهٔ نمایشی؛ همیشه از prepared statements استفاده کنید.</div>
            </div>
            <div class="badge">نمونهٔ امن</div>
          </div>

          <pre class="code" id="codeBlock">
// PHP (PDO) — نمونهٔ امن (نمایشی)
&lt;?php
// $pdo = new PDO('mysql:host=localhost;dbname=test','user','pass');
// $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u");
// $stmt->execute([':u' => $inputUsername]);
// $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?&gt;
          </pre>

          <div style="margin-top:10px;display:flex;gap:8px;justify-content:flex-end">
            <button class="btn" id="copyCode">کپی کد</button>
            <button class="btn" id="showWarning">هشدار ایمنی</button>
          </div>
        </div>

        <div class="card" style="margin-top:12px">
          <h2>روش‌های جلوگیری </h2>
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
        <h2>دموی شبیه‌سازی</h2>
        <p class="muted">برای امنیت، این صفحه هیچ‌عملی به سرور ارسال نمی‌کند — خروجی به صورت کلاینت-ساید شبیه‌سازی می‌شود.</p>

        <form id="demoForm" novalidate>
          <div style="margin-top:12px">
            <label class="muted">Username (ورودی کاربر):</label>
            <input id="username" name="username" type="text" placeholder="مثال: ali یا elahe" value=""/>
          </div>

          <div style="margin-top:10px">
            <button class="btn" type="submit" style="width:100%">ارسال و نمایش (ایمن)</button>
          </div>
        </form>

        <div style="margin-top:12px">
          <div class="muted">خروجی:</div>
          <div class="outbox" id="outbox">هنوز چیزی اجرا نشده.</div>
        </div>

        <hr style="border:none;border-top:1px dashed rgba(255,255,255,0.03);margin:12px 0">

        <h3 style="margin:6px 0">نکات اجرای امن آزمایشگاه</h3>
        <div class="muted" style="font-size:13px">
          ۱) همه تمرین‌ها در VM یا Docker اجرا شوند.<br>
          ۲) از دیتابیس واقعی استفاده نکنید.<br>
          ۳) قبل و بعد از هر جلسه snapshot بگیرید.<br>
          ۴) اجرای payload در شبکهٔ عمومی ممنوع.
        </div>
      </aside>
    </main>

    <!-- سه کارت ستونی جدید -->
    <section class="triple-cards">
      <div class="triple-card">
        <img src="qr1.png" alt="QR سوال ۱">
        <h3>سوال اول بوت‌کمپ</h3>
        <p class="muted">توضیح مختصر درباره سوال اول. این سوال مفاهیم پایه SQL Injection را بررسی می‌کند.</p>
      </div>
      <div class="triple-card">
        <img src="qr2.png" alt="QR سوال ۲">
        <h3>سوال دوم بوت‌کمپ</h3>
        <p class="muted">در این سوال، شرکت‌کننده با روش‌های پیشگیری از حملات آشنا می‌شود.</p>
      </div>
      <div class="triple-card">
        <img src="qr3.png" alt="QR سوال ۳">
        <h3>سوال سوم بوت‌کمپ</h3>
        <p class="muted">این سوال شامل ترکیب SQL Injection با تست امنیتی واقعی است.</p>
      </div>
    </section>

    <footer>
      دانشگاه صنعتی ارومیه — بوت‌کمپ SQL Injection | منابع: OWASP, PHP.net, MDN
    </footer>
  </div>

  <script>
    document.getElementById('copyCode').addEventListener('click', async () => {
      const code = document.getElementById('codeBlock').innerText;
      try { await navigator.clipboard.writeText(code); alert('کد در کلیپ‌بورد کپی شد.'); }
      catch (e) { prompt('این کد را کپی کنید:', code); }
    });

    document.getElementById('showWarning').addEventListener('click', () => {
      alert('هشدار: هرگز کدهای آسیب‌پذیر را در محیط عمومی اجرا نکنید.');
    });

    (function(){
      const form = document.getElementById('demoForm');
      const usernameInput = document.getElementById('username');
      const outbox = document.getElementById('outbox');
      const validRe = /^[\p{Script=Arabic}\p{Script=Latin}\d_\-\s]{1,40}$/u;
      function escapeForText(s){ const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

      form.addEventListener('submit', function(ev){
        ev.preventDefault();
        const v = (usernameInput.value || '').trim();
        if(v === ''){ outbox.innerText = 'لطفاً یک نام کاربری وارد کنید.'; return; }

        let ok = false;
        try { ok = validRe.test(v); } catch(e) { ok = /^[A-Za-z\u0600-\u06FF0-9_\-\s]{1,40}$/.test(v); }

        if(!ok){ outbox.innerText = 'ورودی نامعتبر — کاراکترهای غیرمجاز.'; }
        else {
          const safe = escapeForText(v);
          outbox.innerHTML = `یافت شد: user = &quot;${safe}&quot; (خروجی شبیه‌سازی شده — امن)`;
        }

        if(outbox && outbox.innerText.trim() !== 'هنوز چیزی اجرا نشده.') {
          outbox.scrollIntoView({behavior:'smooth'});
        }
      });
    })();

    document.getElementById('downloadSlides').addEventListener('click', (e) => {
      e.preventDefault();
      alert('لینک اسلاید هنوز تنظیم نشده.');
    });
    document.getElementById('repoLink').addEventListener('click', (e) => {
      e.preventDefault();
      alert('لینک ریپوزیتوری هنوز تنظیم نشده.');
    });
  </script>
</body>
</html>
