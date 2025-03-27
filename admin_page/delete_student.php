<?php
session_start();
header('Content-Type: application/json');
include('../dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access. Please log in."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['idno'])) {
        $idno = $_POST['idno'];

        // Check if student exists
        $check = $conn->prepare("SELECT idno FROM studentinfo WHERE idno = ?");
        $check->bind_param("s", $idno);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows === 0) {
            echo json_encode(["success" => false, "message" => "Student record not found."]);
            $check->close();
            exit;
        }
        $check->close();

        // Begin transaction to ensure atomicity
        $conn->begin_transaction();

        try {
            // Delete records from user_logs (first, due to FK constraint)
            $stmt_logs = $conn->prepare("DELETE FROM user_logs WHERE idno = ?");
            $stmt_logs->bind_param("s", $idno);
            if (!$stmt_logs->execute()) {
                throw new Exception("Failed to delete user logs: " . $stmt_logs->error);
            }
            $stmt_logs->close();

            // Delete records from reservations
            $stmt_reservations = $conn->prepare("DELETE FROM reservations WHERE idno = ?");
            $stmt_reservations->bind_param("s", $idno);
            if (!$stmt_reservations->execute()) {
                throw new Exception("Failed to delete reservations: " . $stmt_reservations->error);
            }
            $stmt_reservations->close();

            // Delete student record
            $stmt_student = $conn->prepare("DELETE FROM studentinfo WHERE idno = ?");
            $stmt_student->bind_param("s", $idno);
            if (!$stmt_student->execute()) {
                throw new Exception("Failed to delete student record: " . $stmt_student->error);
            }
            $stmt_student->close();

            // Commit transaction if all deletions succeed
            $conn->commit();
            echo json_encode(["success" => true, "message" => "Student record deleted successfully."]);

        } catch (Exception $e) {
            // Rollback changes in case of error
            $conn->rollback();
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
