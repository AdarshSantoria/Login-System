<?php
session_start();
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM register WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row["password"])) {
            // Login successful, set session variables
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["email"] = $row["email"];

            // Redirect to the home page or any other desired page
            header("Location: home.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Invalid email";
    }
}

require_once "common.php";

?>

                <h2>Login</h2>
            </div>
            <div class="form-content">
                <form action="login.php" method="POST">
                    <div class="user-input">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="user-input">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit">Login</button>
                </form>
                <?php if (isset($error)) { ?>
                    <p class="error-msg"><?php echo $error; ?></p>
                <?php } ?>
            </div>
            <div class="link-container">
                <a class="forgot-link" href="forgot_password.php">Forgot Password?</a>
                <a class="register-link" href="register.php">Register here</a>
            </div>
        </div>
    </div>
</body>
</html>
