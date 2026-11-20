<?php
session_start();

// ================= SESSION CHECK =================
if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// ================= DATABASE CONNECTION =================
require_once '../Authentication/db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ================= FETCH USER PROFILE DATA =================
$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE user_id = :user_id LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Xarafka ugu horreeya ee magaca
$first_letter = strtoupper(substr($user['fullname'], 0, 1));

// Calculate registration duration
$created_at = new DateTime($user['created_at']);
$now = new DateTime();
$interval = $created_at->diff($now);
$months = ($interval->y * 12) + $interval->m;
$days = $interval->d;

// Colors based on role
$role_colors = [
    'owner' => ['gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'color' => '#f59e0b'],
    'user' => ['gradient' => 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)', 'color' => '#3b82f6'],
    'admin' => ['gradient' => 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)', 'color' => '#8b5cf6']
];
$user_role_color = $role_colors[$user['role']] ?? $role_colors['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HoyLink - My Profile</title>
<style>
/* === INTERNAL CSS - CARD DESIGN PROFILE === */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', 'Inter', system-ui, -apple-system, sans-serif;
}

body {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #ffffff;
    min-height: 100vh;
    overflow-x: hidden;
    padding: 20px;
}

/* Header */
.profile-header {
    max-width: 1200px;
    margin: 0 auto 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 24px;
    font-weight: 700;
    background: linear-gradient(90deg, #a5b4fc, #818cf8);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.back-btn {
    padding: 10px 20px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.back-btn:hover {
    background: rgba(255, 255, 255, 0.12);
    transform: translateY(-2px);
}

/* Main Container */
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 30px;
}

/* Left Side - Profile Card */
.profile-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 30px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    position: relative;
    overflow: hidden;
}

.profile-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 120px;
    background: <?php echo $user_role_color['gradient']; ?>;
    opacity: 0.2;
}

.avatar-section {
    position: relative;
    z-index: 2;
    text-align: center;
    margin-bottom: 25px;
}

.profile-avatar {
    width: 140px;
    height: 140px;
    margin: 0 auto 20px;
    background: <?php echo $user_role_color['gradient']; ?>;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 52px;
    font-weight: 700;
    color: white;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    border: 4px solid rgba(255, 255, 255, 0.2);
    transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.profile-avatar:hover {
    transform: scale(1.05) rotate(5deg);
}

.profile-name {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 8px;
    background: linear-gradient(90deg, #fff, #a5b4fc);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.profile-role-badge {
    display: inline-block;
    padding: 8px 20px;
    background: <?php echo $user_role_color['gradient']; ?>;
    color: white;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px <?php echo $user_role_color['color']; ?>40;
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 30px;
}

.stat-item {
    background: rgba(255, 255, 255, 0.04);
    border-radius: 12px;
    padding: 15px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: rgba(255, 255, 255, 0.06);
    transform: translateY(-3px);
}

.stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #a5b4fc;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.action-btn {
    padding: 14px;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.edit-btn {
    background: <?php echo $user_role_color['gradient']; ?>;
    color: white;
}

.edit-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px <?php echo $user_role_color['color']; ?>40;
}

.logout-btn {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.logout-btn:hover {
    background: rgba(239, 68, 68, 0.2);
}

/* Right Side - Details Cards */
.details-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.detail-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 30px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
}

.detail-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: <?php echo $user_role_color['gradient']; ?>;
}

.detail-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    border-color: <?php echo $user_role_color['color']; ?>40;
}

.card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
}

.card-icon {
    width: 48px;
    height: 48px;
    background: <?php echo $user_role_color['gradient']; ?>;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: white;
}

.card-title {
    font-size: 20px;
    font-weight: 600;
    color: #a5b4fc;
}

/* Info Items */
.info-item {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    position: relative;
}

.info-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.info-label {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-value {
    font-size: 16px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.95);
}

.info-value.highlight {
    color: #a5b4fc;
    font-weight: 600;
}

/* Contact Links */
.contact-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: rgba(165, 180, 252, 0.1);
    border-radius: 10px;
    color: #a5b4fc;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    margin-top: 8px;
}

