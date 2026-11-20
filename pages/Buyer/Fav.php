<?php
session_start();

// =================== DATABASE CONNECTION ===================
require_once '../Authentication/db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// =================== USER AUTHENTICATION ===================
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$current_user_name = $_SESSION['user_name'];
$user_initial = strtoupper(substr($current_user_name, 0, 1));

// =================== TOGGLE FAVORITE (REMOVE ONLY FROM THIS PAGE) ===================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'remove_favorite') {
        $property_id = intval($_POST['property_id']);
        $user_id = $current_user_id;
        
        $deleteStmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND property_id = ?");
        $deleteStmt->execute([$user_id, $property_id]);
        
        $response = ['success' => true, 'message' => 'Removed from your favorites'];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// =================== FETCH ONLY THIS USER'S FAVORITES ===================
$user_favorites_stmt = $pdo->prepare("
    SELECT 
        p.id,
        p.owner_id,
        p.title,
        p.description,
        p.price,
        p.location,
        p.bedrooms,
        p.bathrooms,
        p.area,
        p.image,
        p.status,
        p.created_at as property_created,
        u.fullname as owner_name,
        f.created_at as favorited_date
    FROM properties p
    INNER JOIN favorites f ON p.id = f.property_id
    LEFT JOIN users u ON p.owner_id = u.user_id
    WHERE f.user_id = ? AND p.status = 'approved' AND p.is_deleted = 0
    ORDER BY f.created_at DESC
");
$user_favorites_stmt->execute([$current_user_id]);
$user_favorites = $user_favorites_stmt->fetchAll(PDO::FETCH_ASSOC);

$favorites_count = count($user_favorites);

// =================== STATS QUERY (My Listings removed from stats) ===================
$user_stats_stmt = $pdo->prepare("
    SELECT 
        (SELECT COUNT(*) FROM favorites f 
         JOIN properties p ON f.property_id = p.id 
         WHERE f.user_id = ? AND p.status = 'approved' AND p.is_deleted = 0) as total_favorites,
        (SELECT COUNT(*) FROM properties WHERE status = 'approved' AND is_deleted = 0) as total_properties
");
$user_stats_stmt->execute([$current_user_id]);
$user_stats = $user_stats_stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HoyLink - My Favorites</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
}

body {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #ffffff;
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 80%, rgba(239, 68, 68, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(102, 126, 234, 0.05) 0%, transparent 50%);
    z-index: -1;
}

/* Header */
.page-header {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    padding: 25px 40px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-weight: 500;
    font-size: 15px;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.back-btn:hover {
    background: rgba(255, 255, 255, 0.12);
    transform: translateX(-5px);
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    background: linear-gradient(90deg, #ff6b6b, #ff8e8e);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.user-section {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 20px;
    color: white;
    box-shadow: 0 4px 20px rgba(239, 68, 68, 0.3);
}

.user-info h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 4px;
}

.user-info p {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
}

/* Main Container */
.main-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 40px;
}

/* Welcome Card */
.welcome-card {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 40px;
    border: 1px solid rgba(239, 68, 68, 0.2);
    position: relative;
    overflow: hidden;
}

.welcome-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23EF4444' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.welcome-content {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.welcome-text h2 {
    font-size: 32px;
    margin-bottom: 10px;
    background: linear-gradient(90deg, #fff, #ff8e8e);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.welcome-text p {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.8);
    max-width: 600px;
}

.favorites-stats {
    text-align: center;
    background: rgba(0, 0, 0, 0.3);
    padding: 20px;
    border-radius: 16px;
    min-width: 200px;
}

.fav-count {
    font-size: 48px;
    font-weight: 800;
    color: #ef4444;
    line-height: 1;
    margin-bottom: 10px;
}

.fav-label {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
    letter-spacing: 2px;
}

/* Stats Cards - 3 Cards (My Listings removed) */
.stats-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 25px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    text-align: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: rgba(239, 68, 68, 0.3);
}

.stat-icon {
    font-size: 32px;
    margin-bottom: 15px;
    opacity: 0.9;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 5px;
}

.stat-value.fav {
    color: #ef4444;
}

