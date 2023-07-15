# Login System

This project implements a secure and user-friendly login, registration, and password reset system for web applications. It provides a robust authentication mechanism to handle user authentication and password management. The system utilizes PHP and a MySQL database to store and retrieve user information.

## Features

- User Registration: Allows users to create new accounts by providing their email address and a secure password.
- Account Activation: Sends an activation email to the user's email address to verify and activate their account.
- Login: Authenticates users by comparing their credentials against the stored user information in the database.
- Password Hashing: Safely stores user passwords using bcrypt or another secure hashing algorithm.
- Password Reset: Enables users to reset their passwords if they forget them by sending a password reset email with an OTP (One-Time Password).
- Email Notifications: Uses PHPMailer or a similar library to send emails for account activation, password reset, and other notifications.
- Input Validation: Validates user inputs to ensure they meet the required format and prevent common security vulnerabilities.
- Session Management: Utilizes session variables to maintain user login status and protect against session hijacking.

## Installation

1. Clone the repository or download the project files.
2. Configure the database settings in the `config.php` file to match your MySQL database credentials.
3. Import the provided SQL file (`database.sql`) into your MySQL database to create the required tables.
4. Make sure the necessary dependencies, such as PHPMailer, are installed. You can use Composer to manage dependencies.
5. Customize the email templates in the `emails` directory to match your application's branding and requirements.
6. Place the project files in your web server's document root directory.
7. Access the application through the browser to start using the login, registration, and password reset functionality.

## Usage

- Register a New Account:
  - Access the registration page and provide a valid email address and password.
  - An activation email will be sent to the provided email address.
  - Click the activation link in the email to activate the account.

- Login:
  - Enter the registered email address and password on the login page.
  - Upon successful login, the user will be redirected to the home page.

- Forgot Password / Password Reset:
  - If a user forgets their password, they can click the "Forgot Password" link.
  - Enter the registered email address to initiate the password reset process.
  - A password reset email with an OTP will be sent to the provided email address.
  - Enter the OTP and a new password on the password reset page.
  - Upon successful password reset, the user can log in with the new password.

## Security Considerations

- Store passwords securely using a strong hashing algorithm (e.g., bcrypt).
- Implement measures to prevent common security threats, such as SQL injection and cross-site scripting (XSS) attacks.
- Apply input validation and sanitization techniques to ensure data integrity and prevent malicious inputs.
- Use HTTPS to encrypt data transmitted between the client and the server.
- Implement rate limiting and account lockout mechanisms to prevent brute-force attacks.

## Credits

- This project utilizes the PHPMailer library for email handling. More information can be found at [https://github.com/PHPMailer/PHPMailer](https://github.com/PHPMailer/PHPMailer).

## License

This project is licensed under the [MIT License](https://en.wikipedia.org/wiki/MIT_License)https://en.wikipedia.org/wiki/MIT_License).
