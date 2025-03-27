<?php
include('../dbconnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['idno'])) {
    $idno = intval($_POST['idno']);

    // Delete reservation where ID number matches
    $sql = "DELETE FROM reservations WHERE idno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idno);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Reservation deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error deleting reservation: " . $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
?>
