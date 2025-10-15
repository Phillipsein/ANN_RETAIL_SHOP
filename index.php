<?php
/* ============================================================
   ANN RETAIL SHOP — Single-file Mobile-first Website
   File: index.php
   PHP 7.4+
   ------------------------------------------------------------
   Quick start:
   1) Put this file on your hosting as index.php
   2) Edit $SITE and $SMTP settings below
   3) (Optional) Add your Titan SMTP password, then test the form
   ============================================================ */

session_start();

/* --- SITE SETTINGS ---------------------------------------- */
$SITE = [
    'name'       => 'ANN RETAIL SHOP',
    'tagline'    => 'Everyday essentials • Fair prices • Friendly service',
    'phone_human' => '+256 700 000 000',        // visible on site
    'phone_call' => '+256700000000',           // tel: link (no spaces)
    'whatsapp'   => '256700000000',            // WhatsApp intl format (no +)
    'email'      => 'sales@annretailshop.philltechs.com',
    'address'    => 'Kampala, Uganda',
    'lat'        => 0.288835138082504,
    'lng'        => 32.6322174072266,
    'hours'      => 'Mon–Sun: 7:00am – 10:00pm',
    // If you already have a logo image, place it in same folder and set:
    'logo'       => '', // e.g. 'logo.png' (leave empty to use text logo)
];

/* --- (OPTIONAL) TITAN SMTP SETTINGS ----------------------- */
$SMTP = [
    'enabled' => false,                             // <- set true to use SMTP
    'host'    => 'smtp.titan.email',
    'port'    => 587,
    'secure'  => 'tls',                             // tls or ssl
    'user'    => 'sales@annretailshop.philltechs.com',
    'pass'    => ''                                 // <<< PUT TITAN PASSWORD HERE
];

/* --- SIMPLE UTILITIES ------------------------------------- */
function h($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function make_csrf()
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function check_csrf($t)
{
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $t ?? '');
}

function write_lead_csv($row)
{
    $file = __DIR__ . '/leads.csv';
    $isNew = !file_exists($file);
    $fh = fopen($file, 'a');
    if ($isNew) {
        fputcsv($fh, ['timestamp', 'name', 'phone', 'email', 'message', 'ip', 'ua']);
    }
    fputcsv($fh, $row);
    fclose($fh);
}

