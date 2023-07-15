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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    // Check if the email already exists in the database
    $stmt = $conn->prepare("SELECT email FROM register WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $insert1 = true; // Set flag if email already exists
        $otp_verified = false; // Set OTP verification flag to false
    }
    else if (isset($_POST["send_otp"])) {
        $name = $_POST["name"];
        $password = $_POST["password"];
        $contact_number = $_POST["contact_number"];

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
            $mail->Subject = "UdyamWell - OTP Verification";
            $mail->Body = "Your OTP for registration at UdyamWell is: <strong>$otp</strong>";

            $mail->send();
            $otp_verified = true;
            $_POST["name"] = $name;
            $_POST["email"] = $email;
            $_POST["password"] = $password;
            $_POST["contact_number"] = $contact_number;
        } catch (Exception $e) {
            // Email sending failed
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } elseif (isset($_POST["register"])) {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $contact_number = $_POST["contact_number"];
        $entered_otp = $_POST["otp"];

        // Check if the entered OTP matches the stored OTP
        if (isset($_SESSION["otp"]) && $_SESSION["otp"] == $entered_otp) {
            // Encrypt the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO register (name, email, password, contact_number) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $contact_number);
            $stmt->execute();

            // Registration successful, set session variables
            $_SESSION["user_id"] = $stmt->insert_id;
            $_SESSION["email"] = $email;

            $insert = true; // Set insert flag to true
            $otp_verified = true; // Set OTP verification flag to true
        } else {
            $otp_verified = true; // Set OTP verification flag to true
        }
    }
}

require_once "common.php";

?>

                <h2>Register</h2>
            </div>
            <div class="form-content">
                <form action="register.php" method="POST">
                    <div class="user-input">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>">
                    </div>
                    <div class="user-input">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                    </div>
                    <div class="user-input">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>">
                    </div>
                    <div class="user-input">
                        <label for="contact_number">Contact Number:</label>
                        <input type="tel" id="contact_number" name="contact_number" required value="<?php echo isset($_POST['contact_number']) ? $_POST['contact_number'] : ''; ?>">
                    </div>
                    <?php if (!$otp_verified) { ?>
                        <button type="submit" name="send_otp">Send OTP</button>
                    <?php } else { ?>
                        <div class="user-input">
                            <label for="otp">OTP:</label>
                            <input type="text" id="otp" name="otp" required>
                        </div>
                        <button type="submit" name="register">Register</button>
                    <?php } ?>
                </form>
            </div>
            <div class="link-container">
                <a class="register-link" href="index.php">Login here</a>
            </div>
        </div>
        <?php
        if ($insert) {
            echo "<p class='success-msg'>Registration successful</p>";
        } else if ($insert1) {
            echo "<p class='success-msg'>Email already registered</p>";
        } else if (!$otp_verified && isset($_POST["send_otp"])) {
            echo "<p class='error-msg'>Email could not be sent. Please try again.</p>";
        } else if ($otp_verified && !isset($_POST["register"])) {
            echo "<p class='error-msg'>OTP Send Sucessfully</p>";
        } else if ($otp_verified && isset($_POST["register"])) {
            echo "<p class='error-msg'>Invalid OTP. Please try again.</p>";
        }
        ?>
    </div>
</body>
</html>
