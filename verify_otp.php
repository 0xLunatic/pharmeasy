<?php
require 'dbConnection.php'; // Make sure the path is correct

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
