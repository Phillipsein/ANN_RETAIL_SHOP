<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ANN RETAIL SHOP | Everyday Essentials at Great Prices</title>
    <meta name="description" content="ANN RETAIL SHOP — your neighbourhood store for groceries, household items, personal care, and more. Order, call, or visit us today!" />
    <meta name="theme-color" content="#f97316" />

    <!-- SEO / Social -->
    <meta property="og:title" content="ANN RETAIL SHOP" />
    <meta property="og:description" content="Everyday essentials at great prices. Call, WhatsApp, or visit us today." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://annretailshop.com/" />
    <meta property="og:image" content="https://images.unsplash.com/photo-1585386959984-a41552231658?q=80&w=1600&auto=format&fit=crop" />
    <meta name="twitter:card" content="summary_large_image" />

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Favicon (placeholder) -->
    <!-- Letter “A” favicon with orange→mint gradient background -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0' y1='0' x2='1' y2='1'%3E%3Cstop offset='0%25' stop-color='%23f97316'/%3E%3Cstop offset='100%25' stop-color='%2310b981'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='100' height='100' rx='18' fill='url(%23g)'/%3E%3Ctext x='50' y='64' font-size='64' font-weight='700' text-anchor='middle' fill='white' font-family='Arial, Helvetica, sans-serif'%3EA%3C/text%3E%3C/svg%3E">


    <style>
        :root {
            --brand: #f97316;
            --brand-600: #ea580c;
            --accent: #10b981;
            --accent-600: #059669;
            --ink: #0f172a;
            --muted: #475569;
            --bg: #f8fafc;
            --card: #ffffff;
            --ring: rgba(249, 115, 22, .25)
        }

        * {
            box-sizing: border-box
        }

        html,
        body {
            margin: 20;
            padding: 0;
            font-family: 'Inter', system-ui, Segoe UI, Roboto, Arial, sans-serif;
            color: var(--ink);
            background: var(--bg)
        }

        a {
            color: inherit;
            text-decoration: none
        }

        img {
            max-width: 100%;
            display: block
        }

        .container {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 16px
        }

        /* Header */
        header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, .9);
            backdrop-filter: saturate(180%) blur(10px);
            border-bottom: 1px solid #eef2f7
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0
        }

        .brand {
            display: flex;
            gap: 12px;
            align-items: center
        }

        .logo-wrap {
            position: relative;
            height: 80px;
            /* increase size */
            width: 80px;
            border-radius: 22px;
            padding: 4px;
            background: conic-gradient(from 180deg at 50% 50%, #f97316, #10b981, #f97316);
            box-shadow: 0 10px 24px rgba(249, 115, 22, .18), inset 0 0 0 1px rgba(255, 255, 255, .6);
        }

        .logo-wrap .inner {
            border-radius: 18px;
            background: #fff;
            height: 100%;
            width: 100%;
            display: grid;
            place-items: center;
            overflow: hidden;
            padding: 6px;
            /* 👈 added padding inside the logo frame */
        }

        .logo-wrap .inner img {
            height: 100%;
            width: 100%;
            object-fit: contain;
        }

        .brand .name {
            font-weight: 900;
            font-size: 22px;
            letter-spacing: .2px;
            line-height: 1.1;
        }

        .brand .name .grad {
            background: linear-gradient(90deg, #f97316, #10b981);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .brand .subline {
            font-size: 13px;
            color: #065f46;
            margin-top: 4px;
        }

        @media (min-width: 960px) {
            .logo-wrap {
                height: 90px;
                width: 90px;
                border-radius: 26px;
            }
        }

        .brand .name {
            font-weight: 800;
            letter-spacing: .2px
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #d1fae5;
            color: #065f46;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 14px;
            border: 1px solid #a7f3d0
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            padding: 10px 14px;
            border-radius: 12px;
            font-weight: 600;
            transition: .2s;
            box-shadow: 0 1px 0 rgba(15, 23, 42, .02)
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, .08)
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(2, 132, 199, .18)
        }

        .btn.primary {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff
        }

        .btn.primary:hover {
            background: var(--brand-600);
            border-color: var(--brand-600)
        }

        .btn.ghost {
            background: transparent
        }

        .mobile-hide {
            display: none
        }

        /* Hero */
        .hero {
            position: relative;
            overflow: hidden
        }

        .hero:before {
            content: "";
            position: absolute;
            inset: -20% -10% auto -10%;
            height: 120%;
            background: radial-gradient(600px 300px at 20% 20%, rgba(249, 115, 22, .18), transparent 60%), radial-gradient(500px 250px at 80% 10%, rgba(16, 185, 129, .18), transparent 60%);
            filter: blur(10px);
            z-index: 0
        }

        .hero .inner {
            position: relative;
            z-index: 1
        }

        .hero .inner {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 20px;
            align-items: center;
            padding: 28px 0
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            line-height: 1.1;
            margin: 0 0 10px
        }

        .hero p {
            color: var(--muted);
            font-size: 18px;
            margin: 0 0 20px
        }

        .badge {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            background: #fffbeb;
            border: 1px solid #fed7aa;
            color: #9a3412;
            padding: 8px 12px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 13px
        }

        .hero-card {
            background: linear-gradient(180deg, #ffffff 0%, #fff7ed 100%);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(2, 132, 199, .12)
        }

        .hero-visual {
            position: relative
        }

        .float {
            position: absolute;
            inset: auto;
            animation: float 6s ease-in-out infinite
        }

        .float.one {
            top: 8%;
            left: -4%;
        }

        .float.two {
            bottom: -10%;
            right: -6%
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-10px)
            }
        }

        /* Sections */
        section {
            padding: 28px 0
        }

        .section-title {
            font-size: 28px;
            margin: 0 0 8px
        }

        .section-desc {
            color: var(--muted);
            margin: 0 0 24px
        }

        /* Features */
        .features {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px
        }

        .feature {
            background: var(--card);
            padding: 18px;
            border-radius: 16px;
            border: 1px solid #eef2f7;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .04)
        }

        .feature h4 {
            margin: 10px 0 6px;
            font-size: 16px
        }

        .feature p {
            margin: 0;
            color: var(--muted);
            font-size: 14px
        }

        /* Categories grid */
        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px
        }

        .card {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid #eef2f7;
            background: var(--card);
            box-shadow: 0 12px 30px rgba(15, 23, 42, .06)
        }

        .card .content {
            position: absolute;
            inset: auto 0 0 0;
            padding: 14px;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0, rgba(0, 0, 0, .65) 100%);
            color: #fff
        }

        /* Carousel */
        .carousel {
            position: relative
        }

        .track {
            display: flex;
            gap: 16px;
            overflow: auto;
            scroll-snap-type: x mandatory;
            padding-bottom: 8px
        }

        .track .item {
            min-width: 280px;
            scroll-snap-align: start
        }

        .carousel .nav {
            position: absolute;
            inset: -50px 0 auto 0;
            display: flex;
            gap: 8px;
            justify-content: flex-end
        }

        .icon-btn {
            border: 1px solid #e2e8f0;
            background: #fff;
            padding: 8px 10px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .08)
        }

        /* CTA band */
        .cta {
            background: linear-gradient(90deg, var(--brand), var(--accent));
            color: #fff;
            border-radius: 20px;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px
        }

        /* Contact */
        .contact {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px
        }

        form {
            background: var(--card);
            padding: 20px;
            border: 1px solid #eef2f7;
            border-radius: 16px;
            box-shadow: 0 10px 22px rgba(2, 132, 199, .06)
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 12px
        }

        .field input,
        .field textarea {
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            outline: none;
            font-size: 15px;
            background: #fff
        }

        .field input:focus,
        .field textarea:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px var(--ring)
        }

        .hint {
            font-size: 12px;
            color: var(--muted)
        }

        iframe.map {
            width: 100%;
            height: 100%;
            min-height: 360px;
            border: 0;
            border-radius: 16px
        }

        /* Footer */
        footer {
            padding: 24px 0;
            border-top: 1px solid #eef2f7;
            color: var(--muted);
            font-size: 14px
        }

        /* Utilities */
        .row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap
        }

        .tag {
            background: #e2e8f0;
            color: #0f172a;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 12px
        }

        .shadow-pop {
            animation: pop .6s ease-out
        }

        @keyframes pop {
            0% {
                transform: scale(.96);
                opacity: .6
            }

            100% {
                transform: scale(1);
                opacity: 1
            }
        }

        /* Responsive */
        @media (max-width: 960px) {
            .hero .inner {
                grid-template-columns: 1fr;
                gap: 14px;
                padding: 22px 0
            }

            .features {
                grid-template-columns: repeat(2, 1fr)
            }

            .grid {
                grid-template-columns: repeat(2, 1fr)
            }

            .contact {
                grid-template-columns: 1fr
            }

            .actions {
                display: none
            }

            .mstrip {
                display: block
            }
        }

        @media (min-width: 961px) {
            .mobile-hide {
                display: inline-flex
            }
        }

        .features {
            grid-template-columns: repeat(2, 1fr)
        }

        .grid {
            grid-template-columns: repeat(2, 1fr)
        }

        .contact {
            grid-template-columns: 1fr
        }

        .mobile-hide {
            display: none
        }


        @media (min-width: 961px) {
            .mobile-hide {
                display: inline-flex
            }
        }

        /* Mobile action strip */
        .mstrip {
            display: none;
            border-bottom: 1px solid #eef2f7;
            background: #fff
        }

        .mstrip .container {
            display: flex;
            gap: 8px;
            overflow: auto;
            padding: 8px 16px
        }

        .mstrip .btn {
            flex: 1;
            white-space: nowrap;
            justify-content: center
        }

        .hero-copy {
            max-width: 680px
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fffbeb;
            border: 1px solid #fed7aa;
            color: #9a3412;
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            line-height: 1.12;
            margin: 10px 0 8px
        }

        .brand-grad {
            background: linear-gradient(90deg, #f97316, #10b981);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            white-space: nowrap
        }

        .accent-underline {
            display: block;
            margin-top: 6px;
            font-size: .9em;
            color: #475569;
            position: relative;
            padding-bottom: 8px
        }

        .accent-underline:after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 120px;
            height: 6px;
            border-radius: 6px;
            background: linear-gradient(90deg, #f97316, #10b981);
            opacity: .35
        }

        .lead {
            color: #475569;
            font-size: 16px;
            margin: 12px 0 16px
        }

        .cta-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 8px 0 6px
        }

        .cta-row .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px
        }

        .cta-row .btn svg {
            width: 18px;
            height: 18px;
            fill: currentColor
        }

        .chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px
        }

        .chip svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2
        }

        /* Responsive tweaks */
        @media (max-width: 960px) {
            .hero-title {
                font-size: 32px
            }
        }

        /* Section shell */
        .about-section {
            padding: 34px 0
        }

        .about-head {
            max-width: 760px;
            margin: 0 auto 18px;
            text-align: center
        }

        .about-badge {
            display: inline-block;
            background: #fffbeb;
            border: 1px solid #fed7aa;
            color: #9a3412;
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px
        }

        .about-title {
            margin: 8px 0 6px;
            font-size: 28px
        }

        .brand-grad {
            background: linear-gradient(90deg, #f97316, #10b981);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent
        }

        .about-desc {
            color: var(--muted);
            margin: 0 auto
        }

        /* Grid */
        .about-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: 1fr 1fr;
        }

        @media (max-width: 900px) {
            .about-grid {
                grid-template-columns: 1fr
            }
        }

        /* Cards */
        .about-card {
            position: relative;
            background: var(--card);
            padding: 18px;
            border-radius: 16px;
            border: 1px solid #eef2f7;
            box-shadow: 0 6px 18px rgba(15, 23, 42, .06);
            overflow: hidden;
        }

        .about-card:before {
            content: "";
            position: absolute;
            inset: -40% -20% auto -20%;
            height: 120%;
            background: radial-gradient(400px 200px at 10% 10%, rgba(249, 115, 22, .08), transparent 60%),
                radial-gradient(380px 180px at 90% 0%, rgba(16, 185, 129, .08), transparent 60%);
            filter: blur(8px);
            z-index: 0
        }

        .about-card>* {
            position: relative;
            z-index: 1
        }

        .card-title {
            margin: 0 0 6px;
            font-size: 18px
        }

        /* Stars */
        .stars {
            color: #f59e0b;
            letter-spacing: 1px;
            margin-bottom: 6px;
            font-size: 16px
        }

        .stars .half {
            opacity: .6
        }

        /* Quotes & trust */
        .quote {
            margin: 6px 0;
            color: var(--muted)
        }

        .trust-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px
        }

        .pill.soft {
            background: #ecfeff;
            border: 1px solid #bae6fd;
            color: #0369a1;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px
        }

        /* Icon list */
        .icon-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 8px
        }

        .icon-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--ink)
        }

        .icon-list svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            flex: 0 0 auto
        }

        /* Brand chips */
        .brand-chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px
        }

        .brand-chips .chip {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px
        }

        /* Guarantee line */
        .guarantee {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 8px 12px;
            border-radius: 12px;
            font-weight: 700
        }

        .guarantee svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2
        }
    </style>
