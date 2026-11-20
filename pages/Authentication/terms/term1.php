<?php
// terms.php - Shuruudaha Adeegga
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shuruudaha Adeegga | Property Management - Hargeisa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.85), rgba(0, 0, 0, 0.92)),
                        url('https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat fixed;
            color: #ffffff;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(30, 60, 114, 0.2) 0%, rgba(42, 82, 152, 0.15) 100%);
            z-index: -1;
        }

        /* Header - Centered */
        header {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            text-align: center;
        }

        .logo-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .logo-icon {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto;
        }

        .logo-text h1 {
            font-size: 26px;
            font-weight: 700;
            background: linear-gradient(90deg, #fff, #a5b4fc);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .logo-text span {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Policy Card */
        .policy-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: transform 0.3s ease;
        }

        .policy-card:hover {
            border-color: rgba(102, 126, 234, 0.3);
        }

        .badge {
            background: linear-gradient(135deg, #667eea20, #764ba220);
            display: inline-block;
            padding: 6px 18px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            color: #a5b4fc;
            margin-bottom: 25px;
            border: 1px solid rgba(102, 126, 234, 0.3);
        }

        .policy-card h2 {
            font-size: 1.6rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #a5b4fc;
            border-left: 4px solid #667eea;
            padding-left: 18px;
        }

        .policy-card h2:first-of-type {
            margin-top: 0;
        }

        .policy-card h3 {
            font-size: 1.2rem;
            margin: 1.5rem 0 0.8rem;
            color: #c4b5fd;
        }

        .policy-card p {
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
        }

        .policy-card ul, .policy-card ol {
            margin: 0.8rem 0 1.2rem 1.8rem;
            color: rgba(255, 255, 255, 0.85);
        }

        .policy-card li {
            margin: 0.5rem 0;
        }

        .policy-card strong {
            color: #a5b4fc;
        }

        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
            padding: 25px;
            text-align: center;
            margin-top: 50px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-content {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        /* No tabs anymore, so remove related styles and add responsive */
        @media (max-width: 768px) {
            header {
                padding: 20px;
            }
            
            .policy-card {
                padding: 25px;
            }
            
            .policy-card h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo-area">
        <div class="logo-icon">🏠</div>
        <div class="logo-text">
            <h1>HoyLink</h1>
            <span>Property Management System | Hargeisa, Somaliland</span>
        </div>
    </div>
</header>

<h3 class="text-center text-warning mt-4">Shuruudaha Akhri Fadlan ⚠️</h3>

<div class="container">
    <div class="policy-card">
        <div class="badge">
            <i class="fas fa-gavel"></i> Amminta Dejinta Shuruucda | Taariikhda: 28/04/2026
        </div>

        <h2>1. Hordhac & Aqbalida Shuruudaha</h2>
        <p>Ku soo dhawoow <strong>HoyLink - Property Management System</strong> – nidaamka loogu talagalay <strong>guri-doonka (Home Seekers)</strong> iyo <strong>mulkiilayaasha guryaha (Property Owners)</strong> ee magaalada <strong>Hargeisa, Somaliland</strong>. Isticmaalka nidaamkan waxaad si buuxda u ogolaanaysaa inaad ku xiran tahay shuruudahan. Haddii aadan ogoleyn, fadlan ha isticmaalin.</p>

        <h2>2. Qeexitaanka Isticmaalayaasha</h2>
        <p><strong>2.1 Guri-doon (Home Seeker):</strong> Qofka raadinaya guri oo Booqan kara profile, baadhi kara guryaha, sameysan kara codsi ama xiriir la heli kara mulkiilaha.</p>
        <p><strong>2.2 Mulkiile Guri (Property Owner):</strong> Qofka leh guri, oo uu doonayo inuu kiro ama iibiyo. Mulkiilahu wuxuu ku dari karaa guryaha liiska, wax ka beddeli karaa, oo uu maamuli karaa xogta gurigiisa.</p>

        <h2>3. Xogta Saxda ah & Waafaqsan Sharciga</h2>
        <p>Waa in dhammaan macluumaadka la soo galiyo (sawirada, goobta guriga, qiimaha, telefoonada) ay yihiin kuwo sax ah oo aan ka hor imanayn <strong>shuruucda Somaliland</strong>. Been abuur, guri aan jirin, ama xog qaldan – masuuliyadda waxaa saaran isticmaalaha, Soo Booqday Systemka.</p>

        <h2>4. Isku xidhka Guri-doonka & Mulkiilaha</h2>
        <p>Systemku waa madal dhexdhexaad ah oo fududaysa xiriirka labada dhinac. Lama damaanad qaadinayo heshiis iibsi ku dhaca ka dhaca Gudaha ama Dibadda. Heshiis kasta oo dhex mara guri-doonka iyo mulkiilaha waa mid toos ah oo ka dhaxeeya labada Macmiil, <strong> Keliya  systemku waa isku-xidhe masuul kama aha khilaafaadka.</p> </strong>

        <h2>5. Xadgudubyada & Mamnuucidda</h2>
        <p>Mamnuuc in lagu soo galiyo xogta qarsoodiga ah ee dadka kale, khayaanada, ama u isticmaalka systemka falalka sharci darrada ah. Waxaan xuquuq u leenahay inaan hakino ama tirtirno koontada isticmaale kasta oo xadgudba.</p>

        <h2>6. Lacagaha & Qiimaynta (Fees)</h2>
        <p>Waqtigan xaadirka ah adeeggu waa <strong>bilaash</strong>. Mustaqbalka, waxaa laga yaabaa in mulkiilayaasha iyo Hoy-radiaha <strong> lagu soo rogo kharash yar </strong> oo maamul si loo hagaajiyo adeegga. Isbedel kasta waxaan ku ogeysiin doonaa 30 maalmood kahor boggan.</p>

        <h2>7. Cilad la'aan Damaanad Ma Jirto</h2>
        <p>Waxaan ku dadaalaynaa joogteynta systemka, lakin waxaa laga yaabaa inuu yimaado wakhti hoos u dhac (downtime) ama cillad farsamo. Ma masuul noqono khasaare toos ah ama dadban oo ka dhasha isticmaalka nidaamka.</p>

        <h2>8. Beddelka Shuruudahan</h2>
        <p class="text-warning">Waxaan beddeli karnaa shuruudahan waqti kasta. Beddelka wuxuu noqonayaa mid ku qoran boggan, adoo sii wata isticmaalka waxaad ogolaanaysaa beddelka cusub.</p>

        <hr style="margin: 30px 0; border-color: rgba(255,255,255,0.1);">

        <p><strong>© 2026 HoyLink – Property Management System, Hargeisa, Somaliland.</strong></p>
        <p style="font-size: 13px; margin-top: 15px;">
            <i class="fas fa-envelope"></i> support@hoylink.so | 
            <i class="fas fa-phone"></i> +252 63 00000
        </p>
    </div>
</div>

<footer>
    <div class="footer-content">
        © 2026 HoyLink - Hel Gurigaagi Riyada. All rights reserved.
        <div style="margin-top: 10px; font-size: 12px;">
            Hargeisa, Somaliland | Property Management System
        </div>
    </div>
</footer>

</body>
</html>