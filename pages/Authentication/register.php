<?php
// PHP Code for Registration with OTP Verification
// session_start() MUST be at the VERY TOP before ANY output
session_start();
include 'db.php';
include 'send_otp.php';

$fullname = $email = $phone = $password = $role = "";
$fullname_err = $email_err = $phone_err = $password_err = $role_err = "";
$success_msg = $error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate Full Name
    if (empty(trim($_POST["fullname"]))) {
        $fullname_err = "Please enter your full name.";
    } else {
        $fullname = trim($_POST["fullname"]);
        if (preg_match('/[0-9]/', $fullname)) {
            $fullname_err = "Name cannot contain numbers.";
        }
    }

    // Validate Email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
        
        // Check if email exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $email_err = "This email is already registered. Try another.";
        }
        $stmt->close();
    }

    // Validate Phone with UNIQUE check AND Format check
    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter your phone number.";
    } else {
        $phone = trim($_POST["phone"]);
        
        // Format validation: Must be 9 digits
        if (!preg_match("/^[0-9]{9}$/", $phone)) {
            $phone_err = "Invalid format! Please use 9 digits (e.g., 636906585)";
        } else {
            // Check if phone number already exists
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE phone = ? LIMIT 1");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $phone_err = "This phone number is already registered. Please use a different number.";
            }
            $stmt->close();
        }
    }

    // Validate Password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must be at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate Role
    if (empty($_POST["role"])) {
        $role_err = "Please select a role.";
    } else {
        $role = $_POST["role"];
    }

    // If no errors → Insert the user
    if (empty($fullname_err) && empty($email_err) && empty($phone_err) && empty($password_err) && empty($role_err)) {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO users (fullname, email, phone, password, role, is_verified) 
            VALUES (?, ?, ?, ?, ?, 0)
        ");
        $stmt->bind_param("sssss", $fullname, $email, $phone, $hashed_password, $role);

        if ($stmt->execute()) {
            // Generate and send OTP
            $otp_code = generateOTP();
            
            if (saveOTP($conn, $email, $otp_code)) {
                if (sendOTP($email, $otp_code)) {
                    // Store email in session and redirect to OTP verification
                    $_SESSION['temp_email'] = $email;
                    $_SESSION['verification_required'] = true;
                    
                    $stmt->close();
                    $conn->close();
                    
                    header("Location: verify_otp.php");
                    exit();
                } else {
                    $error_msg = "Failed to send OTP email. Please try again.";
                }
            } else {
                $error_msg = "Failed to save OTP. Please try again.";
            }
        } else {
            // Check if error is from duplicate phone
            if ($conn->errno == 1062) {
                $phone_err = "This phone number is already registered. Please use a different number.";
                $error_msg = "";
            } else {
                $error_msg = "Something went wrong. Please try again.";
            }
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HoyLink - Register</title>
    <style>
        /* Your CSS with minor improvements */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-image: linear-gradient(rgba(5,10,25,0.55), rgba(5,10,25,0.55)),
            url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1400&q=80');
            background-size: cover;
            background-position: center;
            color: #f2f6fb;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }
        
        .card {
            width: 100%;
            max-width: 920px;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(8px);
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(2,6,23,0.6);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 0;
        }
        
        .left {
            padding: 40px;
        }
        
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }
        
        .logo {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            background: linear-gradient(135deg, #ffa500, #ff6b6b);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
        }
        
        h1 {
            margin: 6px 0 6px;
            font-size: 22px;
        }
        
        .tagline {
            font-size: 14px;
            color: rgba(242,246,251,0.7);
            margin-bottom: 4px;
        }
        
        .subtitle {
            font-size: 12px;
            color: rgba(242,246,251,0.5);
            margin-top: 2px;
        }
        
        p.lead {
            margin: 8px 0 22px;
            color: rgba(242,246,251,0.85);
            line-height: 1.4;
        }
        
        .right {
            padding: 30px;
            background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        form {
            width: 100%;
            max-width: 360px;
        }
        
        .form-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 24px;
            text-align: center;
        }
        
        label {
            display: block;
            font-size: 13px;
            margin-bottom: 6px;
            color: rgba(242,246,251,0.9);
        }
        
        input, select {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.03);
            color: #fff;
            margin-bottom: 12px;
            outline: none;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        select option {
            background-color: rgba(5,10,25,0.95);
            color: #fff;
        }
        
        input::placeholder {
            color: rgba(255,255,255,0.55);
        }
        
        .btn {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(90deg, #ff8a00, #ff3d00);
            color: #fff;
            font-size: 15px;
            box-shadow: 0 8px 20px rgba(255,77,0,0.18);
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(255,77,0,0.25);
        }
        
        .muted {
            font-size: 13px;
            color: rgba(242,246,251,0.75);
            margin-top: 10px;
            text-align: center;
        }
        
        .error {
            background: rgba(255,50,50,0.12);
            color: #ffd6d6;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 14px;
            display: none;
        }
        
        .success {
            background: rgba(52,211,153,0.12);
            color: #d7ffec;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 14px;
            display: none;
        }
        
        .error-field {
            color: #ff6b6b;
            font-size: 12px;
            margin-top: -8px;
            margin-bottom: 10px;
            display: block;
            min-height: 16px;
        }
        
        .login-link {
            color: #ffa500;
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 900px) {
            .card {
                grid-template-columns: 1fr;
                padding: 0;
            }
            
            .left {
                order: 2;
                padding: 20px;
            }
            
            .right {
                order: 1;
                padding: 22px;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .left, .right {
                padding: 20px 16px;
            }
            
            .form-title {
                font-size: 20px;
            }
        }
        
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: -8px;
            margin-bottom: 12px;
            background: rgba(255,255,255,0.1);
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease;
        }
        
        /* Phone format hint styling */
        .format-hint {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            margin-top: -10px;
            margin-bottom: 10px;
            display: block;
        }
        
        .format-hint.valid {
            color: #2ed573;
        }
        
        .format-hint.invalid {
            color: #ff4757;
        }
    </style>
</head>
<body>
    <div class="card" role="main">
        <div class="left" aria-hidden="true">
            <div class="brand">
                <div class="logo">HL</div>
                <div>
                    <div class="tagline">HoyLink – Hargeisa</div>
                    <div class="subtitle">Online House Sales Platform</div>
                </div>
            </div>
            
            <h1>Find Your Dream Home in Hargeisa</h1>
            <p class="lead">Join HoyLink today to browse thousands of properties, connect with trusted owners, and make your real estate journey seamless.</p>
            <br>
            
            <div style="margin-top: 30px;">
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; margin-right: 10px; font-size: 12px;">✓</div>
                    <span>📞 Isticmaal Number La Heli Karo Marwalna</span>
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; margin-right: 10px; font-size: 12px;">✓</div>
                    <span> 🗝️Ha ka tegin furaha maxalliga 63' 67' 65</span>
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; margin-right: 10px; font-size: 12px;">✓</div>
                    <span> 🔐Isticmaal Fure Sireed Cagaaran</span>
                </div>
                <div style="display: flex; align-items: center;">
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; margin-right: 10px; font-size: 12px;">✓</div>
                    <span>📩 Hubi Emailkaaga Intaanad Is diwaangalin</span>
                </div>
            </div>
        </div>
        
        <div class="right">
            <form method="POST" action="" id="registerForm" onsubmit="return validateForm()">
                <h2 class="form-title">Create Account</h2>
                
                <?php
                // Display messages
                if (!empty($success_msg)) {
                    echo "<div class='success' id='successMsg'>$success_msg</div>";
                }
                
                if (!empty($error_msg)) {
                    echo "<div class='error' id='errorMsg'>$error_msg</div>";
                }
                ?>
                
                <div id="clientSuccessMsg" class="success" style="display: none;"></div>
                <div id="clientErrorMsg" class="error" style="display: none;"></div>
                
                <input type="text" name="fullname" id="fullname" placeholder="Full Name" value="<?php echo htmlspecialchars($fullname); ?>">
                <span class="error-field" id="fullnameError"><?php echo $fullname_err; ?></span>
                
                <input type="email" name="email" id="email" placeholder="Email Address" value="<?php echo htmlspecialchars($email); ?>">
                <span class="error-field" id="emailError"><?php echo $email_err; ?></span>
                
                <input type="tel" name="phone" id="phone" placeholder="Phone Number (e.g., 636906585)" value="<?php echo htmlspecialchars($phone); ?>" onkeyup="validatePhoneFormat()">
                <span class="format-hint" id="formatHint">📱 Format: 9 digits (e.g., 636906585)</span>
                <span class="error-field" id="phoneError"><?php echo $phone_err; ?></span>
                
                <input type="password" name="password" id="password" placeholder="Password (min 6 characters)" onkeyup="checkPasswordStrength()">
                <div class="password-strength">
                    <div class="strength-bar" id="passwordStrength"></div>
                </div>
                <span class="error-field" id="passwordError"><?php echo $password_err; ?></span>
                
                <select name="role" id="role">
                    <option value="">-- Select Role --</option>
                    <option value="user" <?php if ($role == "user") echo "selected"; ?>>User (Looking for a house)</option>
                    <option value="owner" <?php if ($role == "owner") echo "selected"; ?>>Owner (Selling/Renting property)</option>
                </select>
                <span class="error-field" id="roleError"><?php echo $role_err; ?></span>
                
                <button type="submit" class="btn">Create Account</button>
                
                <p class="muted">By registering, you agree to our <a href="terms/term1.php" style="color: #ffa500;">Terms of Service</a> and <a href="terms/privacy.php" style="color: #ffa500;">Privacy Policy</a></p>
                
                <p class="muted">Already have an account? <a href="login.php" class="login-link">Login here</a></p>
            </form>
        </div>
    </div>
    
    <script>
        // JavaScript for form validation and interactivity
        function validateForm() {
            let isValid = true;
            
            // Reset errors
            document.getElementById('fullnameError').textContent = '';
            document.getElementById('emailError').textContent = '';
            document.getElementById('phoneError').textContent = '';
            document.getElementById('passwordError').textContent = '';
            document.getElementById('roleError').textContent = '';
            
            // Get form values
            const fullname = document.getElementById('fullname').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const password = document.getElementById('password').value;
            const role = document.getElementById('role').value;
            
            // Validate full name - No numbers allowed
            if (!fullname) {
                document.getElementById('fullnameError').textContent = 'Please enter your full name.';
                isValid = false;
            } else if (/[0-9]/.test(fullname)) {
                document.getElementById('fullnameError').textContent = 'Valid name - numbers are not allowed!';
                isValid = false;
            }
            
            // Validate email
            if (!email) {
                document.getElementById('emailError').textContent = 'Please enter your email address.';
                isValid = false;
            } else if (!validateEmail(email)) {
                document.getElementById('emailError').textContent = 'Please enter a valid email address.';
                isValid = false;
            }
            
            // Validate phone with format check (9 digits only)
            if (!phone) {
                document.getElementById('phoneError').textContent = 'Please enter your phone number.';
                isValid = false;
            } else if (!/^[0-9]{9}$/.test(phone)) {
                document.getElementById('phoneError').textContent = 'Invalid format! Please use exactly 9 digits (e.g., 636906585)';
                isValid = false;
            }
            
            // Validate password
            if (!password) {
                document.getElementById('passwordError').textContent = 'Please enter a password.';
                isValid = false;
            } else if (password.length < 6) {
                document.getElementById('passwordError').textContent = 'Password must be at least 6 characters.';
                isValid = false;
            }
            
            // Validate role
            if (!role) {
                document.getElementById('roleError').textContent = 'Please select a role.';
                isValid = false;
            }
            
            // Show success/error message
            if (!isValid) {
                document.getElementById('clientErrorMsg').textContent = 'Please fix the errors above.';
                document.getElementById('clientErrorMsg').style.display = 'block';
                document.getElementById('clientSuccessMsg').style.display = 'none';
            } else {
                document.getElementById('clientErrorMsg').style.display = 'none';
            }
            
            return isValid;
        }
        
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        // Validate phone format in real-time
        function validatePhoneFormat() {
            const phoneInput = document.getElementById('phone');
            const phone = phoneInput.value.trim();
            const formatHint = document.getElementById('formatHint');
            
            if (phone.length === 0) {
                formatHint.textContent = '📱 Format: 9 digits (e.g., 636906585)';
                formatHint.className = 'format-hint';
                return;
            }
            
            if (/^[0-9]{9}$/.test(phone)) {
                formatHint.textContent = '✅ Format is correct! (9 digits)';
                formatHint.className = 'format-hint valid';
            } else if (/^[0-9]+$/.test(phone)) {
                if (phone.length < 9) {
                    formatHint.textContent = `⚠️ Need ${9 - phone.length} more digit(s) (9 digits total)`;
                } else if (phone.length > 9) {
                    formatHint.textContent = '⚠️ Too many digits! Please use exactly 9 digits';
                }
                formatHint.className = 'format-hint invalid';
            } else {
                formatHint.textContent = '❌ Numbers only please! Format: 9 digits (e.g., 636906585)';
                formatHint.className = 'format-hint invalid';
            }
            
            // Clear error message while user is typing
            const phoneError = document.getElementById('phoneError');
            if (phoneError.textContent.includes('Invalid format')) {
                phoneError.textContent = '';
            }
        }
        
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthBar.style.width = '0';
                strengthBar.style.background = 'transparent';
                return;
            }
            
            let strength = 0;
            
            // Length check
            if (password.length >= 6) strength += 25;
            if (password.length >= 10) strength += 10;
            
            // Character variety checks
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 25;
            
            // Set width and color
            strength = Math.min(strength, 100);
            strengthBar.style.width = strength + '%';
            
            // Set color based on strength
            if (strength < 40) {
                strengthBar.style.background = '#ff4757'; // Red
            } else if (strength < 70) {
                strengthBar.style.background = '#ffa502'; // Orange
            } else {
                strengthBar.style.background = '#2ed573'; // Green
            }
        }
        
        // Show PHP messages if they exist
        document.addEventListener('DOMContentLoaded', function() {
            const successMsg = document.getElementById('successMsg');
            const errorMsg = document.getElementById('errorMsg');
            
            if (successMsg) {
                successMsg.style.display = 'block';
                // Clear all inputs on successful registration
                if (successMsg.textContent.includes('successful')) {
                    document.getElementById('fullname').value = '';
                    document.getElementById('email').value = '';
                    document.getElementById('phone').value = '';
                    document.getElementById('password').value = '';
                    document.getElementById('role').value = '';
                    document.getElementById('passwordStrength').style.width = '0';
                    const formatHint = document.getElementById('formatHint');
                    if (formatHint) {
                        formatHint.textContent = '📱 Format: 9 digits (e.g., 636906585)';
                        formatHint.className = 'format-hint';
                    }
                }
            }
            
            if (errorMsg) {
                errorMsg.style.display = 'block';
            }
        });
    </script>
</body>
</html>