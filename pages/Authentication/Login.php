<?php
session_start();
require_once 'db.php'; // DB connection

$errors = [];
$show_verification_message = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $errors[] = "Email iyo password waa lama huraan.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, fullname, password, role, status, is_verified FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $fullname, $hash, $role, $status, $is_verified);
            $stmt->fetch();
            
            if (password_verify($password, $hash)) {
                if ($is_verified != 1) {
                    $errors[] = "❌ Oops! Your Email is not Verified. Please register again.";
                    unset($_SESSION['temp_email']);
                    unset($_SESSION['verification_required']);
                }
                
                if($status != 'active' && $is_verified == 1) {
                    $errors[] = "Account-kaaga Waa La Xayiray. Fadlan La Xidhiidh Qaybta Caawinta.";
                } elseif($is_verified == 1 && $status == 'active') {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['user_name'] = $fullname;
                    $_SESSION['user_role'] = $role;

                    if($role === 'owner'){
                        // header("Location: OwnerDashboard.php");
                        exit;
                    } elseif($role === 'user'){
                        header("Location: ../Buyer/userDashboard.php");
                        exit;
                    } else {
                       header("Location: ../Buyer/userDashboard.php");
                        exit;
                    }
                }
            } else {
                $errors[] = "Password-ka waa khalad.";
            }
        } else {
            $errors[] = "Email-ka lama helin.";
        }
        $stmt->close();
    }
}

if (isset($_GET['verified']) && $_GET['verified'] == 1) {
    $show_verification_message = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>HoyLink - Login</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{box-sizing:border-box}
body,html{height:100%;margin:0;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial}
a{color:inherit;text-decoration:none}
body{
    background-image: linear-gradient(rgba(5,10,25,0.55), rgba(5,10,25,0.55)),
    url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1400&q=80');
    background-size: cover;
    background-position: center;
    color:#f2f6fb;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:30px;
}
.card{
    width:100%;
    max-width:920px;
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(8px);
    border-radius:14px;
    box-shadow: 0 10px 30px rgba(2,6,23,0.6);
    overflow:hidden;
    display:grid;
    grid-template-columns: 1fr 420px;
    gap:0;
}
.left{padding:40px}
.brand{display:flex;align-items:center;gap:12px;margin-bottom:10px}
.logo{width:48px;height:48px;border-radius:8px;background:linear-gradient(135deg,#ffa500,#ff6b6b);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;}
h1{margin:6px 0 6px;font-size:22px}
p.lead{margin:8px 0 22px;color:rgba(242,246,251,0.85);line-height:1.4}
.right{padding:30px;background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));display:flex;align-items:center;justify-content:center}
form{width:100%;max-width:360px}
label{display:block;font-size:13px;margin-bottom:6px;color:rgba(242,246,251,0.9)}
input{width:100%;padding:12px 14px;border-radius:10px;border:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.03);color:#fff;margin-bottom:12px;outline:none;font-size:14px}
input::placeholder{color:rgba(255,255,255,0.55)}
.btn{width:100%;padding:12px 14px;border-radius:10px;border:none;font-weight:600;cursor:pointer;background:linear-gradient(90deg,#ff8a00,#ff3d00);color:#fff;font-size:15px;box-shadow:0 8px 20px rgba(255,77,0,0.18);}
.btn:hover{transform:translateY(-2px);box-shadow:0 12px 24px rgba(255,77,0,0.25);transition:all 0.3s ease;}
.muted{font-size:13px;color:rgba(242,246,251,0.75);margin-top:10px}
.error {background: rgba(255,50,50,0.12); color:#ffd6d6; padding:10px;border-radius:8px;margin-bottom:10px}
.success {background: rgba(52,211,153,0.12); color:#d7ffec; padding:10px;border-radius:8px;margin-bottom:10px}

/* ===== WhatsApp Help Button (Glassmorphism) - NUMBER KA SAARAY ===== */
.wa-help-btn {
    position: fixed;
    bottom: 30px;
    left: 30px;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 24px;
    background: rgba(37, 211, 102, 0.15);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(37, 211, 102, 0.25);
    border-radius: 50px;
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    font-family: 'Inter', system-ui, sans-serif;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(37, 211, 102, 0.05);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    cursor: pointer;
    animation: waPulse 2.5s ease-in-out infinite;
}

.wa-help-btn:hover {
    transform: translateY(-4px) scale(1.03);
    background: rgba(37, 211, 102, 0.25);
    border-color: rgba(37, 211, 102, 0.4);
    box-shadow: 0 12px 48px rgba(37, 211, 102, 0.2), 0 0 0 2px rgba(37, 211, 102, 0.1);
}

.wa-help-btn .wa-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #25D366, #128C7E);
    border-radius: 50%;
    font-size: 20px;
    color: #fff;
    box-shadow: 0 4px 16px rgba(37, 211, 102, 0.3);
    flex-shrink: 0;
}

.wa-help-btn .wa-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.wa-help-btn .wa-text .label {
    font-size: 12px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
    letter-spacing: 0.3px;
}

/* Badge */
.wa-help-btn .wa-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 18px;
    height: 18px;
    background: #ef4444;
    border-radius: 50%;
    font-size: 9px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    border: 2px solid #0f172a;
    animation: waBadgePulse 2s ease-in-out infinite;
}

@keyframes waPulse {
    0%, 100% { box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(37, 211, 102, 0.05); }
    50% { box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 0 0 20px rgba(37, 211, 102, 0.15); }
}

@keyframes waBadgePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); }
}

