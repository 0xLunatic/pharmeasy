<?php
require 'dbConnection.php'; // Make sure the path is correct

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
        // Email exists
        echo json_encode(["message" => "Email exists. Proceeding with reset."]);
        
    } else {
        // Email not found
        echo json_encode(["error" => "Email not found. Please check and try again."]);
    }
} else {
    echo json_encode(["error" => "No input data received."]);
}

$conn->close();
