# 🏡 HoyLink Hargeisa Somaliland

> **📌 Project Scope:** This release focuses on the Buyer Part. Owner and Admin dashboards are not included in this repository package.

<p align="center">
  <strong>Connecting Home Seekers with Property Owners</strong><br>
  A modern web-based platform for discovering houses for sale in Hargeisa, Somaliland.
</p>

<p align="center">
  ![PHP](https://img.shields.io/badge/PHP-Backend-777BB4?style=for-the-badge&logo=php&logoColor=white)
  ![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
  ![JavaScript](https://img.shields.io/badge/JavaScript-Frontend-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
  ![Status](https://img.shields.io/badge/Project-Buyer%20Part-6C63FF?style=for-the-badge)
</p>

---

## ✨ 1. Introduction

HoyLink is a web-based property platform designed to help people in Hargeisa discover houses for sale and connect directly with property owners.

This repository contains the **Buyer-facing part** of HoyLink, including the public landing page, account registration, email OTP verification, buyer dashboard, property details, favourites, and profile pages.

> **📌 Project Scope:** This release focuses on the Buyer Part. Owner and Admin dashboards are not included in this repository package.

### 🚀 Main Features

- 🏠 Public Landing Page
- 🔐 Buyer Registration and Login
- 📧 Email Verification using a Six-Digit OTP
- 🏘️ Browse Approved Properties
- 🔎 View Detailed Property Information
- ❤️ Add and Remove Favourite Properties
- 👤 View and Edit Buyer Profile
- 📞 Contact Property Owners
- 📄 About Us, Terms of Service, and Privacy Policy

---

## ⚙️ 2. How It Works

1. A visitor opens the HoyLink landing page.
2. The visitor creates an account and selects the `user` role.
3. HoyLink sends a six-digit verification code (OTP) to the registered email address.
4. The user enters the OTP before it expires.
5. After successful email verification, the user can log in.
6. The Buyer Dashboard displays approved and non-deleted properties.
7. Buyers can view property details, save favourites, and contact owners through phone, WhatsApp, or email.
8. Buyers can manage their profile information.

Property approval and owner-side listing management are outside the scope of this Buyer Part package.

---

## 🛠️ 3. Technologies Used

| Technology | Purpose |
|---|---|
| HTML5 | Page Structure |
| CSS3 | Styling and Layout |
| JavaScript | Frontend Interactions |
| PHP | Backend Development |
| MySQL | Database Management |
| MySQLi & PDO | Database Connectivity |
| PHPMailer | Email Delivery |
| Gmail SMTP | OTP Email Service |
| Google Maps API | Geocoding and Map Features |
| XAMPP | Local Development Environment |

---

## 📁 4. Project Structure

```text
HoyLink/
│
├── index.php
│
├── Demo/
│   ├── Landing Page.png
│   ├── Register Page.png
│   ├── Login Page.png
│   ├── Verify OTP.png
│   ├── BuyerDashboard.png
│   ├── BuyerProfile.png
│   ├── AbotUs.png
│   ├── terms.png
│   └── privacy and policy.png
|    ....More
│
└── pages/
    ├── Authentication/
    │  
    │   ├── Login.php
    │   ├── register.php
    │   ├── Send_otp.php
    │   ├── verify_otp.php
    │   └── terms/
    │       ├── term1.php
    │       └── privacy.php
    │
    └── Buyer/
        ├── userDashboard.php
        ├── Detail.php
        ├── Fav.php
        ├── userProfile.php
        ├── EditProfile.php
        ├── UserAboutUs.php
        └── userLogout.php
```

---

## 🔒 5. Security & Configuration Notice

**Important:** Some essential configuration files and services are intentionally not included in this public repository.

| Component | Availability |
|---|---|
| 🗄️ MySQL Database / SQL Export | ❌ Not Included |
| 📧 SMTP Credentials | ❌ Not Included |
| 📦 PHPMailer Source Files | ❌ Not Included |
| 🗺️ Google Maps API Keys | ❌ Not Included |

---

## 🔢 6. OTP Verification

HoyLink uses a six-digit One-Time Password (OTP) for email verification.

- OTP generation uses PHP's `random_int()`.
- OTP expiration period: **3 minutes**.
- Email delivery is handled through PHPMailer and SMTP.
- OTP information is stored in the `otp_verifications` database table.

A working database connection and SMTP configuration are required for OTP verification to function.

---

## 🖼️ 7. Demo Screenshots

The `Demo/` directory contains screenshots showcasing the main HoyLink pages and user interface.

These include:

- Landing Page
- Registration Page
- Login Page
- OTP Verification
- Buyer Dashboard
- Buyer Profile
- About Us
- Terms of Service
- Privacy Policy
- ..More

---

<p align="center">
  <strong>🏡 HoyLink — Hargeisa</strong><br>
  <em>An online platform connecting home seekers with property owners.</em>
  <br><br>
  Hargeisa, Somaliland.
</p>