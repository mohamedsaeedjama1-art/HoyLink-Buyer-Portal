<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us — HoyLink | Your Trusted Property Partner</title>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    /* ============================================
       HOYLINK BRAND COLORS
       ============================================ */
    :root {
        --hoylink-primary: #2A4B8C;
        --hoylink-secondary: #FF6B35;
        --hoylink-dark: #0f172a;
        --hoylink-surface: #1e293b;
        --hoylink-success: #10b981;
        --hoylink-danger: #ef4444;
        --hoylink-warning: #f59e0b;
        --hoylink-gradient: linear-gradient(135deg, #2A4B8C 0%, #4f46e5 50%, #6366f1 100%);
        --hoylink-text: #f1f5f9;
        --hoylink-muted: #94a3b8;
        --hoylink-glass: rgba(255, 255, 255, 0.06);
        --shadow-glow: 0 0 40px rgba(42, 75, 140, 0.15);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: var(--hoylink-dark);
        color: var(--hoylink-text);
        overflow-x: hidden;
        scroll-behavior: smooth;
    }

    /* Animated Background */
    body::before {
        content: '';
        position: fixed;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: 
            radial-gradient(ellipse at 20% 50%, rgba(42, 75, 140, 0.08) 0%, transparent 60%),
            radial-gradient(ellipse at 80% 20%, rgba(255, 107, 53, 0.06) 0%, transparent 50%);
        animation: bgFloat 20s ease-in-out infinite alternate;
        z-index: 0;
    }

    @keyframes bgFloat {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(2%, 1%) rotate(2deg); }
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 10px; }
    ::-webkit-scrollbar-track { background: var(--hoylink-dark); }
    ::-webkit-scrollbar-thumb {
        background: var(--hoylink-gradient);
        border-radius: 10px;
    }

    /* ============================================
       TOP BAR - Back to Dashboard + Logo
       ============================================ */
    .top-bar {
        position: fixed;
        top: 0;
        width: 100%;
        padding: 16px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(15, 23, 42, 0.92);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        z-index: 1000;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .logo-icon {
        width: 42px;
        height: 42px;
        background: var(--hoylink-gradient);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
        color: white;
        box-shadow: 0 4px 15px rgba(42, 75, 140, 0.3);
    }

    .logo-text h2 {
        font-size: 20px;
        font-weight: 800;
        background: var(--hoylink-gradient);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        letter-spacing: -0.5px;
    }

    .logo-text p {
        font-size: 10px;
        color: var(--hoylink-muted);
        margin-top: 0px;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 24px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        color: var(--hoylink-text);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.10);
        transform: translateX(-4px);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .btn-back i {
        color: var(--hoylink-secondary);
        font-size: 16px;
    }

    /* ============================================
       HERO SECTION (Adjust for fixed top bar)
       ============================================ */
    .hero {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        overflow: hidden;
        padding: 120px 8% 60px;
        z-index: 1;
    }

    .hero-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?q=80&w=2070') center/cover;
        z-index: -2;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.92), rgba(30, 41, 59, 0.85));
        z-index: -1;
    }

    .hero-content {
        max-width: 800px;
        margin: 0 auto;
        animation: fadeInUp 1s ease;
    }

    .hero-badge {
        display: inline-block;
        background: rgba(255, 107, 53, 0.15);
        backdrop-filter: blur(10px);
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        color: var(--hoylink-secondary);
        margin-bottom: 25px;
        border: 1px solid rgba(255, 107, 53, 0.2);
    }

    .hero h1 {
        font-size: 4rem;
        font-weight: 800;
        margin-bottom: 20px;
        line-height: 1.2;
        background: var(--hoylink-gradient);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        animation: glow 3s ease-in-out infinite;
    }

    .hero h1 span {
        background: none;
        -webkit-background-clip: unset;
        background-clip: unset;
        color: var(--hoylink-secondary);
    }

    .hero p {
        font-size: 1.2rem;
        line-height: 1.8;
        color: var(--hoylink-muted);
        margin-bottom: 35px;
    }

    .hero-stats {
        display: flex;
        justify-content: center;
        gap: 50px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        background: var(--hoylink-gradient);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .stat-label {
        font-size: 14px;
        color: var(--hoylink-muted);
        margin-top: 5px;
    }

    /* ============================================
       SECTIONS
       ============================================ */
    .section {
        padding: 80px 8%;
        position: relative;
        z-index: 1;
    }

    .section-title {
        text-align: center;
        margin-bottom: 60px;
    }

    .section-title h2 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        position: relative;
        display: inline-block;
        color: var(--hoylink-text);
    }

    .section-title h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: var(--hoylink-gradient);
        border-radius: 5px;
    }

    .section-title p {
        color: var(--hoylink-muted);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 20px auto 0;
    }

    /* ============================================
       ABOUT CARDS
       ============================================ */
    .about-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    .about-card {
        background: rgba(30, 41, 59, 0.60);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px;
        padding: 40px 30px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .about-card:hover {
        transform: translateY(-15px);
        border-color: rgba(42, 75, 140, 0.4);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .card-icon {
        width: 80px;
        height: 80px;
        background: rgba(42, 75, 140, 0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        font-size: 35px;
        color: var(--hoylink-secondary);
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 107, 53, 0.1);
    }

    .about-card:hover .card-icon {
        transform: scale(1.1);
        background: rgba(255, 107, 53, 0.15);
        border-color: rgba(255, 107, 53, 0.2);
    }

    .about-card h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: var(--hoylink-text);
    }

    .about-card p {
        color: var(--hoylink-muted);
        line-height: 1.6;
    }

    /* ============================================
       MISSION & VISION
       ============================================ */
    .mv-section {
        background: rgba(30, 41, 59, 0.40);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 30px;
        margin: 40px 8%;
        padding: 60px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 40px;
        position: relative;
        z-index: 1;
    }

    .mv-card {
        text-align: center;
        padding: 30px;
    }

    .mv-icon {
        font-size: 50px;
        margin-bottom: 20px;
        display: inline-block;
        animation: float 3s ease-in-out infinite;
    }

    .mv-icon .fa-eye { color: var(--hoylink-primary); }
    .mv-icon .fa-flag-checkered { color: var(--hoylink-secondary); }

    .mv-card h3 {
        font-size: 1.8rem;
        margin-bottom: 20px;
        background: var(--hoylink-gradient);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .mv-card p {
        color: var(--hoylink-muted);
        line-height: 1.7;
        font-size: 1rem;
    }

    /* ============================================
       VALUES
       ============================================ */
    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 25px;
        margin-top: 40px;
    }

    .value-item {
        background: rgba(30, 41, 59, 0.40);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        padding: 30px 20px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .value-item:hover {
        background: rgba(42, 75, 140, 0.10);
        transform: translateY(-5px);
        border-color: rgba(42, 75, 140, 0.3);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .value-icon {
        font-size: 40px;
        margin-bottom: 15px;
        display: block;
    }

    .value-item h4 {
        font-size: 1.2rem;
        margin-bottom: 10px;
        color: var(--hoylink-text);
    }

    .value-item p {
        color: var(--hoylink-muted);
        font-size: 0.9rem;
        line-height: 1.6;
    }

    /* ============================================
       ============================================
       ✅ CONTACT SECTION (CUSUB)
       ============================================
       ============================================ */
    .contact-section {
        padding: 60px 8%;
        position: relative;
        z-index: 1;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-top: 40px;
    }

    .contact-card {
        background: rgba(30, 41, 59, 0.50);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 24px;
        padding: 40px 30px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .contact-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--hoylink-gradient);
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .contact-card:hover::before {
        opacity: 1;
    }

    .contact-card:hover {
        transform: translateY(-12px);
        border-color: rgba(99, 102, 241, 0.3);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
    }

    /* Individual contact card styles */
    .contact-card:nth-child(1) .contact-icon {
        background: linear-gradient(135deg, #D14836, #EA4335);
        box-shadow: 0 8px 25px rgba(234, 67, 53, 0.3);
    }

    .contact-card:nth-child(2) .contact-icon {
        background: linear-gradient(135deg, var(--hoylink-primary), #6366f1);
        box-shadow: 0 8px 25px rgba(42, 75, 140, 0.3);
    }

    .contact-card:nth-child(3) .contact-icon {
        background: linear-gradient(135deg, #25D366, #128C7E);
        box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
    }

    .contact-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 32px;
        color: white;
        transition: all 0.3s ease;
    }

    .contact-card:hover .contact-icon {
        transform: scale(1.08) rotate(-4deg);
    }

    .contact-card h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--hoylink-text);
        margin-bottom: 8px;
    }

    .contact-detail {
        font-size: 1.1rem;
        color: #cbd5e1;
        font-weight: 500;
        margin-bottom: 16px;
        word-break: break-word;
    }

    .contact-detail a {
        color: inherit;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .contact-detail a:hover {
        color: #fff;
    }

    .contact-action {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 28px;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.04);
        color: #94a3b8;
    }

    .contact-action:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        transform: translateY(-2px);
    }

    .contact-card:nth-child(1) .contact-action:hover {
        border-color: #EA4335;
        color: #EA4335;
    }

    .contact-card:nth-child(2) .contact-action:hover {
        border-color: var(--hoylink-primary);
        color: var(--hoylink-primary);
    }

    .contact-card:nth-child(3) .contact-action:hover {
        border-color: #25D366;
        color: #25D366;
    }

    .contact-response {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #64748b;
        margin-top: 14px;
        padding: 4px 16px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.03);
    }

    .contact-response .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--hoylink-success);
        animation: dotPulse 2s ease-in-out infinite;
        display: inline-block;
    }

    @keyframes dotPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.8); }
    }

    /* ============================================
       CTA SECTION
       ============================================ */
    .cta-section {
        background: var(--hoylink-gradient);
        margin: 40px 8% 60px;
        border-radius: 30px;
        padding: 60px;
        text-align: center;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    .cta-section h2 {
        font-size: 2.2rem;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
        color: white;
    }

    .cta-section p {
        margin-bottom: 30px;
        position: relative;
        z-index: 1;
        opacity: 0.9;
        color: rgba(255,255,255,0.9);
    }

    .cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: white;
        color: var(--hoylink-primary);
        padding: 15px 35px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    }

    .cta-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        gap: 15px;
        background: #f8fafc;
    }

    /* ============================================
       FOOTER
       ============================================ */
    .footer {
        background: rgba(15, 23, 42, 0.95);
        padding: 50px 8% 30px;
        margin-top: 60px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        position: relative;
        z-index: 1;
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-bottom: 40px;
    }

    .footer-col h4 {
        font-size: 1.2rem;
        margin-bottom: 20px;
        position: relative;
        display: inline-block;
        color: var(--hoylink-text);
    }

    .footer-col h4::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 40px;
        height: 2px;
        background: var(--hoylink-gradient);
    }

    .footer-col p {
        color: var(--hoylink-muted);
        line-height: 1.6;
        margin-top: 15px;
    }

    .footer-col a {
        color: var(--hoylink-muted);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-col a:hover {
        color: var(--hoylink-secondary);
    }

    .social-links {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .social-links a {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--hoylink-text);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        background: var(--hoylink-gradient);
        transform: translateY(-3px);
        color: white;
        box-shadow: 0 5px 15px rgba(42, 75, 140, 0.3);
    }

    .footer-bottom {
        text-align: center;
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        color: var(--hoylink-muted);
        font-size: 14px;
    }

    .footer-bottom i {
        color: var(--hoylink-secondary);
    }

    /* ============================================
       ANIMATIONS
       ============================================ */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(50px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes glow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* ============================================
       SCROLL REVEAL
       ============================================ */
    .reveal {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .reveal.active {
        opacity: 1;
        transform: translateY(0);
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 992px) {
        .top-bar { padding: 14px 20px; }
        .hero h1 { font-size: 3rem; }
        .mv-section { margin: 30px 5%; padding: 40px; }
        .cta-section { margin: 30px 5%; padding: 40px; }
        .contact-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
        .top-bar {
            flex-direction: column;
            gap: 12px;
            padding: 12px 16px;
        }
        .btn-back {
            width: 100%;
            justify-content: center;
        }
        .hero h1 { font-size: 2.5rem; }
        .hero p { font-size: 1rem; }
        .section { padding: 50px 5%; }
        .section-title h2 { font-size: 2rem; }
        .hero-stats { gap: 25px; }
        .stat-number { font-size: 1.8rem; }
        .cta-section h2 { font-size: 1.8rem; }
        .cta-section { padding: 40px 20px; }
        .mv-section { padding: 30px; }
        .logo-text h2 { font-size: 18px; }
        .contact-grid { grid-template-columns: 1fr; }
        .contact-card { padding: 30px 20px; }
    }

    @media (max-width: 480px) {
        .hero h1 { font-size: 1.8rem; }
        .about-card { padding: 30px 20px; }
        .mv-card h3 { font-size: 1.5rem; }
        .values-grid { grid-template-columns: 1fr; }
        .footer-content { grid-template-columns: 1fr; }
        .hero-stats { flex-direction: column; gap: 20px; }
        .contact-detail { font-size: 0.95rem; }
    }
</style>
</head>
<body>

<!-- ============================================
     TOP BAR - Logo + Back to Dashboard
     ============================================ -->
<div class="top-bar">
    <a href="index.php" class="logo">
        <div class="logo-icon"><i class="fas fa-building"></i></div>
        <div class="logo-text">
            <h2>HoyLink</h2>
            <!-- <p>Your Trusted Property Partner</p> -->
        </div>
    </a>
    <a href="userdashboard.php" class="btn-back">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<!-- ============================================
     HERO SECTION
     ============================================ -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="hero-badge">
            <i class="fas fa-star"></i> Trusted Since 2024
        </span>
        <h1>Welcome to <span>HoyLink</span></h1>
        <p>
            Where finding your dream home becomes an unforgettable journey. 
            We connect property seekers with owners in the most seamless, 
            secure, and innovative way possible.
        </p>
        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Happy Families</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">1000+</div>
                <div class="stat-label">Properties Listed</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">98%</div>
                <div class="stat-label">Satisfaction Rate</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support Available</div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     WHO WE ARE
     ============================================ -->
<section class="section">
    <div class="section-title reveal">
        <h2>Who We Are</h2>
        <p>Discover the story behind HoyLink and what drives us every day</p>
    </div>
    <div class="about-grid">
        <div class="about-card reveal">
            <div class="card-icon"><i class="fas fa-bullseye"></i></div>
            <h3>Our Purpose</h3>
            <p>HoyLink was born from a simple idea: make property hunting in Somalia and East Africa as easy as ordering food online. We're bridging the gap between dream homes and dreamers.</p>
        </div>
        <div class="about-card reveal">
            <div class="card-icon"><i class="fas fa-handshake"></i></div>
            <h3>Trust & Transparency</h3>
            <p>Every property on HoyLink is verified by real owners. No middlemen, no hidden fees, no games. Just honest connections between you and the people who know the property best.</p>
        </div>
        <div class="about-card reveal">
            <div class="card-icon"><i class="fas fa-rocket"></i></div>
            <h3>Innovation First</h3>
            <p>We're constantly evolving our platform with smart search, real-time messaging, and location-based recommendations to help you find your perfect match faster.</p>
        </div>
    </div>
</section>

<!-- ============================================
     MISSION & VISION
     ============================================ -->
<div class="mv-section reveal">
    <div class="mv-card">
        <div class="mv-icon"><i class="fas fa-eye"></i></div>
        <h3>Our Vision</h3>
        <p>To become the most trusted and innovative property platform in East Africa, empowering every individual to find their perfect home with confidence and ease.</p>
    </div>
    <div class="mv-card">
        <div class="mv-icon"><i class="fas fa-flag-checkered"></i></div>
        <h3>Our Mission</h3>
        <p>To revolutionize the real estate experience by creating a direct, transparent, and technology-driven connection between property owners and seekers, making home dreams a reality.</p>
    </div>
</div>

<!-- ============================================
     CORE VALUES
     ============================================ -->
<section class="section">
    <div class="section-title reveal">
        <h2>Our Core Values</h2>
        <p>The principles that guide everything we do at HoyLink</p>
    </div>
    <div class="values-grid">
        <div class="value-item reveal">
            <span class="value-icon">🤝</span>
            <h4>Integrity</h4>
            <p>We do the right thing, always. Honesty and transparency are non-negotiable.</p>
        </div>
        <div class="value-item reveal">
            <span class="value-icon">⚡</span>
            <h4>Speed</h4>
            <p>Fast responses, quick approvals, and instant connections between users.</p>
        </div>
        <div class="value-item reveal">
            <span class="value-icon">❤️</span>
            <h4>Empathy</h4>
            <p>We understand finding a home is personal. We treat every search with care.</p>
        </div>
        <div class="value-item reveal">
            <span class="value-icon">📈</span>
            <h4>Growth</h4>
            <p>Constantly improving our platform to serve you better every single day.</p>
        </div>
        <div class="value-item reveal">
            <span class="value-icon">🔒</span>
            <h4>Security</h4>
            <p>Your data and transactions are protected with enterprise-grade security.</p>
        </div>
        <div class="value-item reveal">
            <span class="value-icon">🌍</span>
            <h4>Community</h4>
            <p>Building a family of homeowners and seekers who support each other.</p>
        </div>
    </div>
</section>

<!-- ============================================
     ✅ CONTACT SECTION (CUSUB)
     ============================================ -->
<section class="contact-section">
    <div class="section-title reveal">
        <h2>📬 Get in Touch</h2>
        <p>We're here to help! Reach out to us through any of the channels below</p>
    </div>

    <div class="contact-grid">
        <!-- Email Card -->
        <div class="contact-card reveal" onclick="window.location.href='mailto:hoylink5@gmail.com'">
            <div class="contact-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <h3>Email</h3>
            <div class="contact-detail">
                <a href="mailto:hoylink5@gmail.com">hoylink5@gmail.com</a>
            </div>
            <a href="mailto:hoylink5@gmail.com" class="contact-action">
                <i class="fas fa-paper-plane"></i> Send Email
            </a>
            <div class="contact-response">
                <span class="dot"></span> Response within 24 hours
            </div>
        </div>

        <!-- Phone Card -->
        <div class="contact-card reveal" onclick="window.location.href='tel:+252636906585'">
            <div class="contact-icon">
                <i class="fas fa-phone"></i>
            </div>
            <h3>Phone</h3>
            <div class="contact-detail">
                <a href="tel:+252636906585">+252 63 6906585</a>
            </div>
            <a href="tel:+252636906585" class="contact-action">
                <i class="fas fa-phone-alt"></i> Call Now
            </a>
            <div class="contact-response">
                <span class="dot"></span> Available 9AM - 6PM
            </div>
        </div>

        <!-- WhatsApp Card -->
        <div class="contact-card reveal" onclick="window.open('https://wa.me/252636906585', '_blank')">
            <div class="contact-icon">
                <i class="fab fa-whatsapp"></i>
            </div>
            <h3>WhatsApp</h3>
            <div class="contact-detail">
                <a href="https://wa.me/252636906585" target="_blank">+252 63 6906585</a>
            </div>
            <a href="https://wa.me/252636906585" target="_blank" class="contact-action">
                <i class="fab fa-whatsapp"></i> Chat on WhatsApp
            </a>
            <div class="contact-response">
                <span class="dot"></span> Quick replies within minutes
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     WHY CHOOSE US
     ============================================ -->
<section class="section" style="background: rgba(42, 75, 140, 0.04); border-radius: 30px; margin: 0 8% 40px; padding: 60px 8%;">
    <div class="section-title reveal">
        <h2>Why Choose HoyLink?</h2>
        <p>What makes us different from the rest</p>
    </div>
    <div class="about-grid">
        <div class="about-card reveal">
            <div class="card-icon"><i class="fas fa-user-check"></i></div>
            <h3>Verified Owners Only</h3>
            <p>Every property comes directly from verified owners. No agents, no middlemen, just real people with real homes.</p>
        </div>
        <div class="about-card reveal">
            <div class="card-icon"><i class="fas fa-comments"></i></div>
            <h3>Direct Messaging</h3>
            <p>Chat directly with property owners. Ask questions, schedule viewings, negotiate prices — all in one place.</p>
        </div>
        <div class="about-card reveal">
            <div class="card-icon"><i class="fas fa-map-marked-alt"></i></div>
            <h3>Smart Location Search</h3>
            <p>Find properties based on your preferred neighborhoods, with integrated maps and live directions.</p>
        </div>
    </div>
</section>

<!-- ============================================
     CTA SECTION
     ============================================ -->
<div class="cta-section reveal">
    <h2>Ready to Find Your Dream Home?</h2>
    <p>Join thousands of satisfied homeowners and property seekers who trust HoyLink.</p>
    <a href="userDashboard.php" class="cta-btn">
        Start Exploring <i class="fas fa-arrow-right"></i>
    </a>
</div>

<!-- ============================================
     FOOTER
     ============================================ -->
<footer class="footer">
    <div class="footer-content">
        <div class="footer-col">
            <h4>HoyLink</h4>
            <p>Your trusted platform for finding properties directly from owners. Safe, fast, and transparent.</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <h4>Quick Links</h4>
            <p><a href="about.php">About Us</a></p>
            <p><a href="properties.php">Properties</a></p>
            <p><a href="contact.php">Contact</a></p>
            <p><a href="privacy.php">Privacy Policy</a></p>
        </div>
        <div class="footer-col">
            <h4>Contact Info</h4>
            <p><i class="fas fa-map-marker-alt"></i> Hargeisa, Somaliland</p>
            <p><i class="fas fa-phone"></i> +252 63 6906585</p>
            <p><i class="fas fa-envelope"></i> hoylink5@gmail.com</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> HoyLink — All Rights Reserved. Made with <i class="fas fa-heart"></i> for your dream home.</p>
    </div>
</footer>

<!-- ============================================
     SCRIPTS
     ============================================ -->
<script>
    // Scroll reveal animation
    const reveals = document.querySelectorAll('.reveal');

    function revealOnScroll() {
        reveals.forEach(element => {
            const windowHeight = window.innerHeight;
            const elementTop = element.getBoundingClientRect().top;
            const revealPoint = 150;

            if (elementTop < windowHeight - revealPoint) {
                element.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', revealOnScroll);
    window.addEventListener('load', revealOnScroll);

    // Top bar scroll effect
    window.addEventListener('scroll', function() {
        const topBar = document.querySelector('.top-bar');
        if (window.scrollY > 50) {
            topBar.style.padding = '12px 40px';
            topBar.style.background = 'rgba(15, 23, 42, 0.98)';
        } else {
            topBar.style.padding = '16px 40px';
            topBar.style.background = 'rgba(15, 23, 42, 0.92)';
        }
    });

    // Animate numbers when they come into view
    const statNumbers = document.querySelectorAll('.stat-number');
    let animated = false;

    function animateNumbers() {
        if (animated) return;
        
        const heroSection = document.querySelector('.hero');
        const heroPosition = heroSection.getBoundingClientRect().bottom;
        
        if (heroPosition > 0 && heroPosition < window.innerHeight) {
            statNumbers.forEach(stat => {
                const target = parseInt(stat.innerText);
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.innerText = target;
                        clearInterval(timer);
                    } else {
                        stat.innerText = Math.floor(current);
                    }
                }, 30);
            });
            animated = true;
        }
    }

    window.addEventListener('scroll', animateNumbers);
    window.addEventListener('load', animateNumbers);
</script>

</body>
</html>