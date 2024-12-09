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

// Check if database exists, if not, create it
if (!$conn->select_db($dbname)) {
    $sql = "CREATE DATABASE $dbname";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully\n";
    } else {
        die("Error creating database: " . $conn->error);
    }
}

// Select the database
$conn->select_db($dbname);

// SQL to create the users table if it does not exist
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    otp VARCHAR(10),
    role VARCHAR(20) DEFAULT 'Customer',
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    // Table created successfully or already exists
    // Ensure 'role' column exists
    $alterSql = "ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(20) DEFAULT 'Customer'";
    if ($conn->query($alterSql) !== TRUE) {
        echo "Error adding role column: " . $conn->error;
    }
} else {
    die("Error creating table: " . $conn->error);
}

// Check for input parameters
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['username'], $data['email'], $data['password'])) {
    $username = mysqli_real_escape_string($conn, $data['username']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $password = mysqli_real_escape_string($conn, $data['password']);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password for security

    // Insert data into the users table with role set to 'Customer'
    $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$hashedPassword', 'Customer')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "New record created successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }

} else {
    echo json_encode(["error" => "No input data received."]);
}

$conn->close();
?>