/* --- LIGHTWEIGHT SMTP MAILER (AUTH LOGIN + STARTTLS) ------ */
/* Not as feature-rich as PHPMailer, but good for a single-file site. */
function send_smtp($smtp, $fromEmail, $fromName, $toEmail, $subject, $html, $alt = '')
{
    $host = $smtp['host'];
    $port = $smtp['port'];
    $secure = $smtp['secure'];
    $user = $smtp['user'];
    $pass = $smtp['pass'];

    $timeout = 30;
    $remote  = ($secure === 'ssl' ? "ssl://$host" : $host) . ":$port";
    $fp = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
    if (!$fp) return [false, "Connect error: $errstr ($errno)"];

    stream_set_timeout($fp, $timeout);
    $read = function () use ($fp) {
        return fgets($fp, 515);
    };
    $send = function ($cmd) use ($fp) {
        fputs($fp, $cmd . "\r\n");
    };

    $resp = $read();
    if (strpos($resp, '220') !== 0) return [false, "Bad banner: $resp"];

    $send("EHLO annretailshop.local");
    $cap = '';
    for ($i = 0; $i < 20; $i++) {
        $line = $read();
        $cap .= $line;
        if (substr($line, 3, 1) !== '-') break;
    }

    if ($secure === 'tls') {
        $send("STARTTLS");
        $resp = $read();
        if (strpos($resp, '220') !== 0) return [false, "STARTTLS failed: $resp"];
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            return [false, "TLS negotiation failed"];
        }
        // re-EHLO after TLS
        $send("EHLO annretailshop.local");
        for ($i = 0; $i < 20; $i++) {
            $line = $read();
            if (substr($line, 3, 1) !== '-') break;
        }
    }

    $send("AUTH LOGIN");
    $resp = $read();
    if (strpos($resp, '334') !== 0) return [false, "AUTH step1: $resp"];
    $send(base64_encode($user));
    $resp = $read();
    if (strpos($resp, '334') !== 0) return [false, "AUTH step2: $resp"];
    $send(base64_encode($pass));
    $resp = $read();
    if (strpos($resp, '235') !== 0) return [false, "AUTH step3: $resp"];

    $send("MAIL FROM:<$fromEmail>");
    $resp = $read();
    if (strpos($resp, '250') !== 0) return [false, "MAIL FROM: $resp"];
    $send("RCPT TO:<$toEmail>");
    $resp = $read();
    if (strpos($resp, '250') !== 0 && strpos($resp, '251') !== 0) return [false, "RCPT TO: $resp"];
    $send("DATA");
    $resp = $read();
    if (strpos($resp, '354') !== 0) return [false, "DATA: $resp"];

    $boundary = "bnd_" . bin2hex(random_bytes(6));
    $headers = [];
    $headers[] = "From: $fromName <$fromEmail>";
    $headers[] = "To: <$toEmail>";
    $headers[] = "Subject: $subject";
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: multipart/alternative; boundary=\"$boundary\"";
    $headers[] = "X-Mailer: SimpleSMTP/1.0";

    $body  = "--$boundary\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
    $body .= ($alt ?: strip_tags(str_replace(["<br>", "<br/>", "<br />"], "\n", $html))) . "\r\n";
    $body .= "\r\n--$boundary\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
    $body .= $html . "\r\n";
    $body .= "\r\n--$boundary--\r\n";

    $data = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
    fputs($fp, $data . "\r\n");
    $resp = $read();
    if (strpos($resp, '250') !== 0) return [false, "Body send: $resp"];

    $send("QUIT");
    fclose($fp);
    return [true, "OK"];
}

/* --- MAIL DISPATCHER -------------------------------------- */
function send_mail($SMTP, $fromEmail, $fromName, $toEmail, $subject, $html, $alt = '')
{
    if ($SMTP['enabled'] && !empty($SMTP['pass'])) {
        return send_smtp($SMTP, $fromEmail, $fromName, $toEmail, $subject, $html, $alt);
    } else {
        // Try PHP mail() and return a friendly status
        $headers = "MIME-Version: 1.0\r\n" .
            "Content-type: text/html; charset=UTF-8\r\n" .
            "From: $fromName <$fromEmail>\r\n";
        $ok = @mail($toEmail, $subject, $html, $headers);
        return [$ok, $ok ? "OK (mail())" : "mail() failed or disabled on host"];
    }
}

/* --- HANDLE FORM SUBMISSION ------------------------------- */
$flash = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf'] ?? '';
    $hp   = $_POST['website'] ?? ''; // honeypot
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg  = trim($_POST['message'] ?? '');

    if (!$hp && check_csrf($csrf) && $name && ($phone || $email) && $msg) {
        // Save lead
        write_lead_csv([
            date('c'),
            $name,
            $phone,
            $email,
            $msg,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);

        // Build email
        $sub = "New Inquiry • " . $SITE['name'];
        $html = "<h2>New Inquiry from {$SITE['name']}</h2>
            <p><strong>Name:</strong> " . h($name) . "</p>
            <p><strong>Phone:</strong> " . h($phone) . "</p>
            <p><strong>Email:</strong> " . h($email) . "</p>
            <p><strong>Message:</strong><br>" . nl2br(h($msg)) . "</p>
            <hr><p>Time: " . h(date('r')) . "<br>IP: " . h($_SERVER['REMOTE_ADDR'] ?? '') . "</p>";

        [$ok, $info] = send_mail(
            $GLOBALS['SMTP'],
            $SITE['email'],
            $SITE['name'],
            $SITE['email'],
            $sub,
            $html
        );

        $flash = $ok ? ['ok', "Thanks $name! We’ve received your message. We’ll get back soon."]
            : ['warn', "Saved your message, but email didn’t send: $info"];
    } else {
        $flash = ['err', "Please fill in the required fields correctly."];
    }
}

