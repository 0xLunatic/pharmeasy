<?php
// Database configuration
$servername = "localhost"; // Change if necessary
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "pharmeasy"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]));
}

// Check for input parameters
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email'], $data['password'])) {
    // Sanitize inputs
    $email = mysqli_real_escape_string($conn, $data['email']);
    $password = $data['password']; // Password will be checked using password_verify

    // Prepare the SQL statement
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            // User exists, fetch user data
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user['password'])) {
                echo json_encode(["status" => "success", "message" => "Login successful"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid password"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "User not found"]);
        }
    } else {
        // Query failed
        echo json_encode(["status" => "error", "message" => "Database query error: " . $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No input data received."]);
}

// Close the database connection
$conn->close();
?>
