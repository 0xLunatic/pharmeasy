<?php
$servername = "localhost"; // Change if needed
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "pharmeasy"; // Your database name


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check for input parameters
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email']) && isset($data['otp'])) {
    $email = mysqli_real_escape_string($conn, $data['email']);
    $otp = mysqli_real_escape_string($conn, $data['otp']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Invalid email format."]);
        exit();
    }

    // Check if email and OTP exist in the database
    $sql = "SELECT otp FROM users WHERE email='$email' AND otp='$otp'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // OTP verified successfully, now delete the OTP from the database
        $deleteSql = "UPDATE users SET otp=NULL WHERE email='$email'";
        if ($conn->query($deleteSql) === TRUE) {
            echo json_encode(["message" => "OTP verified successfully and deleted."]);
        } else {
            echo json_encode(["error" => "Error deleting OTP: " . $conn->error]);
        }
    } else {
        echo json_encode(["error" => "Invalid OTP or email."]);
    }
} else {
    echo json_encode(["error" => "No input data received."]);
}

$conn->close();