/* Small screens */
@media (max-width: 500px) {
    .wa-help-btn {
        padding: 12px 16px;
        gap: 8px;
        bottom: 20px;
        left: 20px;
    }
    .wa-help-btn .wa-icon { width: 32px; height: 32px; font-size: 17px; }
    .wa-help-btn .wa-text .label { font-size: 11px; }
}

@media (max-width: 400px) {
    .wa-help-btn {
        padding: 10px 14px;
        bottom: 15px;
        left: 15px;
    }
    .wa-help-btn .wa-icon { width: 28px; height: 28px; font-size: 14px; }
}

@media (max-width:900px){
    .card{grid-template-columns:1fr; padding:0}
    .left{order:2;padding:20px}
    .right{order:1;padding:22px}
}
</style>
</head>
<body>
<div class="card" role="main">
  <div class="left" aria-hidden="true">
    <div class="brand">
      <div class="logo">HL</div>
      <div>
        <div style="font-size:13px;color:rgba(242,246,251,0.7)">HoyLink – Hargeisa</div>
        <div style="font-size:12px;color:rgba(242,246,251,0.5);margin-top:2px">Online House Sales Platform</div>
      </div>
    </div>

    <h1>Welcome Back</h1>
    <p class="lead">Login to your HoyLink account and continue discovering or listing houses.</p>
  </div>

  <div class="right">
    <form method="post" novalidate>
      <?php if (!empty($errors)): ?>
        <div class="error">
          <?php foreach($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
        </div>
      <?php endif; ?>

      <?php if ($show_verification_message): ?>
        <div class="success">
          ✅ Email-kaaga ayaa si guul leh loo xaqiijiyay! Hadda waxaad soo gali kartaa account-kaaga.
        </div>
      <?php endif; ?>

      <label for="email">Email</label>
      <input id="email" name="email" type="email" placeholder="you@example.com" required />

      <label for="password">Password</label>
      <input id="password" name="password" type="password" placeholder="6+ characters" required />

      <button class="btn" type="submit">Login</button>
      <div class="muted">Don't have an account? <a class="text-warning" href="register.php">Register</a></div>
    </form>
  </div>
</div>

<!-- ===== WHATSAPP HELP BUTTON (NUMBER KA SAARAY) ===== -->
<a href="https://wa.me/252636906585?text=Hello%20HoyLink%20Team%2C%20I%20need%20help%20with%20my%20account" 
   target="_blank" 
   rel="noopener noreferrer" 
   class="wa-help-btn" 
   aria-label="Contact us on WhatsApp">
    <span class="wa-icon">
        <i class="fab fa-whatsapp"></i>
    </span>
    <span class="wa-text">
        <span class="label">💬 Chat with us</span>
    </span>
    <span class="wa-badge">●</span>
</a>

</body>
</html>