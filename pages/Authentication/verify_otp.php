
<?php
session_start();

require_once 'db.php';
require_once 'send_otp.php';

// Verify registration session
if (
    !isset($_SESSION['temp_email']) ||
    !isset($_SESSION['verification_required'])
) {
    header("Location: register.php");
    exit();
}

$email = $_SESSION['temp_email'];
$error = "";
$success = "";

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verify OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $error = "Invalid request. Please refresh and try again.";
    } else {

        $otp_code = trim($_POST['otp_code'] ?? '');

        if (!preg_match('/^\d{6}$/', $otp_code)) {
            $error = "Please enter a valid 6-digit OTP code.";

        } elseif (verifyOTP($conn, $email, $otp_code)) {

            // Verification successful
            unset($_SESSION['temp_email']);
            unset($_SESSION['verification_required']);
            unset($_SESSION['csrf_token']);
            unset($_SESSION['last_otp_resend']);

            $_SESSION['verification_success'] = true;

            header("Location: login.php?verified=1");
            exit();

        } else {
            $error = "Invalid or expired OTP code. Please try again.";
        }
    }
}

// Resend OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_otp'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $error = "Invalid request. Please refresh and try again.";

    } else {

        $last_resend = $_SESSION['last_otp_resend'] ?? 0;

        // 60-second resend cooldown
        if (time() - $last_resend < 60) {

            $error = "Please wait before requesting another OTP.";

        } else {

            $otp_code = generateOTP();

            if (sendOTP($email, $otp_code)) {

                if (saveOTP($conn, $email, $otp_code)) {

                    $_SESSION['last_otp_resend'] = time();

                    $success = "A new OTP has been sent to your email.";

                } else {
                    $error = "Unable to save OTP. Please try again.";
                }

            } else {
                $error = "Unable to send OTP. Please try again.";
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

    <title>HoyLink - Verify OTP</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto;
        }

        body {
            background-image:
                linear-gradient(rgba(5,10,25,0.55), rgba(5,10,25,0.55)),
                url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1400&q=80');

            background-size: cover;
            background-position: center;

            display: flex;
            align-items: center;
            justify-content: center;

            min-height: 100vh;
            padding: 20px;
        }

        .card {
            max-width: 500px;
            width: 100%;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(8px);
            border-radius: 14px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(2,6,23,0.6);
            text-align: center;
        }

        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            border-radius: 12px;
            background: linear-gradient(135deg,#ffa500,#ff6b6b);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: bold;
            color: #fff;
        }

        h1 {
            color: #fff;
            margin-bottom: 10px;
            font-size: 24px;
        }

        p {
            color: rgba(242,246,251,0.8);
            margin-bottom: 25px;
        }

        input {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.05);
            color: #fff;
            font-size: 20px;
            text-align: center;
            letter-spacing: 8px;
            margin-bottom: 20px;
            outline: none;
        }

        input:focus {
            border-color: #ffa500;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(90deg,#ff8a00,#ff3d00);
            color: #fff;
            font-size: 16px;
            margin-bottom: 12px;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-resend {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            color: #ffa500;
        }

        .btn-resend:hover {
            background: rgba(255,165,0,0.1);
        }

        .error {
            background: rgba(255,50,50,0.12);
            color: #ffd6d6;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .success {
            background: rgba(52,211,153,0.12);
            color: #d7ffec;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .info {
            font-size: 14px;
            color: rgba(242,246,251,0.6);
        }

        .email-highlight {
            color: #ffa500;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="card">

    <div class="logo">HL</div>

    <h1>🔐 Verify Your Email</h1>

    <p>
        We've sent a 6-digit verification code to:<br>
        <span class="email-highlight">
            <?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>
        </span>
    </p>

    <?php if ($error): ?>
        <div class="error">
            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success">
            <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>"
        >

        <input
            type="text"
            name="otp_code"
            placeholder="000000"
            maxlength="6"
            pattern="[0-9]{6}"
            inputmode="numeric"
            autocomplete="one-time-code"
            required
        >

        <button type="submit" name="verify_otp" class="btn">
            Verify Account
        </button>

    </form>

    <form method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>"
        >

        <button type="submit" name="resend_otp" class="btn btn-resend">
            📧 Resend OTP Code
        </button>

    </form>

    <p class="info">
        ⏰ OTP code expires in 3 minutes.
    </p>

    <p class="info">
        💡 Check your Spam/Junk folder if you don't see the email.
    </p>

</div>

</body>
</html>
