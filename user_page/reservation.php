<?php
session_start();

// Check if session is set, otherwise exit
if (!isset($_SESSION['user_id'])) {
    echo "<script type='text/javascript'>alert('You must Login first!');</script>";
    echo "<script> window.location.href = '../login.html'; </script>";
}

include('../dbconnect.php');

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM studentinfo WHERE idno = ?";
$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $user_id); 
$stmt->execute();


// Get the result
$result = $stmt->get_result();

// Store student data if available
$studentData = null;
if ($result->num_rows > 0) {
    // Fetch data from the result
    $studentData = $result->fetch_assoc();
}

// Close the statement and connection
$stmt->close();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idno = $_POST['idno'];
    $name = $_POST['name'];
    $remaining_sessions = $_POST['remaining_sessions'];
    $purpose = $_POST['purpose'];
    $lab = $_POST['lab'];
    $status = "Pending";
    $sql = "INSERT INTO reservations (idno, name, remaining_sessions, purpose, lab, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisss", $idno, $name, $remaining_sessions, $purpose, $lab, $status);
    
    if ($stmt->execute()) {
        echo "<script>alert('Reservation successful!'); window.location.href='../user_page/reservation.php';</script>";
    } else {
        echo "<script>alert('Reservation failed!');</script>";
    }
    
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idno = $_POST['idno'];
    $name = $_POST['name'];
    $purpose = $_POST['purpose'];
    $lab = $_POST['lab'];

    $sql = "INSERT INTO reservations (idno, name, purpose, lab, date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisss", $idno, $name, $remaining_sessions, $purpose, $lab, $status);
    
    if ($stmt->execute()) {
        echo "<script>alert('Reservation successful!'); window.location.href='../user_page/reservation.php';</script>";
    } else {
        echo "<script>alert('Reservation failed!');</script>";
    }
    
    $stmt->close();
}



$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../css/styles.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
    }

    .container {
      display: flex;
    }

    .sidebar {
      width: 250px;
      height: 100vh;
      background-color: #333;
      color: #fff;
      padding: 20px;
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar ul li {
      margin-bottom: 20px;
    }

    .sidebar ul li a {
      color: #fff;
      text-decoration: none;
      font-size: 18px;
      display: block;
      padding: 10px;
      border-radius: 4px;
    }

    .sidebar ul li a:hover {
      background-color: #575757;
    }

    .main-content {
      flex: 1;
      padding: 20px;
    }

    header {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    header h1 {
      font-size: 24px;
    }

    header p {
      font-size: 16px;
      color: #555;
    }

    .reservation-form {
      background-color: white;
      padding: 20px;
      margin-left: 35%;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      margin-top: 20px;
    }

    .reservation-form label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }

    .reservation-form input, .reservation-form select, .reservation-form button {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .reservation-form button {
      background-color: #333;
      color: white;
      font-size: 16px;
      cursor: pointer;
      border: none;
    }

    .reservation-form button:hover {
      background-color: #575757;
    }

    .student-info {
      margin-top: 20px;
      padding: 20px;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <h2>Dashboard</h2>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="#">Notification</a></li>
        <li><a href="#">Edit Profile</a></li>
        <li><a href="#">History</a></li>
        <li><a href="user_page/reservation.php">Reservation</a></li>
        <li>
          <form action="../logout.php" method="post">
            <button type="submit" name="logout" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">
              Logout
            </button>
          </form>
        </li>
      </ul>
    </div>
    
    <div class="main-content">
      <header>
        <h1>Welcome, <?php echo htmlspecialchars($studentData['lastname']); ?></h1>
        <p>Your Reservation Page</p>
      </header>
      
      <h2 style ="text-align: center">Reservation Form</h2>
      <div class="reservation-form">
        <form action="#" method="post">
          <label for="idno">ID Number:</label>
          <input type="text" id="idno" name="idno" value="<?php echo htmlspecialchars($studentData['idno']); ?>" readonly>
          
          <label for="name">Name:</label>
          <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($studentData['firstname']) . " ", htmlspecialchars($studentData['middlename']) . " ", htmlspecialchars($studentData['lastname']); ?>" readonly>
          
          <label for="purpose">Purpose:</label>
          <select id="purpose" name="purpose" required>

          <option value="C Programming">C Programming</option>
          <option value="C# Programming">C# Programming</option>
            <option value="Java Programming">Java Programming</option>
            <option value="PHP">PHP</option>
            <option value="Java">Java</option>
            <option value="ASP.NET">ASP.NET</option>

          </select>
          
          <label for="lab">Lab:</label>
          <select id="lab" name="lab" required>
            <option value="528">528</option>
            <option value="540">540</option>
            <option value="542">542</option>
          </select>
          <label for="remaining_sessions">Remaining Sessions:</label>

          <input type="text" id="remaining_sessions" name="remaining_sessions" value="<?php echo htmlspecialchars($studentData['session']); ?>" readonly>

          
          <button type="submit">Submit</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
