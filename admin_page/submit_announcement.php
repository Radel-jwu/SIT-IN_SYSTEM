<?php
include('../dbconnect.php');   

// Initialize a success flag
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcement = trim($_POST['announcement']);
    $date = date("Y-m-d"); 

    echo "Current date: " . $date . "<br>"; 

    if (!empty($announcement)) {
        $sql = "INSERT INTO announcements (admin_name, date, description) VALUES ('CCS Admin', '$date', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $announcement);

        if ($stmt->execute()) {
            $success = true;  // Set success to true if the announcement was posted successfully
            // Redirect to admin.php after success
            header("Location: admin.php?success=true");
            exit();  // Make sure to stop execution here after redirect
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Please enter an announcement.";
    }
}

$conn->close();
?>