.contact-link:hover {
    background: rgba(165, 180, 252, 0.2);
    transform: translateX(5px);
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.status-active {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
}

.status-inactive {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

/* Responsive */
@media (max-width: 1024px) {
    .profile-container {
        grid-template-columns: 1fr;
    }
    
    .profile-card {
        max-width: 500px;
        margin: 0 auto;
    }
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .details-container {
        grid-template-columns: 1fr;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        font-size: 42px;
    }
    
    .profile-name {
        font-size: 24px;
    }
}

@media (max-width: 480px) {
    body {
        padding: 15px;
    }
    
    .profile-card,
    .detail-card {
        padding: 20px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>

<!-- Header -->
<div class="profile-header">
    <div class="logo">
        <span>🏠</span>
        HoyLink Profile
    </div>
    <a href="userDashboard.php" class="back-btn">
        ← Back to Dashboard
    </a>
</div>

<!-- Main Content -->
<div class="profile-container">
    <!-- Left Side - Profile Card -->
    <div class="profile-card">
        <div class="avatar-section">
            <div class="profile-avatar">
                <?php echo $first_letter; ?>
            </div>
            <h1 class="profile-name"><?php echo htmlspecialchars($user['fullname']); ?></h1>
            <div class="profile-role-badge">
                <?php echo ucfirst($user['role']); ?> Account
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value"><?php echo $months; ?>m</div>
                <div class="stat-label">Member For</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo $days; ?>d</div>
                <div class="stat-label">Active Days</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">#<?php echo $user['user_id']; ?></div>
                <div class="stat-label">User ID</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">
                    <?php echo strtoupper($user['status']); ?>
                </div>
                <div class="stat-label">Status</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="action-btn edit-btn" onclick="window.location.href='EditProfile.php'">
                <span>✏️</span>
                Edit Profile
            </button>
            <button class="action-btn logout-btn" onclick="window.location.href='ProfileLogout.php'">
                <span>🚪</span>
                Logout Account
            </button>
        </div>
    </div>

    <!-- Right Side - Details Cards -->
    <div class="details-container">
        <!-- Personal Info Card -->
        <div class="detail-card">
            <div class="card-header">
                <div class="card-icon">👤</div>
                <h3 class="card-title">Personal Information</h3>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <span>📛</span> FULL NAME
                </div>
                <div class="info-value highlight">
                    <?php echo htmlspecialchars($user['fullname']); ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <span>🆔</span> USER ID
                </div>
                <div class="info-value">
                    HL-<?php echo str_pad($user['user_id'], 6, '0', STR_PAD_LEFT); ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <span>🎯</span> PROFILE INITIAL
                </div>
                <div class="info-value">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:50px;height:50px;background:<?php echo $user_role_color['gradient']; ?>;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;">
                            <?php echo $first_letter; ?>
                        </div>
                        <div>
                            <div style="font-size:14px;color:rgba(255,255,255,0.7);">First letter of your name</div>
                            <div style="font-size:13px;color:<?php echo $user_role_color['color']; ?>;">Personal Avatar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Info Card -->
        <div class="detail-card">
            <div class="card-header">
                <div class="card-icon">📞</div>
                <h3 class="card-title">Contact Details</h3>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <span>📧</span> EMAIL ADDRESS
                </div>
                <div class="info-value">
                    <?php echo htmlspecialchars($user['email']); ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <span>📱</span> PHONE NUMBER
                </div>
                <div class="info-value">
                    <?php if(!empty($user['phone'])): ?>
                        <?php echo htmlspecialchars($user['phone']); ?>
                    <?php else: ?>
                        <span style="color:rgba(255,255,255,0.5);">Not provided</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Account Info Card -->
        <div class="detail-card">
            <div class="card-header">
                <div class="card-icon">🔐</div>
                <h3 class="card-title">Account Information</h3>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <span>👑</span> ACCOUNT ROLE
                </div>
                <div class="info-value">
                    <span style="display:inline-block;padding:6px 15px;background:<?php echo $user_role_color['gradient']; ?>;border-radius:20px;font-weight:600;">
                        <?php echo ucfirst($user['role']); ?>
                    </span>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <span>📊</span> ACCOUNT STATUS
                </div>
                <div class="info-value">
                    <?php if($user['status'] === 'active'): ?>
                        <span class="status-badge status-active">
                            <span>●</span> Active
                        </span>
                    <?php else: ?>
                        <span class="status-badge status-inactive">
                            <span>●</span> Inactive
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <span>📅</span> MEMBER SINCE
                </div>
                <div class="info-value">
                    <?php 
                    $date = new DateTime($user['created_at']);
                    echo $date->format('F j, Y');
                    ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <span>⏳</span> ACCOUNT AGE
                </div>
                <div class="info-value">
                    <?php 
                    if($months > 0) {
                        echo $months . ' month' . ($months > 1 ? 's' : '');
                        if($days > 0) {
                            echo ' ' . $days . ' day' . ($days > 1 ? 's' : '');
                        }
                    } else {
                        echo $days . ' day' . ($days > 1 ? 's' : '');
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Animations -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate profile card
    const profileCard = document.querySelector('.profile-card');
    profileCard.style.opacity = '0';
    profileCard.style.transform = 'translateY(30px)';
    
    setTimeout(() => {
        profileCard.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        profileCard.style.opacity = '1';
        profileCard.style.transform = 'translateY(0)';
    }, 200);
    
    // Animate detail cards with stagger
    const detailCards = document.querySelectorAll('.detail-card');
    detailCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 400 + (index * 200));
    });
    
    // Add hover effects to stats
    const statItems = document.querySelectorAll('.stat-item');
    statItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Avatar hover effect
    const avatar = document.querySelector('.profile-avatar');
    avatar.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.1) rotate(10deg)';
    });
    
    avatar.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1.05) rotate(5deg)';
    });
    
    console.log('Profile loaded for: <?php echo $user["fullname"]; ?>');
});
</script>

</body>
</html>