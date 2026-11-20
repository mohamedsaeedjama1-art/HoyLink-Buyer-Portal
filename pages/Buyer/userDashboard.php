<?php
session_start();

// ================= SESSION CHECK =================
if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// ================= OWNER BLOCK =================
if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner') {
    header("Location: ownerDashboard.php");
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

// ================= FETCH USER DATA =================
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'] ?? 'user';
$first_letter = strtoupper(substr($user_name, 0, 1));

// ================= TOGGLE FAVORITE =================
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if($_POST['action'] === 'toggle_favorite') {
        $property_id = intval($_POST['property_id']);
        
        $checkStmt = $pdo->prepare("SELECT favorite_id FROM favorites WHERE user_id = ? AND property_id = ?");
        $checkStmt->execute([$user_id, $property_id]);
        $is_favorite = $checkStmt->fetch();
        
        if($is_favorite) {
            $deleteStmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND property_id = ?");
            $deleteStmt->execute([$user_id, $property_id]);
            $response = ['success' => true, 'action' => 'removed'];
        } else {
            $insertStmt = $pdo->prepare("INSERT INTO favorites (user_id, property_id, created_at) VALUES (?, ?, NOW())");
            $insertStmt->execute([$user_id, $property_id]);
            $response = ['success' => true, 'action' => 'added'];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// ================= FETCH PROPERTIES (ONLY APPROVED & NOT DELETED) =================
$query = "SELECT p.*, 
                 u.fullname as owner_name,
                 (SELECT COUNT(*) FROM favorites f WHERE f.property_id = p.id AND f.user_id = ?) as is_favorite
          FROM properties p 
          LEFT JOIN users u ON p.owner_id = u.user_id 
          WHERE p.status = 'approved' AND p.is_deleted = 0
          ORDER BY p.created_at DESC 
          LIMIT 8";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ================= FETCH USER'S FAVORITES COUNT =================
$favQuery = "SELECT COUNT(*) as fav_count FROM favorites WHERE user_id = ?";
$favStmt = $pdo->prepare($favQuery);
$favStmt->execute([$user_id]);
$fav_count = $favStmt->fetch(PDO::FETCH_ASSOC);

// ================= FETCH STATS =================
$statsQuery = "SELECT 
    (SELECT COUNT(*) FROM properties WHERE status = 'approved' AND is_deleted = 0) as total_properties,
    (SELECT COUNT(*) FROM users WHERE status = 'active') as total_users,
    (SELECT COUNT(*) FROM properties WHERE status = 'approved' AND price > 100000 AND is_deleted = 0) as premium_properties,
    (SELECT SUM(price) FROM properties WHERE status = 'approved' AND is_deleted = 0) as total_value";
$statsStmt = $pdo->prepare($statsQuery);
$statsStmt->execute();
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HoyLink - Luxury Villa Dashboard</title>
<style>
/* === DASHBOARD STYLES === */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', 'Inter', system-ui, -apple-system, sans-serif;
}

body {
    background: linear-gradient(rgba(0, 0, 0, 0.85), rgba(0, 0, 0, 0.9)),
                url('https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat fixed;
    color: #ffffff;
    min-height: 100vh;
    overflow-x: hidden;
    position: relative;
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

/* Header */
header {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    padding: 20px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
    animation: slideDown 0.5s ease;
}

@keyframes slideDown {
    from { transform: translateY(-100%); }
    to { transform: translateY(0); }
}

.logo-area {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.4);
}

.logo-text h1 {
    font-size: 24px;
    font-weight: 700;
    background: linear-gradient(90deg, #fff, #a5b4fc);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    margin-bottom: 3px;
}

.logo-text span {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 20px;
}

.user-avatar {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 18px;
    color: white;
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
}

/* Navigation */
nav {
    display: flex;
    gap: 25px;
}

.nav-link {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-weight: 500;
    font-size: 15px;
    padding: 8px 16px;
    border-radius: 10px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.nav-link::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transition: width 0.3s ease;
}

.nav-link:hover::before {
    width: 100%;
}

.nav-link:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.05);
}

/* Main Container */
.container {
    max-width: 1400px;
    margin: 30px auto;
    padding: 0 20px;
    animation: fadeIn 0.8s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Welcome Banner */
.welcome-banner {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15), rgba(118, 75, 162, 0.1));
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 30px 40px;
    margin-bottom: 40px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
}