/* --- CSRF TOKEN FOR FORM --------------------------------- */
$csrfToken = make_csrf();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= h($SITE['name']) ?> — Kampala Retail Shop</title>
    <meta name="description" content="ANN RETAIL SHOP • Everyday essentials, fair prices, fast service in Kampala. Groceries, household, beauty, baby items & more.">

    <!-- Colors -->
    <meta name="theme-color" content="#ff7a1a">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="" crossorigin="" />

    <style>
        :root {
            --brand: #ff7a1a;
            /* Orange */
            --mint: #23d3b0;
            /* Mint green */
            --ink: #0f172a;
            /* Deep slate */
            --text: #111827;
            --muted: #6b7280;
            --bg: #ffffff;
            --soft: #f8fafc;
            --card: #ffffff;
            --ring: rgba(255, 122, 26, 0.35);
            --ok: #059669;
            --warn: #b45309;
            --err: #b91c1c;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --text: #e5e7eb;
                --muted: #9ca3af;
                --bg: #0b0f1a;
                --soft: #0f1524;
                --card: #101726;
                --ink: #e5e7eb;
                --ring: rgba(35, 211, 176, 0.35);
            }
        }

        * {
            box-sizing: border-box
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Inter, system-ui, ui-sans-serif, Segoe UI, Roboto, Arial;
            background: var(--bg);
            color: var(--text);
        }

        /* Layout helpers */
        .wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: clamp(14px, 3vw, 22px);
        }

        .grid {
            display: grid;
            gap: clamp(14px, 2.5vw, 22px);
        }

        .g2 {
            grid-template-columns: repeat(2, 1fr)
        }

        .g3 {
            grid-template-columns: repeat(3, 1fr)
        }

        .g4 {
            grid-template-columns: repeat(4, 1fr)
        }

        @media (max-width:900px) {

            .g3,
            .g4 {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        @media (max-width:640px) {

            .g2,
            .g3,
            .g4 {
                grid-template-columns: 1fr
            }
        }

        /* Header / Nav */
        header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: var(--bg);
            border-bottom: 1px solid rgba(0, 0, 0, .06);
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px clamp(14px, 3vw, 22px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            letter-spacing: .4px
        }

        .brand .logo {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at 30% 30%, var(--mint), var(--brand));
            color: #fff;
            font-weight: 900
        }

        .brand span {
            font-size: 18px
        }

        .menu-btn {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            padding: 10px 12px;
            border: 1px solid rgba(0, 0, 0, .08);
            border-radius: 12px;
            background: var(--card);
            cursor: pointer
        }

        .menu-btn svg {
            width: 20px;
            height: 20px
        }

        .navlinks {
            display: flex;
            gap: 16px;
            align-items: center
        }

        .navlinks a {
            color: var(--text);
            text-decoration: none;
            font-weight: 600;
            padding: 8px 10px;
            border-radius: 10px
        }

        .navlinks a:hover {
            background: var(--soft)
        }

        @media (max-width:860px) {
            .navlinks {
                display: none
            }

            .menu-panel {
                display: none;
                position: absolute;
                top: 62px;
                right: 12px;
                background: var(--card);
                border: 1px solid rgba(0, 0, 0, .08);
                border-radius: 14px;
                padding: 10px;
                width: min(92vw, 380px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, .18)
            }

            .menu-panel a {
                display: block;
                padding: 12px 10px;
                border-radius: 10px;
                text-decoration: none;
                color: var(--text);
                font-weight: 600
            }

            .menu-panel a:hover {
                background: var(--soft)
            }

            .menu-panel.show {
                display: block
            }
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, rgba(255, 122, 26, .10), rgba(35, 211, 176, .10));
            border-bottom: 1px solid rgba(0, 0, 0, .06)
        }

        .hero-wrap {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 24px;
            align-items: center
        }

        .hero h1 {
            font-size: clamp(28px, 5vw, 44px);
            line-height: 1.05;
            margin: 8px 0
        }

        .hero p {
            color: var(--muted);
            font-size: clamp(14px, 2.5vw, 18px)
        }

        .badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px
        }

        .badge {
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--card);
            border: 1px solid rgba(0, 0, 0, .06);
            font-weight: 600;
            font-size: 13px
        }

        .cta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 16px
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 14px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            border: 2px solid transparent
        }

        .btn.primary {
            background: var(--brand);
            color: #fff
        }

        .btn.mint {
            background: var(--mint);
            color: #0c1b15
        }

        .btn.ghost {
            background: var(--card);
            border-color: rgba(0, 0, 0, .08);
            color: var(--text)
        }

        .hero-img {
            aspect-ratio: 4/3;
            border-radius: 18px;
            overflow: hidden;
            background:
                radial-gradient(600px 220px at 10% 10%, rgba(255, 122, 26, .25), transparent 40%),
                radial-gradient(600px 220px at 90% 90%, rgba(35, 211, 176, .25), transparent 40%),
                linear-gradient(135deg, #0b1222, #111a2f);
            display: grid;
            place-items: center;
            color: #c7fff3;
            font-weight: 800;
            letter-spacing: .3px
        }

        .hero-img small {
            color: #7bead4;
            font-weight: 700
        }

        /* Section headings */
        .sh {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 18px 0 8px
        }

        .sh h2 {
            margin: 0;
            font-size: clamp(20px, 3.2vw, 28px)
        }

        .sh p {
            margin: 0;
            color: var(--muted)
        }

        /* Cards */
        .card {
            background: var(--card);
            border: 1px solid rgba(0, 0, 0, .06);
            border-radius: 16px;
            overflow: hidden
        }

        .card .img {
            aspect-ratio: 1.6/1;
            background: linear-gradient(135deg, rgba(255, 122, 26, .18), rgba(35, 211, 176, .18));
            display: block
        }

        .card .body {
            padding: 12px
        }

        .price {
            font-weight: 800
        }

        .muted {
            color: var(--muted)
        }

        /* Feature list */
        .feature {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 12px;
            background: var(--card);
            border: 1px solid rgba(0, 0, 0, .06);
            border-radius: 14px
        }

        .icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--mint), var(--brand));
            color: #fff;
            font-weight: 900
        }

        /* Contact / Form */
        form {
            display: grid;
            gap: 10px
        }

        .fld {
            display: grid;
            gap: 6px
        }

        .fld input,
        .fld textarea {
            width: 100%;
            padding: 12px 13px;
            border-radius: 12px;
            border: 1.6px solid rgba(0, 0, 0, .12);
            background: var(--bg);
            color: var(--text);
            outline: none;
            box-shadow: 0 0 0 0px var(--ring);
        }

        .fld input:focus,
        .fld textarea:focus {
            box-shadow: 0 0 0 6px var(--ring);
            border-color: var(--brand);
        }

        .fld small {
            color: var(--muted)
        }

        .hint {
            font-size: 13px;
            color: var(--muted)
        }

        .alert {
            padding: 12px;
            border-radius: 12px;
            font-weight: 600
        }

        .alert.ok {
            background: rgba(5, 150, 105, .12);
            color: #d1fae5;
            border: 1px solid rgba(5, 150, 105, .35)
        }

        .alert.warn {
            background: rgba(180, 83, 9, .12);
            color: #fde68a;
            border: 1px solid rgba(180, 83, 9, .35)
        }

        .alert.err {
            background: rgba(185, 28, 28, .12);
            color: #fecaca;
            border: 1px solid rgba(185, 28, 28, .35)
        }

        @media (prefers-color-scheme: light) {
            .alert.ok {
                color: #065f46;
                background: #ecfdf5
            }

            .alert.warn {
                color: #92400e;
                background: #fffbeb
            }

            .alert.err {
                color: #991b1b;
                background: #fef2f2
            }
        }

        /* Map */
        #map {
            height: 320px;
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, .08);
            overflow: hidden
        }

        /* Footer */
        footer {
            margin-top: 30px;
            padding: 30px 0;
            background: var(--soft);
            border-top: 1px solid rgba(0, 0, 0, .06);
            color: var(--muted)
        }

        /* Sticky mobile action bar */
        .mbar {
            position: sticky;
            bottom: 0;
            z-index: 40;
            background: var(--card);
            border-top: 1px solid rgba(0, 0, 0, .08);
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }

        .mbar a {
            display: grid;
            place-items: center;
            padding: 12px;
            text-decoration: none;
            color: var(--text);
            font-weight: 700
        }

        .mbar a span {
            font-size: 12px
        }

        .mbar a.primary {
            background: var(--brand);
            color: #fff
        }

        .mbar a.mint {
            background: var(--mint);
            color: #0c1b15
        }
    </style>
