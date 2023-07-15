<?php
session_start();
require_once "config.php";
require_once "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$insert = false;
$insert1 = false;
$otp_verified = false;
$reset_successful = false;

// Function to generate a random OTP
function generateOTP($length = 6) {
    $otp = "";
    $characters = "0123456789";
    $charLength = strlen($characters);
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[rand(0, $charLength - 1)];
    }
    return $otp;
}

$email = ""; // Initialize email variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $newPassword = $_POST["password"];
    $enteredOTP = $_POST["otp"];

    if (isset($_POST["send_otp"])) {
        // Check if the email exists in the database
        $stmt = $conn->prepare("SELECT email FROM register WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Generate and store a new OTP
            $otp = generateOTP();
            $_SESSION["otp"] = $otp;

            // Send verification email with OTP
            $mail = new PHPMailer(true);
            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host = "smtp.gmail.com"; // Replace with your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = "adarshsantoria@gmail.com"; // Replace with your SMTP username
                $mail->Password = "ciodrbjbrvvdkqvg"; // Replace with your SMTP password
                $mail->SMTPSecure = "tls";
                $mail->Port = 587; // Replace with your SMTP port

                // Sender and recipient settings
                $mail->setFrom("adarshsantoria@gmail.com", "Test"); // Replace with your name and email address
                $mail->addAddress($email);

                // Email content
                $mail->isHTML(true);
                $mail->Subject = "UdyamWell - Password Reset OTP";
                $mail->Body = "Your OTP for password reset at UdyamWell is: <strong>$otp</strong>";

                $mail->send();
                $otp_verified = true;
            } catch (Exception $e) {
                // Email sending failed
                echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $insert1 = true; // Set flag if email does not exist
            $otp_verified = false; // Set OTP verification flag to false
        }
    }

    if (isset($_POST["reset_password"])) {
        // Check if the entered OTP matches the stored OTP
        if (isset($_SESSION["otp"]) && $_SESSION["otp"] == $enteredOTP) {
            $otp_verified = false;
            // Encrypt the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the password in the database
            $stmt = $conn->prepare("UPDATE register SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashedPassword, $email);
            $stmt->execute();

            // Password reset successful
            $reset_successful = true;
        } else {
            $otp_verified = true; // Set OTP verification flag to true
        }
    }
}

require_once "common.php";

?>

                <h2>Reset Password</h2>
            </div>
            <div class="form-content">
                <form action="forgot_password.php" method="POST">
                    <div class="user-input">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required value="<?php echo $email; ?>">
                    </div>
                    <?php if (!$otp_verified) { ?>
                        <button type="submit" name="send_otp">Send OTP</button>
                    <?php } else { ?>
                        <div class="user-input">
                            <label for="otp">OTP:</label>
                            <input type="text" id="otp" name="otp" required>
                        </div>
                        <div class="user-input">
                            <label for="password">New Password:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <button type="submit" name="reset_password">Reset Password</button>
                    <?php } ?>
                </form>
                <?php if ($insert1) { ?>
                    <p class="error-msg">Email not found</p>
                <?php } else if ($reset_successful) { ?>
                    <p class="success-msg">Password Reset Successful</p>
                <?php } else if ($otp_verified && !empty($enteredOTP) && $enteredOTP != $_SESSION["otp"]) { ?>
                    <p class="error-msg">Invalid OTP</p>
                <?php } ?>
            </div>
            <div class="link-container">
                <a class="register-link" href="index.php">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>
