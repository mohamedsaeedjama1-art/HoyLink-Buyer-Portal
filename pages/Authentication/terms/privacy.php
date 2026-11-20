<?php
// privacy.php - Xog-Qarsoodiga (Privacy Policy) - Modern Redesign
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xog-Qarsoodiga | HoyLink - Hargeisa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0f0c29 0%, #1a1a3e 50%, #0f0c29 100%);
            color: #ffffff;
            min-height: 100vh;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.15) 0%, transparent 50%);
            z-index: -1;
        }

        /* Header Modern */
        header {
            background: rgba(15, 12, 41, 0.8);
            backdrop-filter: blur(20px);
            padding: 20px 40px;
            border-bottom: 1px solid rgba(102, 126, 234, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .logo-text h1 {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(90deg, #ffffff, #a5b4fc);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .logo-text p {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            padding: 8px 16px;
            border-radius: 10px;
        }

        .nav-links a:hover {
            background: rgba(102, 126, 234, 0.2);
            color: #a5b4fc;
        }

        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 30px;
        }

        /* Hero Section */
        .hero-section {
            text-align: center;
            margin-bottom: 50px;
        }

        .hero-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea20, #764ba220);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            color: #a5b4fc;
            border: 1px solid rgba(102, 126, 234, 0.3);
            margin-bottom: 20px;
        }

        .hero-section h1 {
            font-size: 48px;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff, #a5b4fc);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 15px;
        }

        .hero-section p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Two Column Layout */
        .two-columns {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }

        /* Sidebar */
        .sidebar {
            flex: 1;
            min-width: 260px;
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .sidebar-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(102, 126, 234, 0.5);
            display: inline-block;
        }

        .toc-list {
            list-style: none;
        }

        .toc-list li {
            margin-bottom: 12px;
        }

        .toc-list a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toc-list a i {
            font-size: 12px;
            color: #667eea;
        }

        .toc-list a:hover {
            color: #a5b4fc;
            transform: translateX(5px);
        }

        /* Main Content */
        .main-content {
            flex: 3;
            min-width: 300px;
        }

        .policy-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(10px);
            border-radius: 28px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.3s ease;
        }

        .policy-card:hover {
            border-color: rgba(102, 126, 234, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .section-block {
            margin-bottom: 40px;
            scroll-margin-top: 100px;
        }

        .section-block h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #a5b4fc;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-block h2 i {
            font-size: 28px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .section-block h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 20px 0 12px 0;
            color: #c4b5fd;
        }

        .section-block p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.7;
            margin-bottom: 15px;
        }

        .section-block ul {
            margin: 15px 0 20px 25px;
            color: rgba(255, 255, 255, 0.8);
        }

        .section-block li {
            margin: 8px 0;
            line-height: 1.6;
        }

        .section-block strong {
            color: #a5b4fc;
        }

        .info-box {
            background: linear-gradient(135deg, #667eea10, #764ba210);
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 16px;
            margin: 20px 0;
        }

        .info-box p {
            margin-bottom: 0;
        }

        /* Footer */
        footer {
            background: rgba(15, 12, 41, 0.9);
            backdrop-filter: blur(10px);
            padding: 40px 30px 30px;
            margin-top: 60px;
            border-top: 1px solid rgba(102, 126, 234, 0.2);
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            text-align: center;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: #a5b4fc;
        }

        .copyright {
            color: rgba(255, 255, 255, 0.4);
            font-size: 13px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .two-columns {
                flex-direction: column;
            }
            
            .sidebar {
                position: static;
                margin-bottom: 30px;
            }
            
            .hero-section h1 {
                font-size: 36px;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 20px;
            }
            
            .policy-card {
                padding: 25px;
            }
            
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .section-block h2 {
                font-size: 1.4rem;
            }
            
            .hero-section h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="header-content">
        <div class="logo-area">
            <div class="logo-icon">🏠</div>
            <div class="logo-text">
                <h1>HoyLink</h1>
                <p>Property Management System | Hargeisa</p>
            </div>
        </div>
        <div class="nav-links">
           
            <a href="term1.php"><i class="fas fa-file-contract"></i> Shuruudaha</a>
        </div>
    </div>
</header>

<div class="container">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-badge">
            <i class="fas fa-shield-alt"></i> Ilaalinta Xogtaada
        </div>
        <h1>Xog-Qarsoodiga <span style="color:#667eea;">(Privacy Policy)</span></h1>
        <p>Sida aan u ilaalinno oo u isticmaalno xogtaada shakhsiyeed</p>
        <div style="margin-top: 15px; font-size: 14px; color: rgba(255,255,255,0.5);">
            <i class="fas fa-calendar-alt"></i> Wax ku ool: 28/04/2026 | 
            <i class="fas fa-map-marker-alt"></i> Hargeisa, Somaliland
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="two-columns">
        <!-- Sidebar - Table of Contents -->
        <aside class="sidebar">
            <div class="sidebar-card">
                <div class="sidebar-title">Qaybaha</div>
                <ul class="toc-list">
                    <li><a href="#sec1"><i class="fas fa-database"></i> Xogta aan soo ururinno</a></li>
                    <li><a href="#sec2"><i class="fas fa-chart-line"></i> Isticmaalka xogta</a></li>
                    <li><a href="#sec3"><i class="fas fa-share-alt"></i> Kala qaybinta</a></li>
                    <li><a href="#sec4"><i class="fas fa-image"></i> Sawirada guryaha</a></li>
                    <li><a href="#sec5"><i class="fas fa-cookie-bite"></i> Cookies</a></li>
                    <li><a href="#sec6"><i class="fas fa-child"></i> Xogta carruurta</a></li>
                    <li><a href="#sec7"><i class="fas fa-user-check"></i> Xaqa xogtaada</a></li>
                    <li><a href="#sec8"><i class="fas fa-lock"></i> Ilaalinta amniga</a></li>
                    <li><a href="#sec9"><i class="fas fa-sync-alt"></i> Isbeddelada</a></li>
                </ul>
            </div>

            <div class="sidebar-card" style="margin-top: 20px;">
                <div class="sidebar-title">Xiriir</div>
                <ul class="toc-list">
                    <li><i class="fas fa-envelope"></i> privacy@hoylink.so</li>
                    <li><i class="fas fa-phone"></i> +252 63 1234567</li>
                    <li><i class="fas fa-map-marker-alt"></i> Hargeisa, Somaliland</li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="policy-card">
                <!-- Section 1 -->
                <div id="sec1" class="section-block">
                    <h2><i class="fas fa-database"></i> 1. Noocyada Xogta aan Soo Ururinno</h2>
                    <p>Markaad isticmaalayso <strong>HoyLink Property Management System</strong> (guri-doon ama mulkiile), waxaan soo ururin karnaa:</p>
                    <ul>
                        <li><strong>Macluumaadka Aasaasiga:</strong> Magaca, emailka, lambarka taleefanka.</li>
                        <li><strong>Xogta Guriga (Mulkiilaha):</strong> goobta guriga (ciwaan, maps), sawirada guriga, qiimaha kirista/ibista, sharrax.</li>
                        <li><strong>Hawlaha Guri-doonka:</strong> guryaha la daawaday, codsiyada la sameeyay, fariimaha loo diray mulkiilayaasha.</li>
                    </ul>
                </div>

                <!-- Section 2 -->
                <div id="sec2" class="section-block">
                    <h2><i class="fas fa-chart-line"></i> 2. Sidee Loo Isticmaalayaa Xogtaada?</h2>
                    <p>Xogta waxaa loo adeegsadaa:</p>
                    <ul>
                        <li>In lagu suurto-geliyo isku xidhka tooska ah ee u dhexeeya guri-doonka & mulkiilaha.</li>
                        <li>In lagu hagaajiyo habka baadhista guryaha (search & filter).</li>
                        <li>In lagu ilaaliyo amniga nidaamka lana hortago khiyaano ama been abuur.</li>
                        <li>In laga soo saaro tirakoobyada (statistics) iyo horumarinta adeegga.</li>
                    </ul>
                </div>

                <!-- Section 3 -->
                <div id="sec3" class="section-block">
                    <h2><i class="fas fa-share-alt"></i> 3. Kala Qaybinta Xogta Dadka Saddexaad</h2>
                    <p>Lama iibin doono ama loo ijaarin doono xogtaada shakhsiyeed cid saddexaad ah oo aan la xiriirin adeegga. Si kastaba:</p>
                    <ul>
                        <li>Mulkiilaha guriga ayaa arki doona magaca & taleefanka guri-doonka markuu codsi diro.</li>
                        <li>Guri-doonka ayaa arka xogta mulkiilaha (sida magaca ama taleefanka) marka uu danaynayo guri gaar ah.</li>
                    </ul>
                </div>

                <!-- Section 4 -->
                <div id="sec4" class="section-block">
                    <h2><i class="fas fa-image"></i> 4. Sawirada Guryaha & Xogta Guud</h2>
                    <p>Mulkiilahu wuxuu ikhtiyaar u leeyahay inuu sawirada gurigiisa ku daro. Sawiradan waxaa arki kara dhamaan booqdayaasha nidaamka. Ha soo gelinin sawirro ay ku jiraan qoyskaaga, xog xasaasi ah, ama wax ka hor imanaya diinta/dhaqanka Somaliland.</p>
                </div>

                <!-- Section 5 -->
                <div id="sec5" class="section-block">
                    <h2><i class="fas fa-cookie-bite"></i> 5. Cookies & Teknoolojiyada</h2>
                    <p>Waxaan isticmaalnaa cookies si loogu xasuusto qalabkaaga (device), loo fududeeyo login-ka, loona soo celiyo baadhistaada hore. Waad diidi kartaa cookies-ka browser settings-ka, lkn qaar ka mid ah hawlaha systemku ma shaqeyn doonaan si sax ah.</p>
                </div>

                <!-- Section 6 -->
                <div id="sec6" class="section-block">
                    <h2><i class="fas fa-child"></i> 6. Xogta Carruurta</h2>
                    <p>Nidaamka looma talagalin qof da'diisu ka yar tahay 18 sano. Haddii aan ogaanno in xog laga soo ururiyay qof da' yar oo aan ogolaansho waalid lahayn, xogtaas waa la tirtiri doonaa isla markiiba.</p>
                </div>

                <!-- Section 7 -->
                <div id="sec7" class="section-block">
                    <h2><i class="fas fa-user-check"></i> 7. Xaqa Aad U Leedahay Xogtaada</h2>
                    <p>Adiga ayaa leh xogtaada. Waa xaqaagu:</p>
                    <ul>
                        <li>Inaad tirtirto koontadaada iyo xogta ku jirta.</li>
                        <li>Inaad wax ka beddesho profile-gaaga (magaca, taleefanka).</li>
                        <li>Inaad codsato koobi (copy) xogta kaydsan.</li>
                    </ul>
                    <div class="info-box">
                        <p><i class="fas fa-envelope"></i> Fadlan nala soo xiriir: <strong>privacy@hoylink.so</strong> ama cinwaankan: Hargeisa, Somaliland.</p>
                    </div>
                </div>

                <!-- Section 8 -->
                <div id="sec8" class="section-block">
                    <h2><i class="fas fa-lock"></i> 8. Ilaalinta Xogta (Security)</h2>
                    <p>Waxaan isticmaalnaa habab casri ah: encryption-ka xogta, firewalls, iyo siyaabo kale oo looga hortago gelitaan sharci darro ah. Si kastaba, internet-ka cid kama dammaanad qaadi karto 100% ammaan. Fadlan password-kaaga ku hayso sir, kana ilaali dadka kale.</p>
                </div>

                <!-- Section 9 -->
                <div id="sec9" class="section-block">
                    <h2><i class="fas fa-sync-alt"></i> 9. Isbeddelka Qarsoodiga</h2>
                    <p>Waxaan beddeli karnaa siyaasaddan waqti kasta. Waxaan ku dhejin doonaa boggan isbeddelka, taariikhda cusabna waa la muujin doonaa. Isticmaalka sii wata waxaad ogolaanaysaa siyaasadda cusub.</p>
                </div>

                <hr style="margin: 30px 0; border-color: rgba(255,255,255,0.1);">

                <div class="info-box">
                    <p><i class="fas fa-check-circle"></i> <strong>Waafaqsan Qawaaniinta Somaliland</strong> – Ku xiran Xeerka Ilaalinta Xogta.</p>
                </div>
            </div>
        </main>
    </div>
</div>

<footer>
    <div class="footer-content">
        <div class="footer-links">
            <a href="../register.php">🏠 Dashboard</a>
            <a href="../register.php">🔍 Guryaha</a>
            <a href="terms/term1.php">📜 Shuruudaha Adeegga</a>
            <a href="contact.php">📞 Nala Soo Xiriir</a>
        </div>
        <div class="copyright">
            © 2026 HoyLink - Hel Gurigaagi Riyada. All rights reserved.<br>
            Hargeisa, Somaliland | Property Management System
        </div>
    </div>
</footer>

<!-- Smooth Scroll -->
<script>
    document.querySelectorAll('.toc-list a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if(targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
</script>

</body>
</html>