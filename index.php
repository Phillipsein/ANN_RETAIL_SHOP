<?php
// index.php — ANN RETAIL SHOP (single file, mobile-first, polished UI)

// ------------ SETTINGS ------------ //
$SITE_NAME   = 'ANN RETAIL SHOP';
$DOMAIN_URL  = 'https://annretailshop.com/';
$TO_EMAILS   = ['phillipsein6@gmail.com', 'nabukeeraannet2@gmail.com'];
$FROM_EMAIL  = 'sales@annretailshop.philltechs.com';
$SMTP_HOST   = 'smtp.titan.email';
$SMTP_USER   = 'sales@annretailshop.philltechs.com';
$SMTP_PASS   = 'lilAnn@78930_salesEmail'; // Tip: move to env var for security
$SMTP_PORT   = 587;

// ------------ CONTACT FORM HANDLER ------------ //
session_start();
$status = null; $status_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Honeypot + simple rate limit
  if (!empty($_POST['company'])) { $status='err'; $status_msg='Bad request.'; }
  elseif (isset($_SESSION['last_submit']) && time() - ($_SESSION['last_submit'] ?? 0) < 12) { $status='err'; $status_msg='Please wait a few seconds and try again.'; }
  else {
    $_SESSION['last_submit'] = time();
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
      $status='err'; $status_msg='Fill all required fields correctly.';
    } else {
      $subject = "New inquiry from $SITE_NAME website";
      $body    = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message\n\n--\n$SITE_NAME";
      $sent = false;

      // Try PHPMailer (Composer) if available
      $autoload = __DIR__.'/vendor/autoload.php';
      if (file_exists($autoload)) {
        require $autoload;
        try {
          $mail = new PHPMailer\PHPMailer\PHPMailer(true);
          $mail->isSMTP();
          $mail->Host       = $SMTP_HOST;
          $mail->SMTPAuth   = true;
          $mail->Username   = $SMTP_USER;
          $mail->Password   = $SMTP_PASS;
          $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
          $mail->Port       = $SMTP_PORT;
          $mail->CharSet    = 'UTF-8';
          $mail->setFrom($FROM_EMAIL, $SITE_NAME);
          foreach ($TO_EMAILS as $rcpt) { $mail->addAddress($rcpt); }
          $mail->addReplyTo($email, $name);
          $mail->Subject = $subject;
          $mail->Body    = $body;
          $sent = $mail->send();
        } catch (Throwable $e) { $sent = false; }
      }

      // Fallback to mail() if PHPMailer not installed
      if (!$sent) {
        $headers  = 'From: ' . $SITE_NAME . ' <' . $FROM_EMAIL . ">\r\n";
        $headers .= 'Reply-To: ' . $email . "\r\n";
        $headers .= 'Content-Type: text/plain; charset=UTF-8';
        $okAll = true; foreach ($TO_EMAILS as $rcpt) { $okAll = mail($rcpt, $subject, $body, $headers) && $okAll; }
        $sent = $okAll;
      }

      if ($sent) { $status='ok'; $status_msg='Thanks! Your message was sent.'; }
      else { $status='err'; $status_msg='Sorry, message failed. Please WhatsApp or call us.'; }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
  <title>ANN RETAIL SHOP | Everyday Essentials at Great Prices</title>
  <meta name="description" content="Your neighbourhood store for groceries, home & cleaning, and personal care. Order fast on WhatsApp or call."/>
  <meta name="theme-color" content="#f97316"/>

  <!-- Social -->
  <meta property="og:title" content="ANN RETAIL SHOP"/>
  <meta property="og:description" content="Everyday essentials at great prices. Call, WhatsApp, or visit us today."/>
  <meta property="og:type" content="website"/>
  <meta property="og:url" content="<?= htmlspecialchars($DOMAIN_URL) ?>"/>
  <meta property="og:image" content="https://images.unsplash.com/photo-1586201375761-83865001e31b?q=80&w=1600&auto=format&fit=crop"/>
  <meta name="twitter:card" content="summary_large_image"/>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <style>
    :root{
      --brand:#f97316; --brand-600:#ea580c;
      --accent:#10b981; --accent-600:#059669;
      --ink:#0f172a; --muted:#475569; --bg:#fff; --card:#ffffff;
      --ring:rgba(249,115,22,.25)
    }
    *{box-sizing:border-box}
    html,body{margin:0;padding:0;font-family:'Inter',system-ui,Segoe UI,Roboto,Arial,sans-serif;color:var(--ink);background:#fff}
    a{color:inherit;text-decoration:none}
    img{max-width:100%;display:block}

    /* Container with safe mobile margins */
    .container{
      max-width:1200px;margin:0 auto;
      padding:0 20px;
      padding-left:calc(20px + env(safe-area-inset-left));
      padding-right:calc(20px + env(safe-area-inset-right));
    }

    /* Header */
    header{position:sticky;top:0;z-index:80;background:#ffffffd8;backdrop-filter:saturate(180%) blur(10px);border-bottom:1px solid #eef2f7}
    .nav{display:flex;align-items:center;justify-content:space-between;padding:12px 0}
    .brand{display:flex;gap:12px;align-items:center}
    .logo{height:42px;width:42px;border-radius:12px;box-shadow:0 6px 16px rgba(2,132,199,.2)}
    .name{font-weight:800;letter-spacing:.2px}
    .open-pill{display:inline-flex;align-items:center;gap:6px;background:#d1fae5;color:#065f46;padding:6px 10px;border-radius:999px;font-size:12px;border:1px solid #a7f3d0}
    .desktop-actions{display:none}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid #e2e8f0;background:#fff;color:var(--ink);padding:10px 14px;border-radius:12px;font-weight:700;transition:.2s}
    .btn:hover{transform:translateY(-1px);box-shadow:0 10px 28px rgba(0,0,0,.08)}
    .btn.primary{background:var(--brand);border-color:var(--brand);color:#fff}
    .btn.primary:hover{background:var(--brand-600);border-color:var(--brand-600)}

    /* Full-screen mobile menu */
    .hamburger{appearance:none;border:1px solid #e2e8f0;background:#fff;color:var(--ink);height:42px;width:46px;border-radius:10px;display:flex;align-items:center;justify-content:center}
    .bar{width:18px;height:2px;background:var(--ink);position:relative}
    .bar:before,.bar:after{content:"";position:absolute;left:0;width:18px;height:2px;background:var(--ink)}
    .bar:before{top:-6px}.bar:after{top:6px}
    .overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);opacity:0;pointer-events:none;transition:.25s;z-index:85}
    .overlay.show{opacity:1;pointer-events:auto}
    .sheet{position:fixed;inset:0;background:#ffffff;display:flex;flex-direction:column;transform:translateY(100%);transition:.35s ease;z-index:90}
    .sheet.show{transform:translateY(0)}
    .sheet .menu{display:grid;gap:12px;padding:18px}

    /* Hero */
    .hero{position:relative;background:linear-gradient(180deg,#fff 0,#fff7ed 100%);padding:22px 0 6px;overflow:hidden}
    .hero .blob{position:absolute;inset:-30% -20% auto -10%;height:120%;background:
      radial-gradient(600px 300px at 20% 20%, rgba(249,115,22,.25), transparent 60%),
      radial-gradient(500px 250px at 80% 10%, rgba(16,185,129,.22), transparent 60%);
      filter:blur(18px);z-index:0}
    .hero .inner{position:relative;z-index:1;display:grid;grid-template-columns:1fr;gap:18px;align-items:center}
    .title{font-family:'Playfair Display',serif;font-size:36px;line-height:1.1;margin:0}
    .lead{color:var(--muted);font-size:15px;margin:6px 0 0}
    .hero-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
    .hero-card{border-radius:18px;overflow:hidden;box-shadow:0 10px 30px rgba(2,132,199,.12)}
    .hero-card img{width:100%;height:260px;object-fit:cover}
    @media(min-width:900px){.hero .inner{grid-template-columns:1.05fr .95fr}.title{font-size:56px}.hero-card img{height:420px}}

    /* Sections */
    section{padding:26px 0}
    .section-title{font-size:22px;margin:0 0 8px}
    .section-desc{color:var(--muted);margin:0 0 16px}

    /* Chips */
    .chips{display:flex;gap:8px;flex-wrap:wrap}
    .chip{display:inline-flex;align-items:center;gap:8px;background:#fff;border:1px solid #e5e7eb;border-radius:999px;padding:8px 12px;font-weight:700}

    /* Image grid */
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    @media(min-width:860px){.grid{grid-template-columns:repeat(4,1fr)}}
    .card{position:relative;overflow:hidden;border-radius:16px;border:1px solid #eef2f7;background:var(--card);box-shadow:0 8px 20px rgba(15,23,42,.06)}
    .card img{height:170px;object-fit:cover}
    .card .content{position:absolute;left:0;right:0;bottom:0;padding:10px;background:linear-gradient(180deg,rgba(0,0,0,0) 0, rgba(0,0,0,.7) 100%);color:#fff}

    /* Carousel */
    .track{display:flex;gap:12px;overflow:auto;scroll-snap-type:x mandatory;padding-bottom:6px}
    .track .item{min-width:230px;scroll-snap-align:start}

    /* CTA (visible, not white) */
    .cta{background:linear-gradient(90deg,var(--brand),var(--accent));color:#fff;border-radius:16px;padding:16px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;margin:6px 0}
    .cta strong{font-size:18px}

    /* Contact */
    .contact{display:grid;grid-template-columns:1fr;gap:16px}
    @media(min-width:960px){.contact{grid-template-columns:1fr 1fr}}
    form{background:#fff;padding:16px;border:1px solid #eef2f7;border-radius:14px;box-shadow:0 8px 18px rgba(2,132,199,.06)}
    .field{display:flex;flex-direction:column;gap:6px;margin-bottom:10px}
    .field input,.field textarea{padding:12px 14px;border-radius:12px;border:1px solid #e2e8f0;outline:none;font-size:16px;background:#fff;color:var(--ink)}
    .field input:focus,.field textarea:focus{border-color:var(--brand);box-shadow:0 0 0 4px var(--ring)}
    .hint{font-size:12px;color:var(--muted)}
    iframe.map{width:100%;height:100%;min-height:320px;border:0;border-radius:14px}

    /* Sticky bottom quick actions */
    .quickbar{position:sticky;bottom:0;z-index:70;background:#ffffff;border-top:1px solid #e5e7eb;padding:10px 0}
    .quickbar .container{display:flex;gap:10px}
    .quickbar .btn{flex:1}

    /* Footer */
    footer{padding:18px 0;border-top:1px solid #eef2f7;color:var(--muted);font-size:14px}

    /* Toast */
    .toast{position:fixed;left:50%;transform:translateX(-50%);bottom:18px;background:#111827;color:#fff;padding:12px 14px;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,.18);display:none;max-width:92%;text-align:center}
    .toast.show{display:block}
    .toast.ok{background:#065f46}
    .toast.err{background:#7f1d1d}

    @media(min-width:960px){.desktop-actions{display:flex;gap:10px}.hamburger{display:none}}
    @media (prefers-reduced-motion: reduce){*{animation:none!important;transition:none!important}}
  </style>
</head>
<body>
  <header>
    <div class="container nav">
      <div class="brand">
        <img class="logo" alt="ANN Retail Shop logo" src="https://images.unsplash.com/photo-1526403226243-67b9d8bd22d9?q=80&w=256&auto=format&fit=crop"/>
        <div>
          <div class="name">ANN RETAIL SHOP</div>
          <div class="open-pill">Open • 7:00am – 9:30pm</div>
        </div>
      </div>
      <div class="desktop-actions">
        <a class="btn" href="#catalog">Categories</a>
        <a class="btn" href="#order">Order</a>
        <a class="btn" href="#contact">Contact</a>
        <a class="btn primary" href="https://wa.me/256746825914" target="_blank" rel="noopener">WhatsApp</a>
      </div>
      <button class="hamburger" id="menuBtn" aria-label="Menu"><span class="bar"></span></button>
    </div>
    <div class="overlay" id="overlay"></div>
    <div class="sheet" id="sheet">
      <div class="container" style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between">
        <strong>Menu</strong>
        <a class="btn" href="#" id="closeSheet">Close</a>
      </div>
      <div class="menu container">
        <a class="btn" href="#catalog">Categories</a>
        <a class="btn" href="#order">Order</a>
        <a class="btn" href="#contact">Contact</a>
        <a class="btn primary" href="https://wa.me/256746825914" target="_blank" rel="noopener">WhatsApp</a>
        <a class="btn" href="tel:+256746825914">Call 0746 825 914</a>
      </div>
    </div>
  </header>

  <main>
    <!-- HERO -->
    <section class="hero">
      <div class="blob"></div>
      <div class="container inner">
        <div>
          <h1 class="title">Everything for home <span style="color:var(--brand)">at friendly prices</span></h1>
          <p class="lead">Groceries • Home & cleaning • Personal care. Order on WhatsApp — fast and simple.</p>
          <div class="hero-actions">
            <a class="btn primary" href="#order">Order Now</a>
            <a class="btn" href="https://wa.me/256746825914" target="_blank" rel="noopener">WhatsApp Us</a>
          </div>
        </div>
        <div class="hero-card">
          <img alt="Neat grocery aisle at ANN RETAIL SHOP" src="https://images.unsplash.com/photo-1586201375761-83865001e31b?q=80&w=1400&auto=format&fit=crop"/>
        </div>
      </div>
    </section>

    <!-- CATEGORIES -->
    <section id="catalog">
      <div class="container">
        <h3 class="section-title">Popular categories</h3>
        <div class="grid">
          <a class="card" href="#order"><img alt="Groceries" src="https://images.unsplash.com/photo-1526318472351-c75fcf070305?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Groceries</strong><div>Rice • Flour • Beans</div></div></a>
          <a class="card" href="#order"><img alt="Beverages" src="https://images.unsplash.com/photo-1559718062-361155fad299?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Beverages</strong><div>Water • Soda • Tea</div></div></a>
          <a class="card" href="#order"><img alt="Personal care" src="https://images.unsplash.com/photo-1556228453-efd1e7f49b8f?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Personal Care</strong><div>Soap • Toothpaste</div></div></a>
          <a class="card" href="#order"><img alt="Cleaning" src="https://images.unsplash.com/photo-1581578731548-c64695cc6952?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Home & Cleaning</strong><div>Detergents • Tissues</div></div></a>
        </div>
      </div>
    </section>

    <!-- BEST SELLERS -->
    <section>
      <div class="container">
        <h3 class="section-title">This week’s best sellers</h3>
        <div class="track">
          <div class="item card"><img alt="Cooking oil" src="https://images.unsplash.com/photo-1517260739337-6799d878efca?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Cooking Oil 3L</strong><div>Promo • limited</div></div></div>
          <div class="item card"><img alt="Sugar" src="https://images.unsplash.com/photo-1615485925600-27149fc4d19b?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Sugar 1kg</strong><div>Top demand</div></div></div>
          <div class="item card"><img alt="Beans" src="https://images.unsplash.com/photo-1607301405394-4a578b865c98?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Beans 1kg</strong><div>Fresh stock</div></div></div>
          <div class="item card"><img alt="Rice" src="https://images.unsplash.com/photo-1565688534245-05d6b5be1847?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Rice 5kg</strong><div>Customer fav</div></div></div>
          <div class="item card"><img alt="Toothpaste" src="https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Toothpaste</strong><div>Bundle deal</div></div></div>
        </div>
      </div>
    </section>

    <!-- CTA (orange→mint, visible) -->
    <section>
      <div class="container">
        <div class="cta">
          <div>
            <strong>Need something now?</strong>
            <div>WhatsApp or call and we’ll prepare your order.</div>
          </div>
          <div class="chips">
            <a class="btn" href="https://wa.me/256746825914" target="_blank" rel="noopener">WhatsApp 0746 825 914</a>
            <a class="btn" href="tel:+256746825914">Call 0746 825 914</a>
          </div>
        </div>
      </div>
    </section>

    <!-- ORDER & CONTACT -->
    <section id="order">
      <div class="container">
        <h3 class="section-title">Order & Contact</h3>
        <div class="contact" id="contact">
          <form method="post" action="#contact" id="contactForm" novalidate>
            <div class="chips" style="margin-bottom:8px">
              <span class="chip">Call: 0746 825 914</span>
              <span class="chip">Call: 0781 988 570</span>
              <span class="chip">Email: phillipsein6@gmail.com</span>
              <span class="chip">Email: nabukeeraannet2@gmail.com</span>
            </div>
            <div class="field"><label for="name">Your name</label><input id="name" name="name" type="text" required placeholder="Jane Doe"/></div>
            <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" required placeholder="you@example.com"/></div>
            <div class="field"><label for="phone">Phone / WhatsApp</label><input id="phone" name="phone" type="tel" placeholder="+256…"/></div>
            <div class="field"><label for="message">How can we help?</label><textarea id="message" name="message" rows="5" required placeholder="Tell us what you need…"></textarea></div>
            <input type="text" name="company" style="display:none" tabindex="-1" autocomplete="off">
            <button class="btn primary" type="submit">Send Message</button>
            <div class="hint">We’ll never share your details. You’ll also get a copy of your message by email.</div>
          </form>
          <iframe class="map" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
            src="https://www.google.com/maps?q=0.288835138082504,32.6322174072266&z=15&output=embed"></iframe>
        </div>
      </div>
    </section>
  </main>

  <!-- Sticky bottom quick actions -->
  <div class="quickbar">
    <div class="container">
      <a class="btn" href="tel:+256746825914">Call</a>
      <a class="btn" href="https://wa.me/256746825914" target="_blank" rel="noopener">WhatsApp</a>
      <a class="btn primary" href="#order">Order</a>
    </div>
  </div>

  <footer>
    <div class="container" style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap">
      <div>© <span id="year"></span> ANN RETAIL SHOP • All rights reserved.</div>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a class="btn" href="#catalog">Categories</a>
        <a class="btn" href="#order">Order</a>
        <a class="btn" href="#contact">Contact</a>
      </div>
    </div>
  </footer>

  <!-- JSON-LD -->
  <script type="application/ld+json">{"@context":"https://schema.org","@type":"Store","name":"ANN RETAIL SHOP","url":"<?= htmlspecialchars($DOMAIN_URL) ?>","image":"https://images.unsplash.com/photo-1585386959984-a41552231658?q=80&w=1200&auto=format&fit=crop","telephone":"+256746825914","email":"sales@annretailshop.philltechs.com","address":{"@type":"PostalAddress","addressCountry":"UG"},"openingHours":"Mo-Su 07:00-21:30","sameAs":["https://wa.me/256746825914","https://wa.me/256781988570"]}</script>
  <div id="toast" class="toast"></div>

  <script>
    // Year
    document.getElementById('year').textContent = new Date().getFullYear();

    // Full-screen mobile menu
    const menuBtn = document.getElementById('menuBtn');
    const overlay = document.getElementById('overlay');
    const sheet = document.getElementById('sheet');
    const closeSheetBtn = document.getElementById('closeSheet');
    const toggleMenu = ()=>{ overlay.classList.toggle('show'); sheet.classList.toggle('show'); };
    menuBtn.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);
    closeSheetBtn.addEventListener('click', (e)=>{ e.preventDefault(); toggleMenu(); });

    // Toast after form submit
    const toast = document.getElementById('toast');
    <?php if ($status !== null): ?>
      toast.textContent = <?= json_encode($status_msg) ?>;
      toast.className = 'toast <?= $status === 'ok' ? 'ok' : 'err' ?> show';
      setTimeout(()=> toast.classList.remove('show'), <?= $status === 'ok' ? 5000 : 7000 ?>);
      window.location.hash = '#contact';
    <?php endif; ?>
  </script>
</body>
</html>
