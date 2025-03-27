<?php
session_start();

// Check if session is set, otherwise exit
if (!isset($_SESSION['user_id'])) {
    echo "<script type='text/javascript'>alert('You must Login first!');</script>";
    echo "<script> window.location.href = '../login.html'; </script>";
    exit();
}

include('../dbconnect.php');

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            session, 
            lastname, 
            course, 
            year_lvl, 
            email, 
            address 
        FROM studentinfo 
        WHERE idno = ? 
        ";  
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id); 
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Store reservation data if available
$studentData = null;
if ($result->num_rows > 0) {
    $studentData = $result->fetch_assoc();
}

// Close the statement and connection
$stmt->close();
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
      height: 120vh;
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

    .cards {
      display: flex;
      gap: 20px;
      margin-bottom: 20px;
    }

    .card {
      display: flex;
      flex-direction: column; /* Stack the content vertically inside the card */
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      flex: 1;
    }

    .chart {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      flex: 1;
    }

    .chart h3 {
      font-size: 18px;
      margin-bottom: 10px;
    }

    .chart-placeholder {
      height: 200px;
      background-color: #e0e0e0;
      border-radius: 4px;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #777;
    }
    .mini-card{
        margin-top:10%;
        width: 100%;
        height: 30%;
        background-color:whitesmoke;
        padding:3%;
    }
    .rules_card {
    width: 500px;
    height: 720px;
    padding: 15px;
    border: 1px solid #ccc;
    border-radius: 10px;
    overflow-y: auto;
    background-color: #f9f9f9;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

  .rules_card h4 {
      font-size: 18px;
      margin-bottom: 15px;
  }

  .rules_card p, .card ul {
      font-size: 14px;
      line-height: 1.6;
  }

  .rules_card ul {
      padding-left: 20px;
  }

  .rules_card li {
      margin-bottom: 5px;
  }
  .flex-container {
    display: flex;
    align-items: center; /* Vertically center the image and text */
    margin-bottom: 10px;  /* Optional: Add some spacing between the lines */
}

.flex-container img {
    margin-right: 10px; /* Space between image and text */
}
.header_card{
  border-radius:10px;
  background-color:#333;
  color:white;
  position:sticky;
  top:0;
  z-index: 1;
  padding: 15px;
}


  </style>
  
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <h2>Dashboard</h2>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Notification</a></li>
        <li><a href="#">Edit Profile</a></li>
        <li><a href="#">History</a></li>
        <li><a href="reservation.php">Reservation</a></li>
        
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
      <header style="text-align:center">
        <h1>Welcome to the Dashboard</h1>
        <p>Overview of your recent activities</p>
      </header>
      <section class="cards">
      <div class="card">
        <div class="header_card">
        <h1 style="text-align:center">Student Information</h1>
        </div>
        <img src="../image/pic2.webp" width="35%" height="25%" style="border-radius: 50%; border: 1px solid violet; margin-left: 33%; margin-top:5%"> 
        <hr style="margin-top:10%">
        <br>
        
        <div class="flex-container">
          <img src='../image/icon1.png' width="5%">
          <p><b>Name:</b> <?php echo $studentData ? $studentData['lastname'] : 'No data available'; ?></p>
        </div>     
        <br>  
        <div class="flex-container">
          <img src='../image/icon2.png' width="5%">
          <p><b>Course:</b> <?php echo $studentData ? $studentData['course'] : 'No data available'; ?></p>
        </div>
        <br>
        <div class="flex-container">
          <img src='../image/icon3.png' width="5%">
          <p><b>Year:</b> <?php echo $studentData ? $studentData['year_lvl'] : 'No data available'; ?></p>
        </div>
        <br>
        <div class="flex-container">
          <img src='../image/icon4.png' width="5%">
          <p><b>Email:</b> <?php echo $studentData ? $studentData['email'] : 'No data available'; ?></p>
        </div>
        <br>
        <div class="flex-container">
          <img src='../image/icon5.png' width="5%">
          <p><b>Address:</b> <?php echo $studentData ? $studentData['address'] : 'No data available'; ?></p>
        </div>
        <br>
        <div class="flex-container">
          <img src='../image/icon6.png' width="5%">
          <p><b>Session:</b> <?php echo $studentData ? $studentData['session'] : 'No data available'; ?></p>
        </div>
      </div>

      
      
        <div class="rules_card">
          <div class="header_card">
            <h1 style="text-align:center">Announcement</h1>
          </div>
          <?php
            include('../dbconnect.php');

            // SQL query
            $sql = "SELECT * FROM announcements ORDER BY date DESC";
            $result = $conn->query($sql);

            // Check if query was successful
            if ($result === false) {
                die("Database query failed: " . $conn->error);
            }

            // Check if there are rows
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<p><br><br><strong>CCS Admin | ' . date('Y-M-d', strtotime($row['date'])) . '</strong></p>';
                    echo '<div class="mini-card">';
                    echo '<br><br><p>' . htmlspecialchars($row['description']) . '</p>';
                    echo '</div>';
                    echo '<hr style="margin-top:10%">';
                }
            } else {
                echo '<p>No announcements available.</p>';
            }

            // Close the database connection
            $conn->close();
            ?>

        </div>
        <div class="rules_card" style="width:400px">
          <div class="header_card">
            <h4 style="text-align:center"><strong>University of Cebu</strong><br><br>
            COLLEGE OF INFORMATION & COMPUTER STUDIES</h4>
          </div>
          <br><br><strong>LABORATORY RULES AND REGULATIONS</strong>
          <br><br><p>
            To avoid embarrassment and maintain camaraderie with your 
            friends and superiors at our laboratories, please observe the 
            following:
          </p>
          <br><br><p>
            1. Maintain silence, proper decorum, and discipline inside the 
            laboratory. Mobile phones, walkmans and other personal pieces
             of equipment must be switched off.
          </p>
          <br><br><p>
            2. Games are not allowed inside the lab. This includes computer-related 
            games, card games and other games that may disturb the operation of the lab.
          </p>
          <br><br><p>
            3. Surfing the Internet is allowed only with the permission of the 
            instructor. Downloading and installing of software are strictly prohibited.
          </p>
          <br><br><p>
            4. Getting access to other websites not related to the course (especially 
            pornographic and illicit sites) is strictly prohibited.
          </p>
          <br><br><p>
            5. Deleting computer files and changing the set-up of the computer is a 
            major offense.
          </p>
          <br><br><p>
            6. Observe computer time usage carefully. A fifteen-minute allowance 
            is given for each use. Otherwise, the unit will be given to those who wish to 
            "sit-in".
          </p>
          <br><br><p>
            7. Observe proper decorum while inside the laboratory.
          </p>
            <ul style="margin-left:5%"><br>
              <li>Do not get inside the lab unless the instructor is present.</li>
              <li>All bags, knapsacks, and the likes must be deposited at the counter.</li>
              <li>Do not get inside the lab unless the instructor is present.</li>
              <li>Follow the seating arrangement of your instructor.</li>
              <li>At the end of class, all software programs must be closed.</li>
              <li>Return all chairs to their proper places after using.</li>
            </ul>
            <br><br><p>
              8. Chewing gum, eating, drinking, smoking, and other forms of vandalism are 
              prohibited inside the lab.
            </p>
            <br><br><p>
              9. Anyone causing a continual disturbance will be asked to leave the lab. Acts or 
              gestures offensive to the members of the community, including public display of 
              physical intimacy, are not tolerated.
            </p>
            <br><br><p>
              10. Persons exhibiting hostile or threatening behavior such as yelling, 
              swearing, or disregarding requests made by lab personnel will be asked 
              to leave the lab.
            </p>
            <br><br><p>
              11. For serious offense, the lab personnel may call the Civil Security 
              Office (CSU) for assistance.
            </p>
            <br><br><p>
              12. Any technical problem or difficulty must be addressed to the laboratory 
              supervisor, student assistant or instructor immediately.
            </p>
            <br><h3>DISCIPLINARY ACTION</h3>
            <ul style="margin-left:5%"><br>
              <li>First Offense - The Head or the Dean or OIC recommends to the 
                Guidance Center for a suspension from classes for each offender.
              </li>
              <li>First Offense - The Head or the Dean or OIC recommends to the 
                Guidance Center for a suspension from classes for each offender.
              </li>
            </ul>
        </div>
    </section>
    </div>
  </div>
</body>
</html>