.stat-label {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Empty State */
.empty-state {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 80px 40px;
    text-align: center;
    border: 2px dashed rgba(255, 255, 255, 0.1);
    margin: 40px 0;
}

.empty-icon {
    font-size: 80px;
    margin-bottom: 25px;
    opacity: 0.3;
}

.empty-state h3 {
    font-size: 24px;
    margin-bottom: 15px;
    color: rgba(255, 255, 255, 0.9);
}

.empty-state p {
    color: rgba(255, 255, 255, 0.6);
    max-width: 500px;
    margin: 0 auto 30px;
    line-height: 1.6;
}

.browse-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 15px 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
}

.browse-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

/* Favorites Grid */
.favorites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-top: 40px;
}

.favorite-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    overflow: hidden;
    border: 2px solid rgba(239, 68, 68, 0.2);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    cursor: pointer;
}

.favorite-card:hover {
    transform: translateY(-10px);
    border-color: rgba(239, 68, 68, 0.4);
    box-shadow: 0 20px 40px rgba(239, 68, 68, 0.1);
}

.fav-badge {
    position: absolute;
    top: 20px;
    left: 20px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    z-index: 2;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    pointer-events: none;
}

.remove-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 45px;
    height: 45px;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    border: none;
    border-radius: 50%;
    color: white;
    font-size: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 3;
}

.remove-btn:hover {
    background: rgba(239, 68, 68, 0.8);
    transform: scale(1.1);
}

.remove-btn:active {
    transform: scale(0.95);
}

.property-image {
    width: 100%;
    height: 220px;
    object-fit: cover;
    background: rgba(255, 255, 255, 0.03);
    display: block;
}

.card-content {
    padding: 25px;
}

.card-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.property-id {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
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
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    background: rgba(255, 255, 255, 0.03);
    padding: 8px 15px;
    border-radius: 10px;
}

.property-price {
    font-size: 28px;
    font-weight: 800;
    color: #ef4444;
    margin-top: 15px;
    text-shadow: 0 2px 10px rgba(239, 68, 68, 0.3);
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    font-size: 13px;
    color: rgba(255, 255, 255, 0.6);
}

.favorited-date {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #ef4444;
    font-weight: 600;
}

/* View Details Button */
.view-details-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    margin-top: 18px;
    padding: 14px 20px;
    background: linear-gradient(135deg, #2A4B8C, #6366f1);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.view-details-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(42, 75, 140, 0.4);
}

.view-details-btn i {
    font-size: 16px;
}

/* Footer */
.page-footer {
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    padding: 25px 40px;
    margin-top: 60px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.6);
}

.footer-info {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-top: 15px;
    flex-wrap: wrap;
}

.footer-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ============================================= */
/* 👇 RESPONSIVE CSS */
/* ============================================= */

