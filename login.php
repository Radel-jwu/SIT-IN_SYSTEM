<?php
session_start();
include('dbconnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // Prevent SQL injection by escaping the inputs (or better: use prepared statements)
    $input_username = $conn->real_escape_string($input_username);

    // Prepare and bind to avoid SQL injection and fetch the user by username
    $stmt = $conn->prepare("SELECT idno, firstname, lastname, password FROM studentinfo WHERE username = ? AND password = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $input_username, $input_password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_id = $user['idno'];
            $firstname = $user['firstname'];
            $lastname = $user['lastname'];

            // Insert login time into generate_reports table
            $login_time = date('h:i A'); // Format time as 9:45 PM or AM
            $date_today = date('Y-m-d'); // Format date as YYYY-MM-DD
            $report_stmt = $conn->prepare("INSERT INTO user_logs (idno, login, date) VALUES (?, ?, ?)");
            if ($report_stmt) {
                $report_stmt->bind_param("iss", $user_id, $login_time, $date_today);
                $report_stmt->execute();
                $report_stmt->close();
            } else {
                die("Error preparing statement: " . $conn->error);
            }

            // Check if the user idno is 1 (admin)
            if ($user_id == 1) {
                // Admin login
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $input_username;
                echo "<script type='text/javascript'>alert('Admin login successful');</script>";
                echo "<script> window.location.href = 'admin_page/admin.php' </script>";  // Redirect to admin dashboard
            } else {
                // Regular user login
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $input_username;
                echo "<script type='text/javascript'>alert('Login successful');</script>";
                echo "<script> window.location.href = 'user_page/index.php' </script>";  // Redirect to the regular user dashboard
            }
        } else {
            echo "No user found with that username!";
            echo "<script type='text/javascript'>alert('No user found with that username!');</script>";
            echo "<script> window.location.href = 'login.html' </script>";
        }
        $stmt->close();
    } else {
        die("Error preparing statement: " . $conn->error);
    }
}
$conn->close();
?>