</head>

<body>
    <header>
        <div class="container nav">
            <div class="brand">
                <div class="logo-wrap" aria-hidden="true">
                    <div class="inner">
                        <img src="assets/logo.png" alt="ANN RETAIL SHOP logo" />
                    </div>
                </div>
                <div>
                    <div class="name"><span class="grad">ANN RETAIL SHOP</span></div>
                    <div class="subline">Everyday essentials • Neighbourhood store</div>
                    <div class="pill" title="Open 7 days a week">Open • 7:00am – 9:30pm</div>
                </div>
            </div>

            <div class="actions">
                <a class="btn ghost mobile-hide" href="https://wa.me/256746825914" target="_blank" rel="noopener">WhatsApp</a>
                <a class="btn" href="#contact">Call / Visit</a>
                <a class="btn primary" href="#order">Order Now</a>
            </div>
        </div>
    </header>

    <div class="mstrip">
        <div class="container">
            <a class="btn" href="#catalog">Categories</a>
            <a class="btn" href="#order">Order</a>
            <a class="btn" href="#contact">Contact</a>
            <a class="btn" href="https://wa.me/256746825914" target="_blank" rel="noopener">WhatsApp</a>
        </div>
    </div>

    <main>
        <!-- Hero -->
        <section class="hero">
            <div class="container inner">
                <div class="hero-copy">
                    <span class="badge">Everyday essentials • Fast • Friendly</span>

                    <h1 class="hero-title">
                        Welcome to <span class="brand-grad">ANN RETAIL SHOP</span>
                        <span class="accent-underline">Your neighbourhood store for all essentials</span>
                    </h1>

                    <p class="lead">
                        Fresh groceries, home & cleaning, personal care, snacks & beverages, baby needs, and more.
                        Shop in-store or order via WhatsApp — we deliver around town.
                    </p>

                    <div class="cta-row">
                        <a class="btn primary" href="https://wa.me/256746825914" target="_blank" rel="noopener">
                            <!-- WhatsApp icon -->
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 3.9A10 10 0 0 0 3.1 17.3L2 22l4.8-1A10 10 0 1 0 20 3.9ZM12 20a8 8 0 0 1-4.1-1.1l-.3-.2-2.8.6.6-2.7-.2-.3A8 8 0 1 1 12 20Zm4.6-5.3c-.2-.1-1.1-.6-1.3-.7-.2-.1-.4-.1-.6.1-.2.2-.6.7-.7.8-.1.2-.3.2-.5.1-.2-.1-.9-.3-1.7-1-.6-.6-1-1.3-1.1-1.5-.1-.2 0-.3.1-.5l.4-.5c.1-.2.1-.3.2-.5 0-.2 0-.3 0-.4 0-.1-.6-1.5-.8-2-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.4c.1.2 1.6 2.5 3.9 3.5.5.2.9.4 1.2.5.5.2 1 .2 1.4.1.4-.1 1.1-.4 1.3-.9.2-.5.2-.9.1-1 .1-.1 0-.1-.1-.2Z" />
                            </svg>
                            Order on WhatsApp
                        </a>

                        <a class="btn" href="#catalog">
                            <!-- Grid icon -->
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M3 3h8v8H3V3Zm10 0h8v8h-8V3ZM3 13h8v8H3v-8Zm10 0h8v8h-8v-8Z" />
                            </svg>
                            Browse Categories
                        </a>
                    </div>

                    <div class="chips">
                        <span class="chip">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6 9 17l-5-5" />
                            </svg>
                            Weekly deals
                        </span>
                        <span class="chip">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 22s8-4 8-10a8 8 0 1 0-16 0c0 6 8 10 8 10Z" />
                            </svg>
                            Mobile payments
                        </span>
                        <span class="chip">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6 9 17l-5-5" />
                            </svg>
                            48-hour returns
                        </span>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="hero-card">
                        <img alt="Retail aisle" src="https://www.marketing91.com/wp-content/uploads/2018/01/Types-of-Retail-Stores-2.jpg" />
                    </div>
                    <img class="float one" style="width:160px" alt="groceries" src="https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=600&auto=format&fit=crop" />

                </div>
            </div>
        </section>

        <!-- Trust features -->
        <section>
            <div class="container">
                <h3 class="section-title">Why shop with us</h3>
                <p class="section-desc">We keep it simple: quality products, fair prices, and fast friendly service.</p>
                <div class="features">
                    <div class="feature"><strong>Fair Prices</strong>
                        <p>Transparent pricing and regular promos.</p>
                    </div>
                    <div class="feature"><strong>Fresh Stock</strong>
                        <p>Frequent restocks on top sellers.</p>
                    </div>
                    <div class="feature"><strong>Multiple Payments</strong>
                        <p>Cash, Mobile Money, card (where available).</p>
                    </div>
                    <div class="feature"><strong>Local Delivery</strong>
                        <p>WhatsApp to order; we deliver around town.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section id="catalog">
            <div class="container">
                <h3 class="section-title">Popular categories</h3>
                <p class="section-desc">A quick look at our most requested items.</p>
                <div class="grid">
                    <a class="card" href="#order"><img alt="Groceries" src="https://tse3.mm.bing.net/th/id/OIP.Liols46qTt5HfXKIHJXHVAAAAA?cb=12&w=450&h=800&rs=1&pid=ImgDetMain&o=7&rm=3">
                        <div class="content"><strong>Groceries</strong>
                            <div>Rice, flour, sugar, beans…</div>
                        </div>
                    </a>
                    <a class="card" href="#order"><img alt="Beverages" src="https://i.pinimg.com/originals/5c/a2/65/5ca26537161d6418bb9418ba04bd692d.jpg">
                        <div class="content"><strong>Beverages</strong>
                            <div>Juice, water, soda, tea…</div>
                        </div>
                    </a>
                    <a class="card" href="#order"><img alt="Personal care" src="https://images.pond5.com/dollar-general-retail-store-interior-footage-220690764_iconl.jpeg">
                        <div class="content"><strong>Personal Care</strong>
                            <div>Soaps, toothpaste, lotion…</div>
                        </div>
                    </a>
                    <a class="card" href="#order"><img alt="Cleaning" src="https://thumbs.dreamstime.com/z/cleaning-products-supermarket-23731231.jpg?w=768">
                        <div class="content"><strong>Home & Cleaning</strong>
                            <div>Detergents, tissues…</div>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- Best sellers carousel -->
        <section>
            <div class="container carousel">
                <h3 class="section-title">This week's best sellers</h3>
                <div class="nav">
                    <button class="icon-btn" id="prevBtn" aria-label="Previous">◀</button>
                    <button class="icon-btn" id="nextBtn" aria-label="Next">▶</button>
                </div>
                <div class="track" id="track">
                    <div class="item card"><img alt="Cooking oil" src="https://ug.jumia.is/unsafe/fit-in/300x300/filters:fill(white)/product/38/99528/1.jpg?3654">
                        <div class="content"><strong>Cooking Oil 3L</strong>
                            <div>Promo price • while stocks last</div>
                        </div>
                    </div>
                    <div class="item card"><img alt="Sugar" src="https://ug.jumia.is/unsafe/fit-in/680x680/filters:fill(white)/product/72/750331/1.jpg?9730">
                        <div class="content"><strong>Sugar 1kg</strong>
                            <div>High demand</div>
                        </div>
                    </div>
                    <div class="item card"><img alt="Beans" src="https://totco.co.ug/wp-content/uploads/2023/01/beans.jpg">
                        <div class="content"><strong>Beans 1kg</strong>
                            <div>Fresh stock</div>
                        </div>
                    </div>
                    <div class="item card"><img alt="Rice" src="https://tse4.mm.bing.net/th/id/OIP.7V7PgTWVBBzeC9LZNfzbQwHaHa?cb=12&w=700&h=700&rs=1&pid=ImgDetMain&o=7&rm=3">
                        <div class="content"><strong>Rice 5kg</strong>
                            <div>Customer favourite</div>
                        </div>
                    </div>
                    <div class="item card"><img alt="Toothpaste" src="https://tse1.mm.bing.net/th/id/OIP.r0cjQFctx3GewO39qSao1gHaHa?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3">
                        <div class="content"><strong>Toothpaste</strong>
                            <div>Multi‑buy deal</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA strip -->
        <section>
            <div class="container">
                <div class="cta">
                    <div>
                        <strong>Need something now?</strong>
                        <div>Message us on WhatsApp and we’ll prepare your order.</div>
                    </div>
                    <div class="row">
                        <a class="btn" style="color: black;" href="https://wa.me/256746825914" target="_blank" rel="noopener">WhatsApp: +256 746 825 914</a>
                        <a class="btn" style="color: black;" href="https://wa.me/256781988570" target="_blank" rel="noopener">WhatsApp: +256 781 988 570</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- About / Testimonials -->
        <section class="about-section">
            <div class="container">
                <header class="about-head">
                    <span class="about-badge">Since day one</span>
                    <h3 class="about-title">
                        About <span class="brand-grad">ANN RETAIL SHOP</span>
                    </h3>
                    <p class="about-desc">
                        We serve the neighborhood with fresh stock, neat shelves, and friendly prices — making
                        everyday shopping fast and stress-free.
                    </p>
                </header>

                <div class="about-grid">
                    <!-- Testimonials -->
                    <article class="about-card">
                        <div class="stars" aria-label="5 star rating">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span class="half">★</span>
                        </div>
                        <h4 class="card-title">Customer Reviews</h4>
                        <p class="quote">“Clean shop, fair prices, and the staff are kind.” <em>— Sarah K.</em></p>
                        <p class="quote">“Always find what I need. Quick checkout.” <em>— Moses B.</em></p>
                        <div class="trust-row">
                            <span class="pill soft">Over 1,000 happy shoppers</span>
                            <span class="pill soft">4.8 / 5 rating</span>
                        </div>
                    </article>

                    <!-- Highlights -->
                    <article class="about-card">
                        <h4 class="card-title">Store Highlights</h4>
                        <ul class="icon-list">
                            <li>
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M4 3h16v4H4zM6 7v14h12V7" />
                                </svg>
                                Open daily <strong>7:00am – 9:30pm</strong>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                                Fast checkout • Mobile Money accepted
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M3 7h18M6 7v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7" />
                                </svg>
                                Local delivery for WhatsApp orders
                            </li>
                        </ul>
                    </article>

                    <!-- Brands -->
                    <article class="about-card">
                        <h4 class="card-title">Brands You Love</h4>
                        <p>We stock trusted brands across groceries, beverages, home care, and personal care.</p>
                        <div class="brand-chips">
                            <span class="chip">Premium rice</span>
                            <span class="chip">Top oils</span>
                            <span class="chip">Quality soaps</span>
                            <span class="chip">Fresh beverages</span>
                        </div>
                    </article>

                    <!-- Promise -->
                    <article class="about-card">
                        <h4 class="card-title">Quality Promise</h4>
                        <p>If any product isn’t right, return it within <strong>48 hours</strong> with receipt for an exchange.</p>
                        <div class="guarantee">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6 9 17l-5-5" />
                            </svg>
                            Satisfaction guaranteed
                        </div>
                    </article>
                </div>
            </div>
        </section>


        <!-- Order & Contact -->
        <section id="order">
            <div class="container">
                <h3 class="section-title">Order & Contact</h3>
                <p class="section-desc">Reach us by phone, WhatsApp, or the contact form. We respond quickly during opening hours.</p>
                <div class="contact" id="contact">
                    <form method="post" action="/contact.php" id="contactForm" novalidate>
                        <div class="row" style="margin-bottom:10px">
                            <span class="tag">Call: +256 746 825 914</span>
                            <span class="tag">Call: +256 781 988 570</span>
                            <span class="tag">Email: nabukeeraannet2@gmail.com</span>
                        </div>
                        <div class="field">
                            <label for="name">Your name</label>
                            <input id="name" name="name" type="text" required placeholder="Name Here" />
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

                    <!-- Replace src with your map or coordinates if you prefer -->
                    <iframe class="map" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps?q=0.288835138082504,32.6322174072266&z=15&output=embed"></iframe>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container" style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap">
            <div>© <span id="year"></span> ANN RETAIL SHOP • All rights reserved.</div>
            <div class="row">
                <a class="btn ghost" href="https://wa.me/256746825914" target="_blank" rel="noopener">WhatsApp</a>
                <a class="btn ghost" href="tel:+256746825914">Call</a>
                <a class="btn ghost" href="mailto:phillipsein6@gmail.com">Email</a>
            </div>
        </div>
    </footer>

    <!-- JSON‑LD: LocalBusiness -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Store",
            "name": "ANN RETAIL SHOP",
            "url": "https://annretailshop.com/",
            "image": "https://images.unsplash.com/photo-1585386959984-a41552231658?q=80&w=1200&auto=format&fit=crop",
            "telephone": "+256746825914",
            "email": "sales@annretailshop.philltechs.com",
            "address": {
                "@type": "PostalAddress",
                "addressCountry": "UG"
            },
            "openingHours": "Mo-Su 07:00-21:30",
            "sameAs": [
                "https://wa.me/256746825914",
                "https://wa.me/256781988570"
            ]
        }
    </script>

    <script>
        // Year
        document.getElementById('year').textContent = new Date().getFullYear();

        // Simple carousel controls
        const track = document.getElementById('track');
        document.getElementById('prevBtn').addEventListener('click', () => track.scrollBy({
            left: -320,
            behavior: 'smooth'
        }));
        document.getElementById('nextBtn').addEventListener('click', () => track.scrollBy({
            left: 320,
            behavior: 'smooth'
        }));

        // Form validation & UX
        const form = document.getElementById('contactForm');
        form.addEventListener('submit', (e) => {
            const valid = form.checkValidity();
            if (!valid) {
                e.preventDefault();
                alert('Please fill in the required fields correctly.');
            }
        });

        // Fade-in on view
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('shadow-pop');
                    io.unobserve(e.target);
                }
            })
        }, {
            threshold: .14
        });
        document.querySelectorAll('.feature,.card,.cta').forEach(el => io.observe(el));
    </script>
