<?php
require_once 'config/db.php';

// Function to update addiction types statistics
function updateAddictionTypeStats($addiction_type) {
    global $conn;
    
    // First check if the addiction type exists
    $sql = "SELECT * FROM addiction_types WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $addiction_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing count
        $sql = "UPDATE addiction_types SET count = count + 1 WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $addiction_type);
    } else {
        // Insert new addiction type
        $sql = "INSERT INTO addiction_types (name, count) VALUES (?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $addiction_type);
    }
    
    return $stmt->execute();
}

// Function to update monthly admissions statistics
function updateMonthlyAdmissions($admission_date) {
    global $conn;
    
    $month = date('M', strtotime($admission_date));
    $year = date('Y', strtotime($admission_date));
    
    // Check if entry exists for this month and year
    $sql = "SELECT * FROM monthly_admissions WHERE month = ? AND year = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing count
        $sql = "UPDATE monthly_admissions SET count = count + 1 WHERE month = ? AND year = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $month, $year);
    } else {
        // Insert new month entry
        $sql = "INSERT INTO monthly_admissions (month, year, count) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $month, $year);
    }
    
    return $stmt->execute();
}

// Function to update all statistics when a new beneficiary is added
function updateAllStats($addiction_type, $admission_date) {
    $success = true;
    
    // Start transaction
    global $conn;
    $conn->begin_transaction();
    
    try {
        // Update addiction type statistics
        if (!updateAddictionTypeStats($addiction_type)) {
            throw new Exception("Failed to update addiction type statistics");
        }
        
        // Update monthly admissions
        if (!updateMonthlyAdmissions($admission_date)) {
            throw new Exception("Failed to update monthly admissions");
        }
        
        // If everything is successful, commit the transaction
        $conn->commit();
    } catch (Exception $e) {
        // If there's an error, rollback the changes
        $conn->rollback();
        $success = false;
    }
    
    return $success;
}
?> 