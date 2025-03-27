<?php
include('dbconnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get all the data using POST method
    $idno = $_POST['idno'];
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $course = $_POST['course'];
    $yearlevel = $_POST['yearlevel'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $session = 30;
    $address = $_POST['address'];


    // Prepare the SQL statement for insertion
    $stmt = $conn->prepare("INSERT INTO studentinfo (idno, lastname, firstname, middlename, course, year_lvl, email, username, password, session, address) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $idno, $lastname, $firstname, $middlename, $course, $yearlevel, $email, $username, $password, $session, $address);

    // Execute the query
    if ($stmt->execute()) {
        // Success
        echo "<script type='text/javascript'>alert('New record created successfully');</script>";
        echo "<script> window.location.href = 'login.html'; </script>";
    } else {
        // Error occurred
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    // Close the prepared statement and connection
    $stmt->close();
    $conn->close();
}
?>