</head>

<body>

    <header>
        <div class="nav">
            <div class="brand">
                <?php if ($SITE['logo']): ?>
                    <img src="<?= h($SITE['logo']) ?>" alt="<?= h($SITE['name']) ?> logo" width="38" height="38" style="border-radius:10px;object-fit:cover">
                <?php else: ?>
                    <div class="logo">AR</div>
                <?php endif; ?>
                <span><?= h($SITE['name']) ?></span>
            </div>
            <nav class="navlinks">
                <a href="#shop">Shop</a>
                <a href="#deals">Deals</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
            </nav>
            <button class="menu-btn" id="menuBtn" aria-label="Open Menu">
                <!-- burger icon -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 6h18M3 12h18M3 18h18" stroke-width="2" stroke-linecap="round" />
                </svg>
                Menu
            </button>
            <div class="menu-panel" id="menuPanel">
                <a href="#shop">Shop</a>
                <a href="#deals">Deals</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
                <a href="tel:<?= h($SITE['phone_call']) ?>">Call us: <?= h($SITE['phone_human']) ?></a>
            </div>
        </div>
    </header>

    <main>

        <!-- HERO -->
        <section class="hero">
            <div class="wrap hero-wrap">
                <div>
                    <div class="badge">Kampala • Open daily</div>
                    <h1><?= h($SITE['name']) ?></h1>
                    <p><?= h($SITE['tagline']) ?></p>
                    <div class="badges">
                        <div class="badge">Groceries</div>
                        <div class="badge">Household</div>
                        <div class="badge">Beauty</div>
                        <div class="badge">Baby</div>
                        <div class="badge">Snacks</div>
                    </div>
                    <div class="cta">
                        <a class="btn primary" href="#shop">Browse Items</a>
                        <a class="btn mint" href="https://wa.me/<?= h($SITE['whatsapp']) ?>?text=Hello%20ANN%20RETAIL%20SHOP%2C%20I%27d%20like%20to%20order.">WhatsApp Order</a>
                        <a class="btn ghost" href="tel:<?= h($SITE['phone_call']) ?>">Call <?= h($SITE['phone_human']) ?></a>
                    </div>
                </div>
                <div class="hero-img">
                    <div style="text-align:center">
                        <div style="font-size:40px;line-height:.95">SHOP • SAVE • SMILE</div>
                        <small>Tip: replace this with your shop photo</small>
                    </div>
                </div>
            </div>
        </section>

        <!-- CATEGORIES / FEATURED -->
        <section id="shop" class="wrap">
            <div class="sh">
                <h2>Popular Picks</h2>
                <p>Fresh staples & everyday needs</p>
            </div>
            <div class="grid g4">
                <?php
                $items = [
                    ['Rice (5kg)', 'UGX 28,500', 'Switch image later'],
                    ['Cooking Oil (3L)', 'UGX 27,000', 'Switch image later'],
                    ['Sugar (2kg)', 'UGX 9,800', 'Switch image later'],
                    ['Soap Bar', 'UGX 3,500', 'Switch image later'],
                    ['Toothpaste', 'UGX 6,500', 'Switch image later'],
                    ['Baby Diapers', 'UGX 38,000', 'Switch image later'],
                    ['Tea Bags', 'UGX 5,500', 'Switch image later'],
                    ['Biscuits', 'UGX 3,000', 'Switch image later'],
                ];
                foreach ($items as $i) {
                    echo '<article class="card">';
                    echo '  <div class="img"></div>';
                    echo '  <div class="body">';
                    echo '    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px">';
                    echo '      <strong>' . h($i[0]) . '</strong><span class="price">' . h($i[1]) . '</span>';
                    echo '    </div>';
                    echo '    <div class="muted" style="margin-top:4px">' . h($i[2]) . '</div>';
                    echo '    <div style="margin-top:10px;display:flex;gap:8px">';
                    echo '      <a class="btn mint" style="padding:9px 12px" href="https://wa.me/' . h($SITE['whatsapp']) . '?text=Order:%20' . rawurlencode($i[0]) . '%20(' . rawurlencode($i[1]) . ')">Order</a>';
                    echo '      <a class="btn ghost" style="padding:9px 12px" href="#contact">Ask</a>';
                    echo '    </div>';
                    echo '  </div>';
                    echo '</article>';
                }
                ?>
            </div>
        </section>

        <!-- DEALS / PROMOS -->
        <section id="deals" class="wrap">
            <div class="sh">
                <h2>Deals of the Week</h2>
                <p>Save more with bundle offers</p>
            </div>
            <div class="grid g3">
                <div class="feature">
                    <div class="icon">%</div>
                    <div>
                        <strong>Combo: Rice + Oil</strong>
                        <div class="muted">Bundle & save • Ask at checkout</div>
                    </div>
                </div>
                <div class="feature">
                    <div class="icon">★</div>
                    <div>
                        <strong>Members get early deals</strong>
                        <div class="muted">Join our WhatsApp list for weekly offers</div>
                    </div>
                </div>
                <div class="feature">
                    <div class="icon">⏱</div>
                    <div>
                        <strong>Express pickup</strong>
                        <div class="muted">Order on WhatsApp, pick in minutes</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ABOUT / MAP -->
        <section id="about" class="wrap">
            <div class="sh">
                <h2>Find Us</h2>
                <p><?= h($SITE['address']) ?> • <?= h($SITE['hours']) ?></p>
            </div>
            <div id="map"></div>
        </section>

        <!-- CONTACT -->
        <section id="contact" class="wrap">
            <div class="sh">
                <h2>Order / Contact</h2>
                <p>We reply quickly during open hours</p>
            </div>

            <?php if ($flash): ?>
                <div class="alert <?= $flash[0] ?>"><?= $flash[1] ?></div>
            <?php endif; ?>

            <div class="grid g2">
                <form method="post" action="#contact" novalidate>
                    <input type="hidden" name="csrf" value="<?= h($csrfToken) ?>">
                    <!-- honeypot -->
                    <input type="text" name="website" value="" style="display:none!important" tabindex="-1" autocomplete="off">
                    <div class="fld">
                        <label for="name">Your name *</label>
                        <input id="name" name="name" required placeholder="e.g. Jane Doe">
                    </div>
                    <div class="fld">
                        <label for="phone">Phone (or WhatsApp) *</label>
                        <input id="phone" name="phone" placeholder="+256 7…">
                        <small class="hint">We’ll call or WhatsApp if needed</small>
                    </div>
                    <div class="fld">
                        <label for="email">Email (optional)</label>
                        <input id="email" name="email" type="email" placeholder="you@example.com">
                    </div>
                    <div class="fld">
                        <label for="message">What would you like?</label>
                        <textarea id="message" name="message" rows="5" placeholder="List the items and quantities…"></textarea>
                    </div>
                    <button class="btn primary" type="submit">Send Request</button>
                    <div class="hint">We also take orders on WhatsApp: <a href="https://wa.me/<?= h($SITE['whatsapp']) ?>">chat now</a></div>
                </form>

                <div class="grid" style="gap:14px">
                    <div class="feature">
                        <div class="icon">☎</div>
                        <div>
                            <strong>Call us</strong>
                            <div><a href="tel:<?= h($SITE['phone_call']) ?>"><?= h($SITE['phone_human']) ?></a></div>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="icon">✉</div>
                        <div>
                            <strong>Email</strong>
                            <div><a href="mailto:<?= h($SITE['email']) ?>"><?= h($SITE['email']) ?></a></div>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="icon">🕒</div>
                        <div>
                            <strong>Hours</strong>
                            <div class="muted"><?= h($SITE['hours']) ?></div>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="icon">📍</div>
                        <div>
                            <strong>Directions</strong>
                            <div><a target="_blank" rel="noreferrer" href="https://www.google.com/maps/search/?api=1&query=<?= rawurlencode($SITE['lat'] . ',' . $SITE['lng']) ?>">Open in Google Maps</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Sticky Mobile Bar -->
    <nav class="mbar">
        <a href="tel:<?= h($SITE['phone_call']) ?>">
            <div>📞</div><span>Call</span>
        </a>
        <a class="mint" href="https://wa.me/<?= h($SITE['whatsapp']) ?>?text=Hello%20ANN%20RETAIL%20SHOP">
            <div>💬</div><span>WhatsApp</span>
        </a>
        <a href="https://www.google.com/maps/search/?api=1&query=<?= rawurlencode($SITE['lat'] . ',' . $SITE['lng']) ?>">
            <div>📍</div><span>Map</span>
        </a>
        <a class="primary" href="#contact">
            <div>🧺</div><span>Order</span>
        </a>
    </nav>

    <footer>
        <div class="wrap grid g3">
            <div>
                <div class="brand" style="gap:8px">
                    <div class="logo">AR</div><span><?= h($SITE['name']) ?></span>
                </div>
                <div class="muted" style="margin-top:10px">© <?= date('Y') ?> <?= h($SITE['name']) ?>. All rights reserved.</div>
            </div>
            <div>
                <strong>Contact</strong>
                <div class="muted">Phone: <a href="tel:<?= h($SITE['phone_call']) ?>"><?= h($SITE['phone_human']) ?></a></div>
                <div class="muted">Email: <a href="mailto:<?= h($SITE['email']) ?>"><?= h($SITE['email']) ?></a></div>
                <div class="muted">Address: <?= h($SITE['address']) ?></div>
            </div>
            <div>
                <strong>Open Hours</strong>
                <div class="muted"><?= h($SITE['hours']) ?></div>
            </div>
        </div>
    </footer>

    <!-- JS: menu + map -->
    <script>
        // Mobile menu
        const menuBtn = document.getElementById('menuBtn');
        const menuPanel = document.getElementById('menuPanel');
        menuBtn?.addEventListener('click', () => menuPanel.classList.toggle('show'));

        // Basic focus ring for keyboard users
        document.addEventListener('keydown', e => {
            if (e.key === 'Tab') {
                document.body.classList.add('show-focus');
            }
        });
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="" crossorigin=""></script>
    <script>
        // Leaflet map
        const lat = <?= $SITE['lat'] ?>,
            lng = <?= $SITE['lng'] ?>;
        const map = L.map('map', {
            scrollWheelZoom: false
        }).setView([lat, lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup('<?= h($SITE['name']) ?>').openPopup();
    </script>

    <!-- SEO: LocalBusiness -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Store",
            "name": "<?= h($SITE['name']) ?>",
            "telephone": "<?= h($SITE['phone_human']) ?>",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Kampala",
                "addressCountry": "UG"
            },
            "geo": {
                "@type": "GeoCoordinates",
                "latitude": <?= $SITE['lat'] ?>,
                "longitude": <?= $SITE['lng'] ?>
            },
            "url": "",
            "openingHours": "Mo-Su 07:00-22:00",
            "image": ""
        }
    </script>
</body>

</html>