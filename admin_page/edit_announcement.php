<?php
include('../dbconnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['annc_id'], $_POST['announcement'])) {
        $annc_id = intval($_POST['annc_id']); // Get announcement ID
        $announcement = $conn->real_escape_string($_POST['announcement']); // Get announcement text

        // Ensure column names match your database schema
        $sql = "UPDATE announcements SET description = '$announcement' WHERE annc_id = $annc_id";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Error updating: " . $conn->error]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Missing required parameters.",
            "received_data" => $_POST
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Expected POST, received " . $_SERVER["REQUEST_METHOD"],
        "received_data" => $_POST
    ]);
}

$conn->close();
?>
