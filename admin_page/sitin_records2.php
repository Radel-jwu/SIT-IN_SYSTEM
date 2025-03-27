<?php
session_start();
include('../dbconnect.php'); // Ensure this file connects to your database

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Retrieve and sanitize form data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';

    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
    $student_purpose = isset($_POST['student_purpose']) ? trim($_POST['student_purpose']) : '';
    $student_lab = isset($_POST['student_lab']) ? trim($_POST['student_lab']) : '';
    $remaining_sessions = $_POST['student_sessions'] > 0 ? $_POST['student_sessions'] - 1 : 0;

    $status = "Active";

    // Validate the data
    
    // Prepare the INSERT query to add a new record to the reservations table
    $stmt = $conn->prepare("INSERT INTO reservations (idno, name, purpose, lab, remaining_sessions, status) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters:
    // "sssis" means: string (idno), string (purpose), string (lab), integer (session), string (status)
    $stmt->bind_param("ssssis", $student_id, $name, $student_purpose, $student_lab, $remaining_sessions, $status);

    if ($stmt->execute()) {
        // Update session in studentinfo table
        $update_stmt = $conn->prepare("UPDATE studentinfo SET session = GREATEST(session - 1, 0) WHERE idno = ?");
        if ($update_stmt) {
            $update_stmt->bind_param("s", $student_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
        echo "<script>alert('Record inserted and session updated successfully!'); window.location.href='admin.php';</script>";
    } else {
        echo "Error inserting record: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>