.welcome-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.welcome-banner h2 {
    font-size: 28px;
    margin-bottom: 10px;
    font-weight: 700;
    position: relative;
    z-index: 1;
}

.welcome-banner p {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.8);
    position: relative;
    z-index: 1;
}

/* Search Bar */
.search-section {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 40px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.search-bar {
    display: flex;
    gap: 15px;
    align-items: center;
}

.search-input {
    flex: 1;
    padding: 18px 25px;
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    color: white;
    font-size: 15px;
    transition: all 0.3s ease;
    outline: none;
}

.search-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.search-btn {
    padding: 18px 35px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    color: white;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
}

/* Properties Grid */
.section-title {
    font-size: 22px;
    margin-bottom: 25px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-title::before {
    content: '';
    width: 6px;
    height: 25px;
    background: linear-gradient(180deg, #667eea, #764ba2);
    border-radius: 3px;
}

.properties-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 50px;
}

.property-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.08);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    animation: cardAppear 0.6s ease forwards;
    opacity: 0;
    cursor: pointer;
}

@keyframes cardAppear {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.property-card:nth-child(1) { animation-delay: 0.1s; }
.property-card:nth-child(2) { animation-delay: 0.2s; }
.property-card:nth-child(3) { animation-delay: 0.3s; }
.property-card:nth-child(4) { animation-delay: 0.4s; }

.property-card:hover {
    transform: translateY(-10px);
    border-color: rgba(102, 126, 234, 0.3);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.property-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.property-card:hover .property-image {
    transform: scale(1.05);
}

/* ========== FAVORITE BUTTON ========== */
.fav-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 45px;
    height: 45px;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 22px;
    transition: all 0.3s ease;
    z-index: 10;
}

.fav-btn:hover {
    background: rgba(239, 68, 68, 0.8);
    transform: scale(1.1);
}

.fav-btn.active {
    background: rgba(239, 68, 68, 0.9);
    animation: heartBeat 0.5s;
}

.fav-btn.active span {
    color: #ef4444;
}

@keyframes heartBeat {
    0% { transform: scale(1); }
    25% { transform: scale(1.2); }
    50% { transform: scale(1); }
    75% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.property-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    z-index: 2;
}

.property-content {
    padding: 25px;
}

.property-content h3 {
    font-size: 20px;
    margin-bottom: 10px;
    font-weight: 600;
    color: #a5b4fc;
}

.property-location {
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
    margin-bottom: 15px;
}

.property-details {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.property-detail {
    display: flex;
    align-items: center;
    gap: 6px;
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
}

.property-price {
    font-size: 24px;
    font-weight: 700;
    color: #a5b4fc;
    margin-top: 10px;
}

/* Stats Cards */
.stats-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 50px;
}

.stat-card {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15), rgba(118, 75, 162, 0.1));
    backdrop-filter: blur(10px);
    border-radius: 18px;
    padding: 25px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: rgba(102, 126, 234, 0.3);
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #a5b4fc;
    margin-bottom: 10px;
}

