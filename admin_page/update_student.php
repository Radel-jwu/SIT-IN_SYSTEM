<?php
session_start();
include('../dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access. Please log in."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $name = $_POST['name'];
    $year_lvl = $_POST['year_lvl'];
    $course = $_POST['course'];
    $remaining_sessions = $_POST['remaining_sessions'];

    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update studentinfo table
        $stmt = $conn->prepare("UPDATE studentinfo SET year_lvl = ?, course = ? WHERE idno = ?");
        $stmt->bind_param("ssi", $year_lvl, $course, $idno);
        $stmt->execute();
        $stmt->close();
        
        // Update reservations table
        $stmt = $conn->prepare("UPDATE reservations SET name = ?, remaining_sessions = ? WHERE idno = ?");
        $stmt->bind_param("ssi", $name, $remaining_sessions, $idno);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        echo json_encode(["success" => true, "message" => "Student record updated successfully."]);
    } catch (Exception $e) {
        // Rollback transaction if an error occurs
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Error updating record: " . $e->getMessage()]);
    }
}
?>
