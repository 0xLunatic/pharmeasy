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

if (isset($data['email']) && isset($data['new_password']) && isset($data['confirm_password'])) {
    $email = $data['email'];
    $new_password = $data['new_password'];
    $confirm_password = $data['confirm_password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Invalid email format."]);
        exit();
    }

    // Check if new password and confirm password match
    if ($new_password !== $confirm_password) {
        echo json_encode(["error" => "Passwords do not match."]);
        exit();
    }

    // Hash the new password (using bcrypt)
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Use prepared statements to update the password in the database
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
    $stmt->bind_param("ss", $hashed_password, $email);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Password updated successfully."]);
    } else {
        echo json_encode(["error" => "Error updating password: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "No input data received."]);
}

$conn->close();
?>