/* 📱 MOBILE: (< 480px) */
@media (max-width: 480px) {
    .page-header {
        padding: 16px;
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }

    .header-left {
        flex-direction: column;
        gap: 12px;
        width: 100%;
    }

    .back-btn {
        font-size: 13px;
        padding: 8px 16px;
        border-radius: 8px;
        width: 100%;
        justify-content: center;
        white-space: nowrap;
    }

    .page-title {
        font-size: 20px;
    }

    .user-section {
        width: 100%;
        justify-content: center;
        gap: 10px;
    }

    .user-avatar {
        width: 38px;
        height: 38px;
        font-size: 16px;
    }

    .user-info h3 {
        font-size: 14px;
    }

    .user-info p {
        font-size: 11px;
    }

    .main-container {
        padding: 16px;
    }

    .welcome-card {
        padding: 18px 16px;
        border-radius: 14px;
        margin-bottom: 25px;
    }

    .welcome-content {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }

    .welcome-text h2 {
        font-size: 22px;
    }

    .welcome-text p {
        font-size: 13px;
    }

    .favorites-stats {
        padding: 16px;
        min-width: 100%;
        border-radius: 12px;
    }

    .fav-count {
        font-size: 36px;
    }

    .fav-label {
        font-size: 12px;
    }

    .stats-cards {
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 25px;
    }

    .stat-card {
        padding: 16px;
        border-radius: 12px;
    }

    .stat-card:last-child {
        grid-column: span 2;
    }

    .stat-icon {
        font-size: 24px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 22px;
    }

    .stat-label {
        font-size: 10px;
        letter-spacing: 0.5px;
    }

    .favorites-grid {
        grid-template-columns: 1fr;
        gap: 20px;
        margin-top: 25px;
    }

    .favorite-card {
        border-radius: 14px;
        border-width: 1.5px;
    }

    .favorite-card:hover {
        transform: translateY(-5px);
    }

    .fav-badge {
        top: 12px;
        left: 12px;
        padding: 4px 14px;
        font-size: 10px;
    }

    .remove-btn {
        top: 12px;
        right: 12px;
        width: 36px;
        height: 36px;
        font-size: 16px;
    }

    .property-image {
        height: 160px;
    }

    .card-content {
        padding: 16px;
    }

    .card-title {
        font-size: 16px;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }

    .property-id {
        font-size: 10px;
        padding: 3px 10px;
    }

    .property-location {
        font-size: 12px;
        margin-bottom: 10px;
    }

    .property-details {
        gap: 8px;
    }

    .detail-item {
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 8px;
    }

    .property-price {
        font-size: 22px;
        margin-top: 10px;
    }

    .card-footer {
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
        font-size: 12px;
        margin-top: 14px;
        padding-top: 14px;
    }

    .favorited-date {
        font-size: 12px;
    }

    .view-details-btn {
        font-size: 13px;
        padding: 12px 16px;
        border-radius: 10px;
        margin-top: 14px;
    }

    .empty-state {
        padding: 40px 20px;
        border-radius: 14px;
        margin: 25px 0;
    }

    .empty-icon {
        font-size: 50px;
        margin-bottom: 16px;
    }

    .empty-state h3 {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .empty-state p {
        font-size: 13px;
        padding: 0 10px;
    }

    .browse-btn {
        font-size: 14px;
        padding: 12px 24px;
        border-radius: 10px;
    }

    .page-footer {
        padding: 16px 20px;
        margin-top: 30px;
        font-size: 12px;
    }

    .footer-info {
        flex-direction: column;
        gap: 8px;
        margin-top: 10px;
    }

    .footer-item {
        font-size: 12px;
        justify-content: center;
    }
}

/* 📱 TABLET: (481px - 768px) */
@media (min-width: 481px) and (max-width: 768px) {
    .page-header {
        padding: 20px 24px;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: center;
        text-align: center;
    }

    .header-left {
        flex: 1 1 100%;
        justify-content: center;
    }

    .back-btn {
        font-size: 14px;
        padding: 10px 18px;
        border-radius: 10px;
    }

    .user-section {
        flex: 1 1 100%;
        justify-content: center;
    }

    .main-container {
        padding: 24px;
    }

    .welcome-card {
        padding: 24px;
        border-radius: 16px;
        margin-bottom: 30px;
    }

    .welcome-content {
        flex-direction: column;
        gap: 24px;
        text-align: center;
    }

    .welcome-text h2 {
        font-size: 26px;
    }

    .welcome-text p {
        font-size: 15px;
        max-width: 100%;
    }

    .favorites-stats {
        min-width: 100%;
        padding: 18px;
    }

    .stats-cards {
        grid-template-columns: 1fr 1fr 1fr;
        gap: 14px;
        margin-bottom: 30px;
    }

    .stat-card {
        padding: 20px;
        border-radius: 14px;
    }

    .stat-value {
        font-size: 28px;
    }

    .favorites-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 30px;
    }

    .favorite-card {
        border-radius: 16px;
    }

    .property-image {
        height: 180px;
    }

    .card-content {
        padding: 18px;
    }

    .card-title {
        font-size: 17px;
    }

    .property-price {
        font-size: 24px;
    }

    .view-details-btn {
        font-size: 14px;
        padding: 12px 16px;
    }

    .empty-state {
        padding: 60px 30px;
        border-radius: 16px;
    }

    .empty-icon {
        font-size: 60px;
    }

    .page-footer {
        padding: 20px 24px;
        margin-top: 40px;
    }

    .footer-info {
        gap: 20px;
    }
}

