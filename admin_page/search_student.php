<?php
include('../dbconnect.php');

header("Content-Type: application/json"); // Ensure response is JSON
error_reporting(0); // Hide errors from being sent in the response

$response = ["success" => false, "message" => "An error occurred."];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["idno"])) {
        echo json_encode(["success" => false, "message" => "ID number is required."]);
        exit;
    }

    $idno = $_POST["idno"];
    $sql = "SELECT * FROM studentinfo WHERE idno = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $idno);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $student = $result->fetch_assoc();
            $response = [
                "success" => true,
                "idno" => $student["idno"],
                "fullname" => $student["firstname"] . " " . $student["lastname"],   
                "session" => $student["session"]
            ];
        } else {
            $response = ["success" => false, "message" => "Student not found."];
        }

        $stmt->close();
    } else {
        $response = ["success" => false, "message" => "Database error."];
    }
}

$conn->close();
echo json_encode($response);
?>
