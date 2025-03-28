<?php
// Start the session
session_start();
include('dbconnect.php');

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];  // Get user ID before destroying session
    $logout_time = date('h:i A'); // Format logout time as 9:45 PM or AM

    // Update the latest log entry where idno matches the user ID and log_id is the most recent
    $update_stmt = $conn->prepare("UPDATE user_logs SET logout = ? WHERE idno = ? ORDER BY idno DESC LIMIT 1");
    if ($update_stmt) {
        $update_stmt->bind_param("si", $logout_time, $user_id);
        $update_stmt->execute();
        $update_stmt->close();
    }

    // Decrement remaining_sessions in the reservations table if greater than zero
    $update_reservation = $conn->prepare("UPDATE studentinfo SET session = session - 1 WHERE idno = ? AND session > 0");
    if ($update_reservation) {
        $update_reservation->bind_param("i", $user_id);
        $update_reservation->execute();
        $update_reservation->close();
    }
}

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect the user to the login page or home page
echo "<script type='text/javascript'>alert('Log-out successful!');</script>";
echo "<script>window.location.href = 'login.html';</script>";
exit();

?>
