<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nasha_mukti_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$hashedPassword = password_hash('admin456', PASSWORD_DEFAULT);

$sql = "
INSERT INTO users (username, password, email, role)
SELECT 'admin2', '$hashedPassword', 'admin2@nashamukti.com', 'admin'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'admin2')
";

// Execute query
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
