<?php
include('../dbconnect.php');

function getReservationCounts() {
    global $conn;

    $query = "SELECT purpose, COUNT(*) as count FROM reservations GROUP BY purpose";
    $result = mysqli_query($conn, $query);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['purpose']] = $row['count'];
    }

    return json_encode($data);
}

echo getReservationCounts(); // Output data as JSON for JavaScript
?>
