<?php
// index.php — Single-file site for ANN RETAIL SHOP (mobile-first, orange + mint)
// Handles: UI + PHPMailer (Titan SMTP) + fallback mail() if PHPMailer missing.

// ------------ SETTINGS ------------ //
$SITE_NAME   = 'ANN RETAIL SHOP';
$DOMAIN_URL  = 'https://annretailshop.com/';
$TO_EMAILS   = ['phillipsein6@gmail.com', 'nabukeeraannet2@gmail.com'];
$FROM_EMAIL  = 'sales@annretailshop.philltechs.com';
$SMTP_HOST   = 'smtp.titan.email';
$SMTP_USER   = 'sales@annretailshop.philltechs.com';
$SMTP_PASS   = 'lilAnn@78930_salesEmail';
$SMTP_PORT   = 587;

// ------------ CONTACT FORM HANDLER ------------ //
session_start();
$status = null; $status_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Anti-spam
  if (!empty($_POST['company'])) {
    $status = 'err';
    $status_msg = 'Bad request.';
  } elseif (isset($_SESSION['last_submit']) && time() - ($_SESSION['last_submit'] ?? 0) < 12) {
    $status = 'err';
    $status_msg = 'Too many requests. Please wait a few seconds and try again.';
  } else {
    $_SESSION['last_submit'] = time();

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
      $status = 'err';
      $status_msg = 'Please fill the required fields correctly.';
    } else {
      $subject = "New inquiry from $SITE_NAME website";
      $body    = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message\n\n--\n$SITE_NAME";

      // Try PHPMailer (Composer) first
      $sent = false; $mailerError = '';
      $autoload = __DIR__ . '/vendor/autoload.php';
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
        } catch (Throwable $e) {
          $mailerError = $e->getMessage();
          $sent = false;
        }
      }

      // Fallback to native mail() (less reliable) if PHPMailer not available or failed
      if (!$sent) {
        $headers  = 'From: ' . $SITE_NAME . ' <' . $FROM_EMAIL . ">\r\n";
        $headers .= 'Reply-To: ' . $email . "\r\n";
        $headers .= 'Content-Type: text/plain; charset=UTF-8';
        $okAll = true;
        foreach ($TO_EMAILS as $rcpt) {
          $okAll = mail($rcpt, $subject, $body, $headers) && $okAll;
        }
        $sent = $okAll;
      }

      if ($sent) {
        $status = 'ok';
        $status_msg = 'Thanks! Your message was sent.';
      } else {
        $status = 'err';
        $status_msg = 'Sorry, message failed. Please WhatsApp or call us.';
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>ANN RETAIL SHOP | Everyday Essentials at Great Prices</title>
  <meta name="description" content="ANN RETAIL SHOP — your neighbourhood store for groceries, household items, personal care, and more. Order, call, or visit us today!" />
  <meta name="theme-color" content="#f97316" />

  <!-- SEO / Social -->
  <meta property="og:title" content="ANN RETAIL SHOP" />
  <meta property="og:description" content="Everyday essentials at great prices. Call, WhatsApp, or visit us today." />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="<?= htmlspecialchars($DOMAIN_URL) ?>" />
  <meta property="og:image" content="https://images.unsplash.com/photo-1585386959984-a41552231658?q=80&w=1600&auto=format&fit=crop" />
  <meta name="twitter:card" content="summary_large_image" />

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --brand:#f97316; --brand-600:#ea580c;
      --accent:#10b981; --accent-600:#059669;
      --ink:#0f172a; --muted:#475569; --bg:#fffaf3; --card:#ffffff;
      --ring:rgba(249,115,22,.25)
    }
    *{box-sizing:border-box}
    html,body{margin:0;padding:0;font-family:'Inter',system-ui,Segoe UI,Roboto,Arial,sans-serif;color:var(--ink);background:linear-gradient(180deg,#fafafa 0%, #ffffff 40%, #fff7ed 100%)}
    a{color:inherit;text-decoration:none}
    img{max-width:100%;display:block}

    /* Spacing container with safe area on phones */
    .container{max-width:1200px;margin:0 auto;padding:0 16px; padding-left:calc(16px + env(safe-area-inset-left)); padding-right:calc(16px + env(safe-area-inset-right));}

    /* Header */
    header{position:sticky;top:0;z-index:70;background:#ffffffee;backdrop-filter:saturate(180%) blur(10px);border-bottom:1px solid #eef2f7}
    .nav{display:flex;align-items:center;justify-content:space-between;padding:10px 0}
    .brand{display:flex;gap:10px;align-items:center;min-width:0}
    .brand img.logo{height:40px;width:40px;border-radius:10px;box-shadow:0 6px 16px rgba(2,132,199,.2)}
    .brand .name{font-weight:800;letter-spacing:.2px;white-space:nowrap}
    .pill{display:inline-flex;align-items:center;gap:6px;background:#d1fae5;color:#065f46;padding:6px 10px;border-radius:999px;font-size:12px;border:1px solid #a7f3d0}

    .desktop-actions{display:none}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid #e2e8f0;background:#fff;color:var(--ink);padding:10px 14px;border-radius:12px;font-weight:700;transition:.2s;box-shadow:0 1px 0 rgba(15,23,42,.02)}
    .btn:hover{transform:translateY(-1px);box-shadow:0 10px 28px rgba(0,0,0,.08)}
    .btn.primary{background:var(--brand);border-color:var(--brand);color:#fff}
    .btn.primary:hover{background:var(--brand-600);border-color:var(--brand-600)}

    /* Hamburger */
    .menu-toggle{appearance:none;border:1px solid #e2e8f0;background:#fff;color:var(--ink);height:40px;width:44px;border-radius:10px;display:flex;align-items:center;justify-content:center}
    .bar{width:18px;height:2px;background:var(--ink);position:relative}
    .bar:before,.bar:after{content:"";position:absolute;left:0;width:18px;height:2px;background:var(--ink)}
    .bar:before{top:-6px}.bar:after{top:6px}

    .mobile-sheet{position:fixed;inset:auto 0 0 0;background:#ffffff;border-top:1px solid #eef2f7;box-shadow:0 -10px 24px rgba(0,0,0,.08);transform:translateY(110%);transition:.28s ease;z-index:65}
    .mobile-sheet.open{transform:translateY(0)}
    .mobile-sheet .inner{padding:12px 0}
    .mobile-sheet .row{padding:0 16px}
    .mobile-sheet .btn{flex:1;margin-bottom:8px}

    /* Hero */
    .hero{position:relative;overflow:hidden}
    .hero:before{content:"";position:absolute;inset:-20% -10% auto -10%;height:120%;background:radial-gradient(600px 300px at 20% 20%, rgba(249,115,22,.22), transparent 60%), radial-gradient(500px 250px at 80% 10%, rgba(16,185,129,.2), transparent 60%);filter:blur(12px);z-index:0}
    .hero .inner{position:relative;z-index:1;display:grid;grid-template-columns:1fr;gap:16px;align-items:center;padding:22px 0}
    .hero h1{font-family:'Playfair Display',serif;font-size:36px;line-height:1.1;margin:0 0 8px}
    .hero p{color:var(--muted);font-size:15px;margin:0 0 12px}
    .badge{display:inline-flex;gap:8px;align-items:center;background:#fffbeb;border:1px solid #fed7aa;color:#9a3412;padding:6px 10px;border-radius:999px;font-weight:700;font-size:12px}

    /* Visual hero block (big image) */
    .hero-card{position:relative;border-radius:18px;overflow:hidden;box-shadow:0 10px 30px rgba(2,132,199,.12)}
    .hero-card img{width:100%;height:240px;object-fit:cover}
    @media(min-width:860px){.hero .inner{grid-template-columns:1.1fr .9fr}.hero-card img{height:340px}}

    /* Sections */
    section{padding:26px 0}
    main section:nth-of-type(odd){background:linear-gradient(180deg,#ffffff 0%, #fefce8 100%)}
    .section-title{font-size:22px;margin:0 0 6px}
    .section-desc{color:var(--muted);margin:0 0 16px}

    /* Feature chips */
    .chips{display:flex;gap:8px;flex-wrap:wrap}
    .chip{display:inline-flex;align-items:center;gap:8px;background:#fff;border:1px solid #e5e7eb;border-radius:999px;padding:8px 12px;font-weight:700}
    .chip svg{width:18px;height:18px}

    /* Image grid */
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    @media(min-width:860px){.grid{grid-template-columns:repeat(4,1fr)}}
    .card{position:relative;overflow:hidden;border-radius:16px;border:1px solid #eef2f7;background:var(--card);box-shadow:0 8px 20px rgba(15,23,42,.06)}
    .card img{height:160px;object-fit:cover}
    .card .content{position:absolute;left:0;right:0;bottom:0;padding:10px;background:linear-gradient(180deg,rgba(0,0,0,0) 0, rgba(0,0,0,.7) 100%);color:#fff}

    /* Carousel */
    .carousel{position:relative}
    .track{display:flex;gap:12px;overflow:auto;scroll-snap-type:x mandatory;padding-bottom:6px}
    .track .item{min-width:220px;scroll-snap-align:start}
    .icon-btn{border:1px solid #e2e8f0;background:#fff;color:var(--ink);padding:8px 10px;border-radius:10px;box-shadow:0 2px 10px rgba(15,23,42,.08)}

    /* CTA band — ensure NOT white */
    .cta{background:linear-gradient(90deg,var(--brand),var(--accent));color:#fff;border-radius:16px;padding:16px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px}
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

    /* Sticky bottom quickbar */
    .quickbar{position:sticky;bottom:0;z-index:66;background:#ffffff;border-top:1px solid #e5e7eb;padding:8px 0}
    .quickbar .container{display:flex;gap:8px}
    .quickbar .btn{flex:1}

    /* Footer */
    footer{padding:16px 0;border-top:1px solid #eef2f7;color:var(--muted);font-size:14px}

    /* Toast */
    .toast{position:fixed;left:50%;transform:translateX(-50%);bottom:18px;background:#111827;color:#fff;padding:12px 14px;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,.18);display:none;max-width:92%;text-align:center}
    .toast.show{display:block}
    .toast.ok{background:#065f46}
    .toast.err{background:#7f1d1d}

    /* Desktop tweaks */
    @media(min-width:960px){
      .desktop-actions{display:flex;gap:10px}
      .menu-toggle{display:none}
      .hero h1{font-size:48px}
      .hero-card img{height:380px}
    }
  </style>
</head>
<body>
  <header>
    <div class="container nav">
      <div class="brand">
        <img class="logo" alt="ANN Retail Shop logo" src="https://images.unsplash.com/photo-1526403226243-67b9d8bd22d9?q=80&w=256&auto=format&fit=crop"/>
        <div>
          <div class="name">ANN RETAIL SHOP</div>
          <div class="pill" title="Open 7 days a week">Open • 7:00am – 9:30pm</div>
        </div>
      </div>
      <div class="desktop-actions">
        <a class="btn" href="#catalog">Categories</a>
        <a class="btn" href="#order">Order</a>
        <a class="btn" href="#contact">Contact</a>
        <a class="btn primary" href="https://wa.me/256746825914" target="_blank" rel="noopener">WhatsApp</a>
      </div>
      <button class="menu-toggle" id="menuToggle" aria-label="Menu"><span class="bar"></span></button>
    </div>
    <!-- Mobile bottom sheet menu -->
    <div class="mobile-sheet" id="mobileSheet">
      <div class="inner container">
        <div class="row" style="display:flex;gap:8px;flex-wrap:wrap">
          <a class="btn" href="#catalog" onclick="closeSheet()">Categories</a>
          <a class="btn" href="#order" onclick="closeSheet()">Order</a>
          <a class="btn" href="#contact" onclick="closeSheet()">Contact</a>
          <a class="btn primary" href="https://wa.me/256746825914" target="_blank" rel="noopener" onclick="closeSheet()">WhatsApp</a>
        </div>
      </div>
    </div>
  </header>

  <main>
    <!-- Hero (visual first, fewer words) -->
    <section class="hero">
      <div class="container inner">
        <div>
          <span class="badge">Fresh • Fast • Friendly</span>
          <h1>Everything for home —
            <span style="color:var(--brand)">at fair prices</span></h1>
          <div class="chips" style="margin:10px 0 12px">
            <span class="chip">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7H4l2 12h12l2-12Z"/><path d="M10 11v4M14 11v4"/></svg>
              Groceries
            </span>
            <span class="chip">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 21h8"/><rect x="6" y="3" width="12" height="14" rx="2"/></svg>
              Home & Cleaning
            </span>
            <span class="chip">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 12.39A2 2 0 0 0 9.62 15H19a2 2 0 0 0 2-1.65L23 6H6"/></svg>
              Quick Delivery
            </span>
          </div>
          <div class="chips">
            <a class="btn primary" href="#order">Order Now</a>
            <a class="btn" href="tel:+256746825914">Call 0746 825 914</a>
          </div>
        </div>
        <div class="hero-card">
          <img alt="Neat grocery aisle at ANN RETAIL SHOP" src="https://images.unsplash.com/photo-1586201375761-83865001e31b?q=80&w=1400&auto=format&fit=crop"/>
        </div>
      </div>
    </section>

    <!-- Visual categories -->
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

    <!-- Best sellers carousel (visual) -->
    <section>
      <div class="container carousel">
        <h3 class="section-title">This week’s best sellers</h3>
        <div class="track" id="track">
          <div class="item card"><img alt="Cooking oil" src="https://images.unsplash.com/photo-1517260739337-6799d878efca?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Cooking Oil 3L</strong><div>Promo • limited</div></div></div>
          <div class="item card"><img alt="Sugar" src="https://images.unsplash.com/photo-1615485925600-27149fc4d19b?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Sugar 1kg</strong><div>Top demand</div></div></div>
          <div class="item card"><img alt="Beans" src="https://images.unsplash.com/photo-1607301405394-4a578b865c98?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Beans 1kg</strong><div>Fresh stock</div></div></div>
          <div class="item card"><img alt="Rice" src="https://images.unsplash.com/photo-1565688534245-05d6b5be1847?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Rice 5kg</strong><div>Customer fav</div></div></div>
          <div class="item card"><img alt="Toothpaste" src="https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?q=80&w=1200&auto=format&fit=crop"><div class="content"><strong>Toothpaste</strong><div>Bundle deal</div></div></div>
        </div>
      </div>
    </section>

    <!-- Colored CTA band (not white) -->
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

    <!-- Order & Contact (short + visual) -->
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
            <div class="field">
              <label for="name">Your name</label>
              <input id="name" name="name" type="text" required placeholder="Jane Doe" />
            </div>
            <div class="field">
              <label for="email">Email</label>
              <input id="email" name="email" type="email" required placeholder="you@example.com" />
            </div>
            <div class="field">
              <label for="phone">Phone / WhatsApp</label>
              <input id="phone" name="phone" type="tel" placeholder="+256…" />
            </div>
            <div class="field">
              <label for="message">How can we help?</label>
              <textarea id="message" name="message" rows="5" required placeholder="Tell us what you need…"></textarea>
            </div>
            <!-- Honeypot -->
            <input type="text" name="company" style="display:none" tabindex="-1" autocomplete="off">
            <button class="btn primary" type="submit">Send Message</button>
            <div class="hint">We’ll never share your details. You’ll also get a copy of your message by email.</div>
          </form>

          <!-- Map at your exact coordinates -->
          <iframe class="map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=0.288835138082504,32.6322174072266&z=15&output=embed"></iframe>
        </div>
      </div>
    </section>
  </main>

  <!-- Sticky bottom quickbar -->
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

  <!-- JSON‑LD: LocalBusiness -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Store",
    "name": "ANN RETAIL SHOP",
    "url": "<?= htmlspecialchars($DOMAIN_URL) ?>",
    "image": "https://images.unsplash.com/photo-1585386959984-a41552231658?q=80&w=1200&auto=format&fit=crop",
    "telephone": "+256746825914",
    "email": "sales@annretailshop.philltechs.com",
    "address": {"@type": "PostalAddress", "addressCountry": "UG"},
    "openingHours": "Mo-Su 07:00-21:30",
    "sameAs": [
      "https://wa.me/256746825914",
      "https://wa.me/256781988570"
    ]
  }
  </script>

  <div id="toast" class="toast"></div>

  <script>
    // Year
    document.getElementById('year').textContent = new Date().getFullYear();

    // Mobile sheet menu
    const menuBtn = document.getElementById('menuToggle');
    const sheet = document.getElementById('mobileSheet');
    function closeSheet(){ sheet.classList.remove('open'); }
    menuBtn.addEventListener('click', ()=> sheet.classList.toggle('open'));

    // Toast for form status
    const toast = document.getElementById('toast');
    <?php if ($status !== null): ?>
      toast.textContent = <?= json_encode($status_msg) ?>;
      toast.className = 'toast <?= $status === 'ok' ? 'ok' : 'err' ?> show';
      setTimeout(()=> toast.classList.remove('show'), <?= $status === 'ok' ? 5000 : 7000 ?>);
    <?php endif; ?>

    // Lazy scroll to contact if POST
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
      window.location.hash = '#contact';
    <?php endif; ?>
  </script>
</body>
</html>
