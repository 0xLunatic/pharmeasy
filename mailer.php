<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Include the database connection file
require 'db_connection.php'; // Make sure the path is correct

$mail = new PHPMailer(true);

// Check for input parameters
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email'])) {
    $email = mysqli_real_escape_string($conn, $data['email']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Invalid email format."]);
        exit();
    }

    // Check if email exists in the database
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Generate a random 6-digit OTP
        $otp = rand(100000, 999999); // Generates a random number between 100000 and 999999

        // Store the OTP in the database
        $updateSql = "UPDATE users SET otp='$otp' WHERE email='$email'";
        if ($conn->query($updateSql) === TRUE) {
            // Prepare the email for sending
            try {
                // Server settings
                $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
                $mail->isSMTP(); // Send using SMTP
                $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
                $mail->SMTPAuth = true; // Enable SMTP authentication
                $mail->Username = 'fzntaufiq@gmail.com'; // SMTP username
                $mail->Password = 'rbzxliaadfnmtegi'; // SMTP password
                $mail->SMTPSecure = 'tls'; // Enable implicit TLS encryption
                $mail->CharSet = "UTF-8";
                $mail->Port = 587; // TCP port to connect to

                // Recipients
                $mail->setFrom('from@pharmeasy.com', 'Pharmeasy Forgot Password');
                $mail->addAddress($email); // Add a recipient
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'PharmEasy OTP Code Confirmation';
                $mail->Body = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>body{font-family:Arial,sans-serif;background-color:#f4f4f4;margin:0;padding:0}.email-container{max-width:600px;margin:20px auto;background-color:#ffffff;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1);overflow:hidden}.email-header{background-color:#007bff;color:#ffffff;text-align:center;padding:20px}.email-header h1{margin:0;font-size:24px}.email-content{padding:20px;color:#333333;line-height:1.6}.otp-code{font-size:32px;font-weight:bold;color:#007bff;text-align:center;padding:10px 0}.email-footer{text-align:center;padding:20px;font-size:12px;color:#888888}</style></head><body><div class="email-container"><div class="email-header"><h1>PharmEasy Password Reset</h1></div><div class="email-content"><p>Hello, Pharmies!</p><p>We received a request to reset your PharmEasy account password. To proceed, please use the OTP code below:</p><div class="otp-code">' . $otp . '</div><p>Please enter this OTP on the PharmEasy app or website to confirm your password reset request. This OTP is valid for 10 minutes.</p><p>If you didn\'t request a password reset, you can ignore this email. Your account remains secure.</p><p>Best regards,<br>PharmEasy Team</p></div><div class="email-footer">&copy; ' . date("Y") . ' PharmEasy. All rights reserved.<br>This is an automated message, please do not reply.</div></div></body></html>';

                $mail->send();

            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo json_encode(["error" => "Error updating OTP in database: " . $conn->error]);
        }
    } else {
        // Provide feedback if the email does not exist in the database
        echo json_encode(["error" => "Email not found. Please check and try again."]);
    }
} else {
    echo json_encode(["error" => "No input data received."]);
}

$conn->close();
