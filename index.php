<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>HoyLink — Luxury Living</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        /* ───────── RESET ───────── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #0b1120;
            color: #f1f5f9;
            min-height: 100vh;
            overflow-x: hidden;
        }
        a { text-decoration: none; color: inherit; }

        /* ───────── ANIMATED BG ───────── */
        .bg-glow {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .bg-glow::before,
        .bg-glow::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            filter: blur(140px);
            opacity: 0.25;
            animation: floatGlow 16s ease-in-out infinite alternate;
        }
        .bg-glow::before {
            top: -180px;
            left: -180px;
            background: #2a4b8c;
        }
        .bg-glow::after {
            bottom: -180px;
            right: -180px;
            background: #6d28d9;
            animation-delay: -5s;
        }
        @keyframes floatGlow {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(60px, 50px) scale(1.2); }
        }

        .wrapper {
            position: relative;
            z-index: 1;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px 50px;
        }

        /* ───────── HEADER WITH IMAGE ───────── */
        .header {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            margin-top: 16px;
            min-height: 260px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 32px 40px;
            flex-wrap: wrap;
            gap: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }
        .header-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
            background: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTCYTDMvnSw6z9sNnzG4kDrMN8N_DO3X7LvTZBINbH8kuo6Zc21rWMv04ls&s=10') center/cover no-repeat;
            filter: brightness(0.40) saturate(1.1);
            transition: filter 0.4s ease;
        }
        .header-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(11, 17, 32, 0.7), rgba(30, 41, 59, 0.4));
        }
        .header:hover .header-bg {
            filter: brightness(0.50) saturate(1.2);
        }

        .header-content {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            flex-wrap: wrap;
            gap: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .logo-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #2a4b8c, #6d28d9);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 8px 24px rgba(42, 75, 140, 0.3);
        }
        .logo h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(90deg, #fff, #a5b4fc);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .logo span {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            display: block;
            margin-top: -2px;
        }

        .nav-actions {
            display: flex;
            gap: 14px;
        }
        .btn-outline {
            padding: 10px 26px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 14px;
            color: #f1f5f9;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(6px);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }
        .btn-primary {
            padding: 10px 30px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 14px;
            color: #fff;
            background: linear-gradient(135deg, #2a4b8c, #6d28d9);
            border: none;
            box-shadow: 0 8px 24px rgba(42, 75, 140, 0.3);
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(42, 75, 140, 0.4);
        }

        /* ───────── HERO TEXT ───────── */
        .hero-text {
            text-align: center;
            margin: 40px 0 30px;
            padding: 0 20px;
        }
        .hero-text h2 {
            font-size: clamp(1.8rem, 4vw, 3.2rem);
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .hero-text h2 .highlight {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .hero-text p {
            margin-top: 12px;
            color: #94a3b8;
            font-size: 1.05rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
        }

        /* ───────── 3D HOME ICON ───────── */
        .icon-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 10px 0 40px;
            perspective: 800px;
        }

        .home-icon-3d {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 20px 32px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            cursor: pointer;
            transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            transform-style: preserve-3d;
            transform: rotateY(0deg) rotateX(0deg) translateZ(0);
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .home-icon-3d i {
            font-size: 56px;
            transition: all 0.6s ease;
            color: #fbbf24;
            text-shadow: 0 0 30px rgba(251, 191, 36, 0.1);
        }
        .home-icon-3d .label {
            font-size: 14px;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 0.5px;
            transition: color 0.4s ease;
            text-transform: uppercase;
        }
        .home-icon-3d .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #334155;
            transition: all 0.4s ease;
            margin-top: 4px;
        }

        /* ───────── 3D HOVER & ANIMATION ───────── */
        .home-icon-3d:hover {
            transform: rotateY(18deg) rotateX(10deg) translateZ(30px) scale(1.08);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(251, 191, 36, 0.25);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4), 0 0 60px rgba(251, 191, 36, 0.05);
        }
        .home-icon-3d:hover i {
            color: #fbbf24;
            text-shadow: 0 0 50px rgba(251, 191, 36, 0.2);
            transform: translateZ(15px) scale(1.05);
        }
        .home-icon-3d:hover .label {
            color: #f1f5f9;
        }
        .home-icon-3d:hover .dot {
            background: #fbbf24;
            box-shadow: 0 0 20px rgba(251, 191, 36, 0.4);
        }

        /* ───────── FLOATING ANIMATION ───────── */
        .home-icon-3d {
            animation: float3D 4s ease-in-out infinite;
        }

        @keyframes float3D {
            0% {
                transform: rotateY(0deg) rotateX(0deg) translateY(0px);
            }
            25% {
                transform: rotateY(8deg) rotateX(4deg) translateY(-10px);
            }
            50% {
                transform: rotateY(0deg) rotateX(0deg) translateY(0px);
            }
            75% {
                transform: rotateY(-8deg) rotateX(-4deg) translateY(-10px);
            }
            100% {
                transform: rotateY(0deg) rotateX(0deg) translateY(0px);
            }
        }

        /* ───────── IMAGE CARDS ───────── */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            margin-top: 10px;
        }
        @media (max-width: 920px) {
            .card-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 580px) {
            .card-grid { grid-template-columns: 1fr; }
        }

        .property-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
            cursor: pointer;
            position: relative;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }
        .property-card:hover {
            transform: translateY(-12px);
            border-color: rgba(255, 255, 255, 0.12);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.4);
        }
        .property-card .img-wrap {
            overflow: hidden;
            aspect-ratio: 16 / 10;
            position: relative;
        }
        .property-card .img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s ease;
            display: block;
        }
        .property-card:hover .img-wrap img {
            transform: scale(1.06);
        }
        .property-card .card-body {
            padding: 20px 22px 26px;
        }
        .property-card .card-body h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #fff;
            letter-spacing: -0.3px;
        }
        .property-card .card-body p {
            font-size: 14px;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 12px;
        }
        .property-card .card-body .tag {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(251, 191, 36, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.1);
        }

        /* ───────── FOOTER ───────── */
        .footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            color: #64748b;
            font-size: 14px;
        }
        .footer-socials {
            display: flex;
            gap: 18px;
        }
        .footer-socials a {
            color: #94a3b8;
            transition: color 0.3s ease;
            font-size: 18px;
        }
        .footer-socials a:hover {
            color: #f1f5f9;
        }

        /* ───────── RESPONSIVE ───────── */
        @media (max-width: 720px) {
            .header {
                padding: 24px 20px;
                min-height: 200px;
            }
            .header-content {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            .nav-actions {
                justify-content: center;
                flex-wrap: wrap;
            }
            .logo {
                justify-content: center;
            }
            .home-icon-3d i {
                font-size: 40px;
            }
            .home-icon-3d {
                padding: 16px 24px;
            }
        }
        @media (max-width: 480px) {
            .card-grid {
                gap: 18px;
            }
            .property-card .card-body h3 {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<!-- ─── BG GLOW ─── -->
<div class="bg-glow"></div>

<div class="wrapper">

    <!-- ─── HEADER WITH IMAGE ─── -->
    <header class="header">
        <div class="header-bg"></div>
        <div class="header-content">
            <div class="logo">
                <div class="logo-icon">🏠</div>
                <div>
                    <h1>HoyLink</h1>
                    <span>Premium Real Estate</span>
                </div>
            </div>
            <div class="nav-actions">
                <a href="./pages/Authentication/login.php" class="btn-outline">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="./pages/Authentication/register.php" class="btn-primary">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            </div>
        </div>
    </header>

    <!-- ─── HERO TEXT ─── -->
    <div class="hero-text">
        <h2>
         <span class="highlight">HOYLINK</span> <br />
            HARGEISA SOMALILAND
        </h2>
        <p>
            Explore premium properties with stunning views, modern amenities,
            and unparalleled comfort. Your dream home is just a click away.
        </p>
    </div>

    <!-- ─── 3D HOME ICON (KELIYA) ─── -->
    <div class="icon-container">
        <div class="home-icon-3d" onclick="window.location.href='login.php'">
            <i class="fas fa-home"></i>
            <span class="label">Home</span>
            <span class="dot"></span>
        </div>
    </div>

    <!-- ─── IMAGE CARDS ─── -->
    <div class="card-grid" id="cardGrid">

        <!-- Card 1 -->
        <div class="property-card" onclick="window.location.href='login.php'">
            <div class="img-wrap">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTCYTDMvnSw6z9sNnzG4kDrMN8N_DO3X7LvTZBINbH8kuo6Zc21rWMv04ls&s=10" alt="Luxury Villa" loading="lazy" />
            </div>
            <div class="card-body">
                <h3>🏡 Luxury Villa</h3>
                <p>Experience modern elegance with panoramic views, private pool, and state-of-the-art amenities.</p>
                <span class="tag">✨ Premium Living</span>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="property-card" onclick="window.location.href='login.php'">
            <div class="img-wrap">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ38CtpoERCuwsx9sNmN728Pl8g1T68_UBB0yl4SILQ-ZoiMXqy1LusE7U9&s=10" alt="Modern Apartment" loading="lazy" />
            </div>
            <div class="card-body">
                <h3>🏙️ Modern Apartment</h3>
                <p>Contemporary design with open-concept layout, smart home technology, and city views.</p>
                <span class="tag">⚡ Smart Living</span>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="property-card" onclick="window.location.href='login.php'">
            <div class="img-wrap">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR9j09YA8UMZFeuPguAw7cQo2ic1Is5aGCovSJv1LSwqvjYqcxd7Z-ajBfY&s=10" alt="Penthouse Suite" loading="lazy" />
            </div>
            <div class="card-body">
                <h3>🌅 Penthouse Suite</h3>
                <p>Sky-high living with floor-to-ceiling windows, private terrace, and exclusive access.</p>
                <span class="tag">🌟 Sky Living</span>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="property-card" onclick="window.location.href='login.php'">
            <div class="img-wrap">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTRr2G72GNECQJF3BlwxFKIWFsZuEHOT9dFAbDOGyzvO3K-_MH5xAnNTOb6&s=10" alt="Elegant Interior" loading="lazy" />
            </div>
            <div class="card-body">
                <h3>🛋️ Elegant Interior</h3>
                <p>Designed with premium materials, warm tones, and carefully curated spaces for comfort.</p>
                <span class="tag">🎨 Interior Luxury</span>
            </div>
        </div>

        <!-- Card 5 -->
        <div class="property-card" onclick="window.location.href='login.php'">
            <div class="img-wrap">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTCYTDMvnSw6z9sNnzG4kDrMN8N_DO3X7LvTZBINbH8kuo6Zc21rWMv04ls&s=10" alt="Beachfront Villa" loading="lazy" />
            </div>
            <div class="card-body">
                <h3>🏖️ Beachfront Villa</h3>
                <p>Wake up to ocean views, private beach access, and sunset skies every evening.</p>
                <span class="tag">🌊 Ocean View</span>
            </div>
        </div>

        <!-- Card 6 -->
        <div class="property-card" onclick="window.location.href='login.php'">
            <div class="img-wrap">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ38CtpoERCuwsx9sNmN728Pl8g1T68_UBB0yl4SILQ-ZoiMXqy1LusE7U9&s=10" alt="Poolside Paradise" loading="lazy" />
            </div>
            <div class="card-body">
                <h3>🏊 Poolside Paradise</h3>
                <p>Luxury outdoor living with infinity pool, landscaped gardens, and entertainment areas.</p>
                <span class="tag">🌴 Resort Style</span>
            </div>
        </div>

    </div>

    <!-- ─── FOOTER ─── -->
    <footer class="footer">
        <span>© 2026 HoyLink — Luxury Real Estate</span>
        <div class="footer-socials">
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
            <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </footer>

</div>

</body>
</html>