.stat-label {
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Inbox Section */
.inbox-section {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 30px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    margin-bottom: 40px;
}

.message-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.message-item {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    padding: 20px;
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;
}

.message-item:hover {
    background: rgba(255, 255, 255, 0.07);
    transform: translateX(5px);
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.message-sender {
    font-weight: 600;
    color: #a5b4fc;
}

.message-time {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.5);
}

.message-content {
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    line-height: 1.5;
}

/* Footer */
footer {
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    padding: 25px 0;
    text-align: center;
    margin-top: 50px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-content {
    color: rgba(255, 255, 255, 0.6);
    font-size: 14px;
}

/* Favorites Count Display */
.fav-count-display {
    color: #ef4444;
    font-weight: 600;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .properties-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 768px) {
    header {
        flex-direction: column;
        gap: 20px;
        padding: 20px;
    }
    
    .user-info {
        flex-direction: column;
        gap: 15px;
    }
    
    nav {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .search-bar {
        flex-direction: column;
    }
    
    .search-btn {
        width: 100%;
    }
    
    .properties-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-section {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 15px;
    }
    
    .welcome-banner {
        padding: 20px;
    }
    
    .property-content {
        padding: 20px;
    }
    
    .inbox-section {
        padding: 20px;
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
            <span>Luxury Villa Dashboard - Welcome <?php echo htmlspecialchars($user_name); ?></span>
        </div>
    </div>
    
    <div class="user-info">
        <div class="user-avatar"><?php echo $first_letter; ?></div>
        <div>
            <div style="font-weight:600;font-size:16px;">Welcome, <?php echo htmlspecialchars($user_name); ?>!</div>
            <div style="font-size:13px;color:rgba(255,255,255,0.7);">
                <?php echo ucfirst($user_role); ?> Member  
            </div>
        </div>
    </div>
    
    <nav>
        <a href="#" class="nav-link">🏠 Dashboard</a>
        <a href="#" class="nav-link">🔍 Search</a>
        <a href="Fav.php" class="nav-link">❤️ My Favorites</a>
        <a href="userAboutUs.php" class="nav-link">ℹ️ About us</a>
        <a href="userprofile.php" class="nav-link">👤 Profile</a>
        <a href="userlogout.php" class="nav-link">🚪 Logout</a>
    </nav>
</header>

<div class="container">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <h2>Welcome Back, <?php echo htmlspecialchars($user_name); ?>! 👋</h2>
        <p> ❤️  <strong>Find Your Next Home —</strong> Effortlessly.</p>
        <div style="margin-top:15px;font-size:14px;color:rgba(255,255,255,0.6);">
            📊 Logged in as: <strong><?php echo ucfirst($user_role); ?></strong>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <div class="search-bar">
            <input type="text" class="search-input" placeholder="Search by location, price, or amenities..." id="searchInput">
            <select class="search-input" style="width:200px;" id="locationFilter">
                <option value="">All Locations</option>
                <?php
                $locQuery = "SELECT DISTINCT location FROM properties WHERE location IS NOT NULL AND location != ''";
                $locStmt = $pdo->prepare($locQuery);
                $locStmt->execute();
                $locations = $locStmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach($locations as $loc):
                    if(!empty($loc['location'])):
                ?>
                <option value="<?php echo htmlspecialchars($loc['location']); ?>">
                    <?php echo htmlspecialchars($loc['location']); ?>
                </option>
                <?php endif; endforeach; ?>
            </select>
            <select class="search-input" style="width:150px;" id="priceFilter">
                <option value="">Any Price</option>
                <option value="0-10000">Under $10K</option>
                <option value="10000-50000">$10K - $50K</option>
                <option value="50000-100000">$50K - $100K</option>
                <option value="100000-9999999">$100K+</option>
            </select>
            <button class="search-btn" onclick="filterProperties()">🔍 Search Properties</button>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['total_properties'] ?? 0; ?></div>
            <div class="stat-label">Properties in HoyLink</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['premium_properties'] ?? 0; ?></div>
            <div class="stat-label">Premium Properties</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['total_users'] ?? 0; ?></div>
            <div class="stat-label">Active Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="statsFavCount"><?php echo $fav_count['fav_count']; ?></div>
            <div class="stat-label">Your Favorites</div>
        </div>
    </div>

    <!-- Featured Properties -->
    <h2 class="section-title">Featured Luxury Villas</h2>
    <p style="color:rgba(255,255,255,0.7);margin-bottom:25px;font-size:14px;">
        Click the ❤️ heart icon to add properties to your favorites. Your favorites will appear in <a href="Fav.php" style="color:#ef4444;">My Favorites</a> page.
        <br><strong>Click on any property to view details & contact owner</strong>
    </p>
    
    <div class="properties-grid" id="propertiesGrid">
        <?php if(empty($properties)): ?>
            <div class="property-card">
                <div class="property-badge">No Properties</div>
                <button class="fav-btn"><span>🤍</span></button>
                <img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Modern Villa" class="property-image">
                <div class="property-content">
                    <h3>No Properties Found</h3>
                    <div class="property-location">📍 Add properties to your database</div>
                    <div class="property-details">
                        <div class="property-detail">🛏 0 Bedrooms</div>
                        <div class="property-detail">📐 0 sqft</div>
                    </div>
                    <div class="property-price">$0</div>
                    <div style="margin-top:15px; text-align:center;">
                        <button style="background:rgba(102,126,234,0.2); border:1px solid rgba(102,126,234,0.5); color:#a5b4fc; padding:8px 16px; border-radius:8px; cursor:not-allowed; font-size:14px; width:100%;" disabled>
                            👁 No Details Available
                        </button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach($properties as $property): ?>
            <div class="property-card" 
                 onclick="window.location.href='detail.php?id=<?php echo $property['id']; ?>'"
                 data-id="<?php echo $property['id']; ?>"
                 data-price="<?php echo $property['price']; ?>"
                 data-location="<?php echo strtolower($property['location']); ?>"
                 data-title="<?php echo strtolower($property['title']); ?>">
                
                <!-- Badge based on price -->
                <?php if($property['price'] > 100000): ?>
                    <div class="property-badge" style="background:linear-gradient(135deg, #dc2626 0%, #991b1b 100%);">PREMIUM</div>
                <?php elseif($property['price'] > 50000): ?>
                    <div class="property-badge">VALUABLE</div>
                <?php else: ?>
                    <div class="property-badge" style="background:linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">STANDARD</div>
                <?php endif; ?>
                
                <!-- Favorite Button -->
                <button class="fav-btn <?php echo $property['is_favorite'] ? 'active' : ''; ?>" 
                        onclick="event.stopPropagation(); toggleFavorite(<?php echo $property['id']; ?>, this)">
                    <span><?php echo $property['is_favorite'] ? '❤' : '🤍'; ?></span>
                </button>
                
                <!-- Property Image - ONLY FIRST IMAGE (image1) -->
                <?php 
                $imgSrc = 'https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
                
                if(!empty($property['image'])) {
                    // Sawirrada waa image1, image2, image3, image4 - kala saar
                    $images = explode(',', $property['image']);
                    $first_image = trim($images[0]); // Sawirka koowaad (image1)
                    
                    if(!empty($first_image) && file_exists('uploads/' . $first_image)) {
                        $imgSrc = 'uploads/' . $first_image;
                    }
                }
                ?>
                <img src="<?php echo $imgSrc; ?>" 
                     alt="<?php echo htmlspecialchars($property['title']); ?>" 
                     class="property-image">
                
                <!-- Property Details -->
                <div class="property-content">
                    <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                    <div class="property-location">
                        📍 <?php echo htmlspecialchars($property['location']); ?>
                    </div>
                    <div class="property-details">
                        <div class="property-detail">🛏 <?php echo $property['bedrooms']; ?> Bedrooms</div>
                        <div class="property-detail">📐 <?php echo $property['area']; ?> sqft</div>
                        <div class="property-detail">🏠 ID: <?php echo $property['id']; ?></div>
                    </div>
                    <div class="property-price">$<?php echo number_format($property['price'], 2); ?></div>
                    
                    <!-- View Details Button -->
                    <div style="margin-top:15px; text-align:center;">
                        <button style="background:rgba(102,126,234,0.2); 
                                      border:1px solid rgba(102,126,234,0.5);
                                      color:#a5b4fc;
                                      padding:8px 16px;
                                      border-radius:8px;
                                      cursor:pointer;
                                      font-size:14px;
                                      transition:all 0.3s ease;
                                      width:100%;"
                                onmouseover="this.style.background='rgba(102,126,234,0.4)';"
                                onmouseout="this.style.background='rgba(102,126,234,0.2)';"
                                onclick="event.stopPropagation(); window.location.href='detail.php?id=<?php echo $property['id']; ?>'">
                            👁 View Details & Contact Owner
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Messages Section -->
    <h2 class="section-title">Recent Messages</h2>
    <div class="inbox-section">
        <div class="message-list">
            <div class="message-item">
                <div class="message-header">
                    <div class="message-sender">Property Details</div>
                    <div class="message-time">Today</div>
                </div>
                <div class="message-content">
                    🏠 Click on any property to view complete details and contact the owner directly via WhatsApp, call, or email.
                </div>
            </div>
            <div class="message-item">
                <div class="message-header">
                    <div class="message-sender">Favorites System</div>
                    <div class="message-time">Just now</div>
                </div>
                <div class="message-content">
                    ❤️ Click the heart icon on any property to add it to your favorites.
                    View all your saved properties in <a href="Fav.php" style="color:#ef4444;">My Favorites</a> page.
                </div>
            </div>
            <div class="message-item">
                <div class="message-header">
                    <div class="message-sender">Dashboard Status</div>
                    <div class="message-time">Today</div>
                </div>
                <div class="message-content">
                    You currently have <strong><?php echo $fav_count['fav_count']; ?> favorite properties</strong>.
                    Your favorites are private and only visible to you.
                </div>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="footer-content">
        © 2026 HoyLink - Hel Gurigaagi Riyada. All rights reserved.
        <div style="margin-top:10px;font-size:12px;color:rgba(255,255,255,0.5);">
            User: <strong><?php echo htmlspecialchars($user_name); ?></strong> | 
            Favorites: <strong class="fav-count-display" id="footerFavCount"><?php echo $fav_count['fav_count']; ?></strong> | 
            Properties: <?php echo $stats['total_properties'] ?? 0; ?> |
            <a href="detail.php" style="color:#a5b4fc; text-decoration:none;">View Property Details</a>
        </div>
    </div>
</footer>

<!-- JavaScript -->
<script>
// Toggle favorite function
async function toggleFavorite(propertyId, button) {
    const heart = button.querySelector('span');
    
    // Show loading state
    const originalHTML = heart.innerHTML;
    heart.innerHTML = '⏳';
    button.disabled = true;
    
    try {
        const response = await fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=toggle_favorite&property_id=${propertyId}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            if (data.action === 'added') {
                // Add to favorites
                button.classList.add('active');
                heart.innerHTML = '❤';
                heart.style.color = '#ef4444';
                
                // Animation
                button.style.animation = 'heartBeat 0.5s';
                setTimeout(() => button.style.animation = '', 500);
                
                // Update favorites count (+1)
                updateFavoritesCount(1);
                
                // Show notification
                showNotification('Added to favorites!', 'success');
                
            } else if (data.action === 'removed') {
                // Remove from favorites
                button.classList.remove('active');
                heart.innerHTML = '🤍';
                heart.style.color = 'white';
                
                // Update favorites count (-1)
                updateFavoritesCount(-1);
                
                // Show notification
                showNotification('Removed from favorites', 'info');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        heart.innerHTML = originalHTML;
        button.disabled = false;
        showNotification('Error updating favorites', 'error');
    }
}

// Update favorites count on all displays
function updateFavoritesCount(change) {
    const statsCount = document.getElementById('statsFavCount');
    const footerCount = document.getElementById('footerFavCount');
    
    [statsCount, footerCount].forEach(counter => {
        if(counter) {
            let current = parseInt(counter.textContent) || 0;
            current = Math.max(0, current + change);
            counter.textContent = current;
        }
    });
}

// Filter properties
function filterProperties() {
    const searchText = document.getElementById('searchInput').value.toLowerCase();
    const location = document.getElementById('locationFilter').value.toLowerCase();
    const priceRange = document.getElementById('priceFilter').value;
    
    const cards = document.querySelectorAll('#propertiesGrid .property-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const cardLocation = card.dataset.location;
        const cardTitle = card.dataset.title;
        const cardPrice = parseFloat(card.dataset.price);
        
        let matchesSearch = !searchText || 
                           cardLocation.includes(searchText) || 
                           cardTitle.includes(searchText);
        let matchesLocation = !location || cardLocation.includes(location);
        let matchesPrice = !priceRange;
        
        if (priceRange) {
            const [min, max] = priceRange.split('-').map(Number);
            matchesPrice = cardPrice >= min && cardPrice <= max;
        }
        
        if (matchesSearch && matchesLocation && matchesPrice) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    if (visibleCount === 0) {
        showNotification('No properties match your search criteria.', 'info');
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 12px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        background: ${type === 'success' ? 'linear-gradient(135deg, #10b981, #059669)' : 
                     type === 'error' ? 'linear-gradient(135deg, #ef4444, #dc2626)' : 
                     'linear-gradient(135deg, #3b82f6, #1d4ed8)'};
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        transform: translateX(100px);
        opacity: 0;
        transition: all 0.3s ease;
    `;
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 100);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard Loaded');
    console.log('User: <?php echo $user_name; ?>');
    console.log('Properties: <?php echo count($properties); ?>');
    console.log('Click any property to view details at detail.php');
});
</script>
</body>
</html>