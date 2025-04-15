<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nasha_mukti1_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create tables if they don't exist
$sql = "CREATE TABLE IF NOT EXISTS centers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    state VARCHAR(100),
    city VARCHAR(100),
    contact_person VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    capacity INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS beneficiaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    center_id INT,
    name VARCHAR(255) NOT NULL,
    age INT,
    gender VARCHAR(20),
    address TEXT,
    phone VARCHAR(20),
    addiction_type VARCHAR(100),
    admission_date DATE,
    status VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id)
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS interventions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    beneficiary_id INT,
    intervention_type VARCHAR(100),
    description TEXT,
    date DATE,
    outcome TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id)
)";

$conn->query($sql);
?> 