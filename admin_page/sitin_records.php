    <?php
    session_start();
    include('../dbconnect.php'); // Ensure this file connects to your database

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
        $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
        
        if (!empty($student_id)) {
            $stmt = $conn->prepare("UPDATE studentinfo SET session = GREATEST(session - 1, 0) WHERE idno = ?");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("s", $student_id);
            
            if ($stmt->execute()) {
                echo "<script>alert('Record updated successfully!'); window.location.href='admin.php';</script>";
            } else {
                echo "Error updating record: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            echo "Invalid Student ID.";
        }
    } else {
        echo "Invalid request method.";
    }

    $conn->close();
    ?>