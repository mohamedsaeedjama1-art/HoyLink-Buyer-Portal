<?php
session_start();

// Session check
if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require_once '../Authentication/db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch user data
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

// Handle update
$success_message = '';
$error_message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $new_password = trim($_POST['new_password']);
    
    // Validation
    if(empty($fullname) || empty($email)) {
        $error_message = "Full name and email are required!";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address!";
    } else {
        // Check if email exists for other users
        $check_query = "SELECT user_id FROM users WHERE email = :email AND user_id != :user_id";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->bindParam(':email', $email);
        $check_stmt->bindParam(':user_id', $user_id);
        $check_stmt->execute();
        
        if($check_stmt->rowCount() > 0) {
            $error_message = "Email already exists for another user!";
        } else {
            // Update query
            if(!empty($new_password)) {
                // Update with new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query = "UPDATE users SET fullname = :fullname, email = :email, phone = :phone, password = :password WHERE user_id = :user_id";
                $update_stmt = $pdo->prepare($update_query);
                $update_stmt->bindParam(':fullname', $fullname);
                $update_stmt->bindParam(':email', $email);
                $update_stmt->bindParam(':phone', $phone);
                $update_stmt->bindParam(':password', $hashed_password);
                $update_stmt->bindParam(':user_id', $user_id);
            } else {
                // Update without changing password
                $update_query = "UPDATE users SET fullname = :fullname, email = :email, phone = :phone WHERE user_id = :user_id";
                $update_stmt = $pdo->prepare($update_query);
                $update_stmt->bindParam(':fullname', $fullname);
                $update_stmt->bindParam(':email', $email);
                $update_stmt->bindParam(':phone', $phone);
                $update_stmt->bindParam(':user_id', $user_id);
            }
            
            if($update_stmt->execute()) {
                $_SESSION['user_name'] = $fullname;
                $success_message = "Profile updated successfully!";
                
                // Refresh user data
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error_message = "Failed to update profile!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile - HoyLink</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', 'Inter', system-ui, sans-serif;
}

body {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #ffffff;
    min-height: 100vh;
    padding: 20px;
}

.container {
    max-width: 800px;
    margin: 0 auto;
}

/* Header */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo {
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
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: rgba(255, 255, 255, 0.12);
}

/* Form Card */
.form-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 35px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.form-title {
    font-size: 28px;
    margin-bottom: 10px;
    background: linear-gradient(135deg, #fff, #a5b4fc);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.form-subtitle {
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

/* Messages */
.alert {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #10b981;
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

/* Form Groups */
.form-group {
    margin-bottom: 25px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-size: 13px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
    letter-spacing: 1px;
}

input {
    width: 100%;
    padding: 14px 16px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: white;
    font-size: 15px;
    transition: all 0.3s ease;
}

input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

input:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.help-text {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.5);
    margin-top: 6px;
}

/* Button */
.btn-update {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    border: none;
    border-radius: 12px;
    color: white;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.btn-update:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
}

hr {
    border-color: rgba(255, 255, 255, 0.1);
    margin: 25px 0;
}

/* Info Box */
.info-box {
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.2);
    border-radius: 12px;
    padding: 15px;
    margin-top: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.info-icon {
    font-size: 20px;
}

.info-text {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
}

@media (max-width: 768px) {
    .form-card {
        padding: 25px;
    }
    .form-title {
        font-size: 24px;
    }
}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">🏠 HoyLink</div>
        <a href="userProfile.php" class="back-btn">← Back to Profile</a>
    </div>
    
    <div class="form-card">
        <h1 class="form-title">Edit Profile</h1>
        <p class="form-subtitle">Update your personal information</p>
        
        <?php if($success_message): ?>
            <div class="alert alert-success">✓ <?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if($error_message): ?>
            <div class="alert alert-error">⚠ <?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                <div class="help-text">Optional - Enter your phone number</div>
            </div>
            
            <div class="form-group">
                <label>Role</label>
                <input type="text" value="<?php echo ucfirst($user['role']); ?>" disabled>
                <div class="help-text">Role cannot be changed</div>
            </div>
            
            <div class="form-group">
                <label>User ID</label>
                <input type="text" value="HL-<?php echo str_pad($user['user_id'], 6, '0', STR_PAD_LEFT); ?>" disabled>
            </div>
            
            <hr>
            
            <div class="form-group">
                <label>New Password (Optional)</label>
                <input type="password" name="new_password" placeholder="Leave blank to keep current password">
                <div class="help-text">Minimum 6 characters if you want to change password</div>
            </div>
            
            <div class="info-box">
                <div class="info-icon">ℹ️</div>
                <div class="info-text">Your password is encrypted and secure. Leave password field blank if you don't want to change it.</div>
            </div>
            
            <button type="submit" name="update_profile" class="btn-update">
                💾 Save Changes
            </button>
        </form>
    </div>
</div>
</body>
</html>