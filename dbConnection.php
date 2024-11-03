<?php

// db_connection.php

$servername = "besy6pezobyodedsajkk-mysql.services.clever-cloud.com"; // Change if needed
$username = "umvllcd3qeuol4os"; // Your database username
$password = "QUl974byTu0sw8MZpyDb"; // Your database password
$dbname = "besy6pezobyodedsajkk"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it does not exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating database: " . $conn->error;
}

// Close the connection to the server
$conn->close();

// Reconnect to the specific database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection to the database
if ($conn->connect_error) {
    die("Connection to database failed: " . $conn->connect_error);
}

?>