</body>

</html>

<!-- =========================
     contact.php (place at your web root)
     - Handles the contact form and emails the message to phillipsein6@gmail.com
     - Requires PHP 7.4+ (Hostinger is OK). Uses built‑in mail() or PHPMailer (recommended).
   ========================= -->
<?php /*
<?php
// contact.php — basic, secure handler
// Update these:
$TO_EMAILS = ['phillipsein6@gmail.com','nabukeeraannet2@gmail.com'];
$FROM_EMAIL = 'no-reply@annretailshop.com'; // Set this to an email on your domain (improves deliverability)
$SITE_NAME  = 'ANN RETAIL SHOP';

// Simple rate limit + spam trap
session_start();
if (!empty($_POST['company'])) { http_response_code(400); exit('Bad request'); } // honeypot
if (isset($_SESSION['last_submit']) && time() - $_SESSION['last_submit'] < 20) { http_response_code(429); exit('Too many requests, try later.'); }
$_SESSION['last_submit'] = time();

// Validate
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');
if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message) {
  http_response_code(400); exit('Invalid input');
}

$subject = "New inquiry from $SITE_NAME website";
$body    = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message\n\n--\n$SITE_NAME";
$headers = [
  'From' => "$SITE_NAME <$FROM_EMAIL>",
  'Reply-To' => $email,
  'Content-Type' => 'text/plain; charset=UTF-8'
];

// Option A: native mail()
$okAll = true;
foreach ($TO_EMAILS as $rcpt) {
  $okAll = mail($rcpt, $subject, $body, $headers) && $okAll;
}
$ok = $okAll;

// Option B (recommended): PHPMailer with Titan/SMTP
// Uncomment and configure if you use Titan Email/Hostinger SMTP for higher deliverability.
/*
use PHPMailer\PHPMailer\PHPMailer; use PHPMailer\PHPMailer\SMTP; use PHPMailer\PHPMailer\Exception;
require __DIR__.'/vendor/autoload.php';
$mail = new PHPMailer(true);
try {
  $mail->isSMTP();
  $mail->Host = 'smtp.titan.email';
  $mail->SMTPAuth = true;
  $mail->Username = 'no-reply@annretailshop.com'; // mailbox on your domain
  $mail->Password = 'YOUR_SMTP_PASSWORD';
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port = 587;
  $mail->setFrom('no-reply@annretailshop.com', $SITE_NAME);
  $mail->addAddress('phillipsein6@gmail.com');
  $mail->addAddress('nabukeeraannet2@gmail.com');
  $mail->addReplyTo($email, $name);
  $mail->Subject = $subject;
  $mail->Body    = $body;
  $ok = $mail->send();
} catch (Exception $e) { $ok=false; }
*/

// Redirect back with status
if ($ok) {
    header('Location: /?ok=1#contact');
} else {
    header('Location: /?ok=0#contact');
}
exit;
?>
*/ ?>