/* 💻 LAPTOP: (769px - 1024px) */
@media (min-width: 769px) and (max-width: 1024px) {
    .page-header {
        padding: 20px 30px;
    }

    .main-container {
        padding: 30px;
    }

    .favorites-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }

    .property-image {
        height: 200px;
    }

    .stats-cards {
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
    }

    .welcome-card {
        padding: 26px;
        border-radius: 18px;
    }
}

/* 🖥️ DESKTOP: (> 1024px) */
@media (min-width: 1025px) {
    .page-header {
        padding: 25px 40px;
    }

    .main-container {
        padding: 40px;
    }

    .favorites-grid {
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
    }

    .stats-cards {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .welcome-card {
        padding: 30px 35px;
        border-radius: 20px;
    }

    .property-image {
        height: 220px;
    }
}

/* 📐 LARGE SCREEN: (> 1400px) */
@media (min-width: 1401px) {
    .main-container {
        max-width: 1600px;
        padding: 50px;
    }

    .favorites-grid {
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 35px;
    }

    .property-image {
        height: 240px;
    }

    .card-title {
        font-size: 22px;
    }

    .property-price {
        font-size: 32px;
    }

    .stats-cards {
        gap: 25px;
    }

    .stat-value {
        font-size: 36px;
    }

    .welcome-text h2 {
        font-size: 36px;
    }

    .welcome-text p {
        font-size: 18px;
    }

    .fav-count {
        font-size: 56px;
    }
}

/* Fix for very small screens (height) */
@media (max-height: 700px) and (max-width: 480px) {
    .page-header {
        padding: 10px 12px;
        gap: 8px;
    }

    .back-btn {
        font-size: 11px;
        padding: 6px 12px;
        border-radius: 6px;
        width: 100%;
        justify-content: center;
    }

    .page-title {
        font-size: 16px;
    }

    .user-avatar {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }

    .user-info h3 {
        font-size: 12px;
    }
    .user-info p {
        font-size: 10px;
    }

    .main-container {
        padding: 10px;
    }

    .welcome-card {
        padding: 12px 14px;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .welcome-text h2 {
        font-size: 17px;
        margin-bottom: 4px;
    }
    .welcome-text p {
        font-size: 11px;
    }

    .favorites-stats {
        padding: 10px;
        border-radius: 8px;
        min-width: 100%;
    }

    .fav-count {
        font-size: 28px;
        margin-bottom: 4px;
    }
    .fav-label {
        font-size: 10px;
    }

    .stats-cards {
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-bottom: 15px;
    }

    .stat-card {
        padding: 10px;
        border-radius: 8px;
    }

    .stat-card:last-child {
        grid-column: span 2;
    }

    .stat-icon {
        font-size: 18px;
        margin-bottom: 4px;
    }
    .stat-value {
        font-size: 18px;
    }
    .stat-label {
        font-size: 9px;
    }

    .favorites-grid {
        gap: 12px;
        margin-top: 15px;
    }

    .property-image {
        height: 120px;
    }

    .card-content {
        padding: 12px;
    }

    .card-title {
        font-size: 14px;
    }
    .property-id {
        font-size: 9px;
        padding: 2px 8px;
    }
    .property-location {
        font-size: 11px;
        margin-bottom: 6px;
    }

    .property-details {
        gap: 6px;
    }
    .detail-item {
        font-size: 10px;
        padding: 4px 8px;
    }

    .property-price {
        font-size: 18px;
        margin-top: 6px;
    }

    .card-footer {
        font-size: 10px;
        margin-top: 10px;
        padding-top: 10px;
    }
    .favorited-date {
        font-size: 10px;
    }

    .view-details-btn {
        font-size: 11px;
        padding: 8px 12px;
        border-radius: 8px;
        margin-top: 10px;
    }

    .empty-state {
        padding: 20px 16px;
        margin: 15px 0;
    }
    .empty-icon {
        font-size: 36px;
        margin-bottom: 10px;
    }
    .empty-state h3 {
        font-size: 15px;
    }
    .empty-state p {
        font-size: 11px;
    }
    .browse-btn {
        font-size: 12px;
        padding: 8px 16px;
    }

    .page-footer {
        padding: 10px 16px;
        margin-top: 16px;
        font-size: 10px;
    }
    .footer-info {
        gap: 4px;
        margin-top: 6px;
    }
    .footer-item {
        font-size: 10px;
    }

    .remove-btn {
        width: 28px;
        height: 28px;
        font-size: 12px;
        top: 8px;
        right: 8px;
    }

    .fav-badge {
        top: 8px;
        left: 8px;
        padding: 3px 10px;
        font-size: 8px;
    }
}

/* Smooth transitions */
.favorite-card {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.favorite-card:hover {
    transform: translateY(-10px);
}

@media (max-width: 480px) {
    .favorite-card:hover {
        transform: translateY(-5px);
    }
}

/* Notification styling for mobile */
@media (max-width: 480px) {
    .notification {
        top: 10px !important;
        right: 10px !important;
        left: 10px !important;
        width: auto !important;
        text-align: center;
        font-size: 13px !important;
        padding: 12px 16px !important;
        border-radius: 10px !important;
    }
}
</style>
</head>
<body>

<!-- Header -->
<header class="page-header">
    <div class="header-left">
        <a href="userdashboard.php" class="back-btn">
            ← Back to Dashboard
        </a>
        <h1 class="page-title">❤️ My Favorites</h1>
    </div>
    
    <div class="user-section">
        <div class="user-avatar">
            <?php echo $user_initial; ?>
        </div>
        <div class="user-info">
            <h3><?php echo htmlspecialchars($current_user_name); ?></h3>
            <p>Your Personal Favorites List</p>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="main-container">
    <!-- Welcome Section -->
    <div class="welcome-card">
        <div class="welcome-content">
            <div class="welcome-text">
                <h2>Your Personal Favorites Collection</h2>
                <p>This page shows <strong>only your personal favorites</strong>. Other users cannot see your favorites, and you cannot see theirs. Each favorite property is uniquely yours.</p>
            </div>
            <div class="favorites-stats">
                <div class="fav-count"><?php echo $favorites_count; ?></div>
                <div class="fav-label">Properties Saved</div>
            </div>
        </div>
    </div>

    <!-- Stats Cards (My Listings removed) -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon">❤️</div>
            <div class="stat-value fav"><?php echo $user_stats['total_favorites']; ?></div>
            <div class="stat-label">My Favorites</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🏠</div>
            <div class="stat-value"><?php echo $user_stats['total_properties']; ?></div>
            <div class="stat-label">Total Properties</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">👤</div>
            <div class="stat-value">#<?php echo $current_user_id; ?></div>
            <div class="stat-label">User ID</div>
        </div>
    </div>

    <!-- Favorites List -->
    <?php if (empty($user_favorites)): ?>
        <div class="empty-state">
            <div class="empty-icon">❤️</div>
            <h3>No Favorites Yet</h3>
            <p>You haven't added any properties to your favorites list yet. Browse properties and click the heart icon to add them to your personal favorites.</p>
            <a href="userDashboard.php" class="browse-btn">
                🔍 Browse Properties
            </a>
        </div>
    <?php else: ?>
        <div class="favorites-grid" id="favoritesContainer">
            <?php foreach ($user_favorites as $property): ?>
                <div class="favorite-card" id="favCard-<?php echo $property['id']; ?>">
                    <div class="fav-badge">❤️ YOUR FAVORITE</div>
                    
                    <!-- Remove Button -->
                    <button class="remove-btn" onclick="event.stopPropagation(); removeFavorite(<?php echo $property['id']; ?>, this)">
                        ❌
                    </button>
                    
                    <!-- Property Image -->
                    <?php 
                    $imageSrc = 'https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
                    
                    if(!empty($property['image'])){
                        $images = explode(',', $property['image']);
                        $first_image = trim($images[0]);
                        if(!empty($first_image) && file_exists('uploads/' . $first_image)){
                            $imageSrc = 'uploads/' . $first_image;
                        }
                    }
                    ?>
                    <img src="<?php echo htmlspecialchars($imageSrc); ?>" 
                         alt="<?php echo htmlspecialchars($property['title']); ?>"
                         class="property-image"
                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'">
                    
                    <div class="card-content">
                        <div class="card-title">
                            <?php echo htmlspecialchars($property['title']); ?>
                            <span class="property-id">ID: <?php echo $property['id']; ?></span>
                        </div>
                        
                        <div class="property-location">
                            📍 <?php echo htmlspecialchars($property['location']); ?>
                        </div>
                        
                        <div class="property-details">
                            <div class="detail-item">
                                🛏 <?php echo $property['bedrooms']; ?> Bedrooms
                            </div>
                            <div class="detail-item">
                                🛁 <?php echo $property['bathrooms']; ?> Bathrooms
                            </div>
                            <div class="detail-item">
                                📐 <?php echo $property['area']; ?> sqft
                            </div>
                        </div>
                        
                        <div class="property-price">
                            $<?php echo number_format($property['price'], 2); ?>
                        </div>
                        
                        <div class="card-footer">
                            <div>
                                👤 <?php echo htmlspecialchars($property['owner_name']); ?>
                            </div>
                            <div class="favorited-date">
                                ❤️ <?php echo date('M d, Y', strtotime($property['favorited_date'])); ?>
                            </div>
                        </div>
                        
                        <a href="detail.php?id=<?php echo $property['id']; ?>" class="view-details-btn" onclick="event.stopPropagation();">
                            👁 View Details & Contact Owner
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<!-- Footer -->
<footer class="page-footer">
    <div>
        © 2026 HoyLink - Personal Favorites System
    </div>
    <div class="footer-info">
        <div class="footer-item">
            <span>👤</span>
            User: <?php echo htmlspecialchars($current_user_name); ?>
        </div>
        <div class="footer-item">
            <span>🏠</span>
            Favorites: <?php echo $favorites_count; ?> properties
        </div>
        <div class="footer-item">
            <span>🔒</span>
            Private & Isolated
        </div>
    </div>
</footer>

<!-- JavaScript -->
<script>
async function removeFavorite(propertyId, button) {
    const card = document.getElementById(`favCard-${propertyId}`);
    
    if (!confirm("Are you sure you want to remove this property from your favorites?")) {
        return;
    }
    
    button.innerHTML = '⏳';
    button.disabled = true;
    
    try {
        const response = await fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove_favorite&property_id=${propertyId}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            card.style.opacity = '0';
            card.style.transform = 'translateX(100px) scale(0.9)';
            card.style.transition = 'all 0.4s ease';
            
            setTimeout(() => {
                card.remove();
                updateFavoriteCounts();
                checkEmptyState();
                showNotification('Removed from favorites', 'success');
            }, 400);
        }
    } catch (error) {
        console.error('Error:', error);
        button.innerHTML = '❌';
        button.disabled = false;
        showNotification('Error removing favorite', 'error');
    }
}

function updateFavoriteCounts() {
    const container = document.getElementById('favoritesContainer');
    const currentCount = container ? container.children.length : 0;
    
    const welcomeCount = document.querySelector('.fav-count');
    if (welcomeCount) welcomeCount.textContent = currentCount;
    
    const statsCount = document.querySelector('.stat-value.fav');
    if (statsCount) statsCount.textContent = currentCount;
    
    const footerCount = document.querySelector('.footer-item:nth-child(2)');
    if (footerCount) {
        footerCount.innerHTML = `<span>🏠</span> Favorites: ${currentCount} properties`;
    }
}

function checkEmptyState() {
    const container = document.getElementById('favoritesContainer');
    const mainContainer = document.querySelector('.main-container');
    
    if (!container || container.children.length === 0) {
        if (container) container.remove();
        
        const emptyState = document.createElement('div');
        emptyState.className = 'empty-state';
        emptyState.innerHTML = `
            <div class="empty-icon">❤️</div>
            <h3>No Favorites Yet</h3>
            <p>You haven't added any properties to your favorites list yet. Browse properties and click the heart icon to add them to your personal favorites.</p>
            <a href="userDashboard.php" class="browse-btn">
                🔍 Browse Properties
            </a>
        `;
        mainContainer.appendChild(emptyState);
    }
}

function showNotification(message, type = 'info') {
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
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
</script>

</body>
</html>