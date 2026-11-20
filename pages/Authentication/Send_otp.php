
<?php
// send_otp.php - Secure OTP Verification
require_once 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer classes
require_once 'src/PHPMailer.php';
require_once 'src/SMTP.php';
require_once 'src/Exception.php';

// Send OTP Email
function sendOTP($email, $otp_code) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USERNAME');
        $mail->Password = getenv('SMTP_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom(getenv('SMTP_USERNAME'), 'HoyLink');
        $mail->addAddress($email);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'HoyLink - OTP Verification Code';

        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container {
                    max-width: 500px;
                    margin: 0 auto;
                    padding: 20px;
                    background: #f5f5f5;
                    border-radius: 10px;
                }
                .otp-code {
                    font-size: 32px;
                    font-weight: bold;
                    color: #ff8a00;
                    text-align: center;
                    padding: 20px;
                    background: white;
                    border-radius: 10px;
                    letter-spacing: 5px;
                }
                .header {
                    background: linear-gradient(90deg, #ff8a00, #ff3d00);
                    color: white;
                    padding: 15px;
                    text-align: center;
                    border-radius: 10px 10px 0 0;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>HoyLink Verification</h2>
                </div>
                <h3>Hello!</h3>
                <p>Thank you for registering with HoyLink.
                Please use the following OTP code to verify your email:</p>
                <div class='otp-code'>
                    <strong>$otp_code</strong>
                </div>
                <p>This code will expire in <strong>3 minutes</strong>.</p>
                <p>If you didn't request this, please ignore this email.</p>
                <hr>
                <small>HoyLink - Hargeisa Online House Sales Platform</small>
            </div>
        </body>
        </html>
        ";

        $mail->AltBody = "Your OTP code is: $otp_code. It expires in 3 minutes.";

        $mail->send();

        return true;

    } catch (Exception $e) {
        error_log("OTP email delivery failed.");
        return false;
    }
}

// Generate Secure 6-Digit OTP
function generateOTP() {
    return sprintf("%06d", random_int(0, 999999));
}

// Save OTP to Database
function saveOTP($conn, $email, $otp_code) {
    $expires_at = date('Y-m-d H:i:s', strtotime('+3 minutes'));

    // Delete previous OTP
    $stmt = $conn->prepare(
        "DELETE FROM otp_verifications WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();

    // Insert new OTP
    $stmt = $conn->prepare(
        "INSERT INTO otp_verifications (email, otp_code, expires_at)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param("sss", $email, $otp_code, $expires_at);
    $result = $stmt->execute();
    $stmt->close();

    return $result;
}

// Verify OTP
function verifyOTP($conn, $email, $otp_code) {

    // Retrieve OTP from database
    $stmt = $conn->prepare(
        "SELECT otp_code, expires_at
         FROM otp_verifications
         WHERE email = ?
         ORDER BY id DESC
         LIMIT 1"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        $stored_otp = trim($row['otp_code']);
        $input_otp = trim($otp_code);
        $expires_time = strtotime($row['expires_at']);

        // Check OTP and expiration
        if (
            hash_equals($stored_otp, $input_otp) &&
            $expires_time > time()
        ) {

            // Mark user as verified
            $update = $conn->prepare(
                "UPDATE users SET is_verified = 1 WHERE email = ?"
            );

            $update->bind_param("s", $email);
            $update->execute();
            $update->close();

            // Delete used OTP
            $delete = $conn->prepare(
                "DELETE FROM otp_verifications WHERE email = ?"
            );

            $delete->bind_param("s", $email);
            $delete->execute();
            $delete->close();

            $stmt->close();
            return true;
        }
    }

    $stmt->close();
    return false;
}
?>
