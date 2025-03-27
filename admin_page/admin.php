
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  echo json_encode(["success" => false, "message" => "Unauthorized access. Please log in."]);
  echo "<script> window.location.href = '../login.html'; </script>";

  exit;
}
?>

<?php
function getTotalStudents() {

  include('../dbconnect.php');

    // Query to count students
    $sql = "SELECT COUNT(*) AS total FROM studentinfo";
    $result = $conn->query($sql);

    // Fetch and return the count
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'];
    } else {
        return 0; // If no records found
    }

    // Close connection
    $conn->close();
}
function getAllTotalSitIn() {

  include('../dbconnect.php');

  $sql = "SELECT COUNT(*) AS total_sit_in FROM reservations";
  $result = $conn->query($sql);

  if (!$result) {
      die("Query failed: " . $conn->error);
  }

  // Get the total count from the result
  $row = $result->fetch_assoc();
  return $row['total_sit_in'];
}

function getTotalSitIn() {

  include('../dbconnect.php');

    // Query to count students
    $sql = "SELECT COUNT(*) AS total FROM reservations WHERE status = 'Active'";
    $result = $conn->query($sql);

    // Fetch and return the count
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'];
    } else {
        return 0; // If no records found
    }

    // Close connection
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../css/styles.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    body {
      font-family: Arial, sans-serif;
    }

    .container {

      display: flex;
      width: 100%;
    }
    .card {
      background: white;
      padding: 15px;
      border-radius: 5px;
      box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
      margin-left:10%;
      gap:1;
    }

    .left, .right {
      flex: 0.5;
    }

    .blue-header {
      background: #333;
      color: white;
      padding: 10px;
      border-radius: 5px 5px 0 0;
    }

    textarea {
      width: 75%;
      height: 80px;
      margin: 10px auto;
      display: block;
    }

    button {
      background: green;
      border-radius: 10px;
      color: white;
      padding: 10px;
      border: none;
      cursor: pointer;
      display: block;
      margin: 10px auto;
    }

    .chart-container {
      width: 700px;
      height: 500px;
      margin: 20px auto;
    }

    .search-container {
      display: flex;
      align-items: center;
      background: white;
      padding: 5px;
      width: 200px;
      height: 35px;
      border-radius: 10px;
      margin-bottom: 10px;
      box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
    }

    .search-container input {
      border: none;
      outline: none;
      width: 100%;
      padding: 5px;
      border-radius: 10px;
      font-size: 14px;
    }

    .search-container button {
      background: #007bff;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 10px;
      cursor: pointer;
      font-size: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .search-container button:hover {
      background: #0056b3;
    }

  
  </style>
  <style>
  /* Modal Styling */
  .modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Dark overlay */
    align-items: center;
    justify-content: center;
  }

  .modal-content {
    
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 300px;
    text-align: center;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
    animation: fadeIn 0.3s ease-in-out;
  }


  .close-b {
    color: red;
    font-size: 20px;
    float: right;
    cursor: pointer;
  }

  .close-b:hover {
    color: darkred;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: scale(0.9);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  .form-group {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-bottom: 15px;
}

.form-group label {
  font-weight: bold;
  margin-bottom: 5px;
}


.form-group input,
  .form-group select {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
      background: #f9f9f9;
      color: #333;
      box-sizing: border-box;
  }
  .sit-in-btn {
  background: #28a745;           /* Green background */
  border: none;                  /* No border */
  border-radius: 5px;            /* Rounded corners */
  padding: 10px 20px;            /* Padding for a comfortable click area */
  color: white;                  /* White text */
  font-size: 16px;               /* Font size */
  cursor: pointer;               /* Pointer cursor on hover */
  transition: background 0.3s ease;
  margin-top: 15px;              /* Some spacing above the button */
}

.sit-in-btn:hover {
  background: #218838;           /* Darker green on hover */
}
</style>
<style>
   .modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0); /* Black background */
    background-color: rgba(0,0,0,0.4); /* Black with opacity */
    padding-top: 60px;
  }

  .modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
  }

  .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }
  
</style>
</head>

<body>
  <div class="container">
    <div class="sidebar">
      <h2>ADMIN Dashboard</h2>
      <ul>
        <div class="search-container">
          <input type="text" id="searchInput" placeholder="Search ID..." name="search">
          <button type="button" id="searchButton"><i class="fa fa-search"></i></button>
        </div>
        <li><a href="admin.php">Home</a></li>
        <li><a href="sit_in.php">Sit-in</a></li>
        <li><a href="view_sit-in_records.php">View Sit-in Records</a></li>
        <li><a href="feedback_reports.php">Feedback Reports</a></li>
        <li><a href="students.php">Students</a></li>
        <form action="../logout.php" method="post">
            <button type="submit" name="logout" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;margin-left:29px">
              Logout
            </button>
        </form>
      </ul>
    </div>
    <?php
    $studentsRegistered = getTotalStudents();
    $students_sitIn = getTotalSitIn();
    $AlltotalSitIn = getAllTotalSitIn();

    ?>

    <div class="left">
      <div class="card">
        <div class="blue-header">Statistics</div>
        <?php echo '<p>Students Registered: ' . $studentsRegistered . '</p>'; ?>
        <?php echo '<p>Currently Sit-in: ' . $students_sitIn . '</p>'; ?>
        <?php echo '<p>Total Sit-in: ' . $AlltotalSitIn . '</p>'; ?>
        <div class="chart-container">
          <canvas id="pieChart"></canvas>
        </div>
      </div>
    </div>

    <div class="right">
      <div class="card">
        <div class="blue-header">Announcement</div>
        <form action="submit_announcement.php" method="POST">
          <textarea name="announcement" placeholder="New Announcement..."></textarea>
          <button type="submit">Submit</button>
        </form>
        <h3>Posted Announcement</h3>
        <?php include 'display_announcements.php'; ?>
        
    </div>
  </div>

  <!-- Modal -->
<!-- Modal -->
<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Student Info</h2>
    <form action = "sitin_records2.php" method = "POST">
      <div class="form-group">
        <label for="studentID"><strong>ID No:</strong></label>
        <input type="text" id="studentID" name = "student_id" readonly>
      </div>

      <div class="form-group">
        <label for="studentName"><strong>Full Name:</strong></label>
        <input type="text" name="name" id="studentName" readonly>
      </div>

      <div class="form-group">
        <label for="studentPurpose"><strong>Purpose:</strong></label>
        <select id="studentPurpose" name ="student_purpose" required>
          <option value="C# Programming">C# Programming</option>
          <option value="C Programming">C Programming</option>
          <option value="Java">Java</option>
          <option value="PHP">PHP</option>
          <option value="ASP.NET">ASP.NET</option>
        </select>
      </div>

      <div class="form-group">
        <label for="studentLab"><strong>Lab:</strong></label>
        <select id="studentLab" name ="student_lab" required>
          <option value="524">524</option>
          <option value="530">530</option>
          <option value="540">540</option>
        </select>
      </div>
      <div class="form-group">
        <label for="studentSession"><strong>Remaining Session:</strong></label>
        <input type="text" id="studentSession" name = "student_sessions" readonly>
          <button class="sit-in-btn" type="submit">Sit In</button>
      </form>
    </div>
  </div>
</div>

<!-- Edit Announcement Modal -->
<div id="editAnnouncementModal" class="modal">
    <div class="modal-content">
        <span class="close-b">&times;</span>
        
        <form id="editAnnouncementForm">
            <input type="hidden" id="announcementId" name="annc_id"> <!-- Hidden input for annc_id -->
            <label for="announcementText">Edit Announcement:</label>
            <textarea id="announcementText" name="announcement"></textarea>
            <button type="submit">Update</button>
        </form>
    </div>
</div>


<!-- Edit Announcement Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    let modal = document.getElementById("editAnnouncementModal");
    let closeButton = document.querySelector(".close-b");
    console.log()
    // Open modal when clicking on an announcement
    document.querySelectorAll('.announcement-card').forEach(item => {
        item.addEventListener("click", function () {
            let announcementId = this.getAttribute("data-id"); // Get annc_id
            let announcementText = this.querySelector(".announcement-description").innerText; // Get announcement text

            // Populate the modal form fields
            document.getElementById("announcementId").value = announcementId;
            document.getElementById("announcementText").value = announcementText;

            // Show the modal
            modal.style.display = "block";
        });
    });

    // Close modal when clicking the close button
    closeButton.addEventListener("click", function () {
        modal.style.display = "none";
        console.log("123");
    });

    // Close modal when clicking outside of it
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
            
        }
    });

    // Handle form submission
    document.getElementById("editAnnouncementForm").addEventListener("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        fetch("edit_announcement.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Announcement updated successfully!");
                modal.style.display = "none"; // Close modal after update
                location.reload(); // Refresh the page to reflect changes
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });
});



</script>

<div id="successModal" class="modal">

  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Announcement Posted Successfully!</h2>
    <p>Your announcement has been posted successfully.</p>
  </div>
</div>

<script>
  // Get the modal element
  var modal = document.getElementById('successModal');
  var closeBtn = document.getElementsByClassName('close')[0];

  // Show modal if success is set in the URL
  <?php if (isset($_GET['success']) && $_GET['success'] == 'true') { ?>
    modal.style.display = "none"; // Show modal if success=true in URL
  <?php } ?>

  // Close modal when "X" is clicked
  closeBtn.addEventListener("click", function() {
    modal.style.display = "none";
  });
  
  window.addEventListener("click", function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  });
  
</script> 



  <script>
  document.getElementById("searchButton").addEventListener("click", function () {
    var idno = document.getElementById("searchInput").value.trim();

    if (idno === "") {
        alert("Please enter an ID number.");
        return;
    }

    var formData = new FormData();
    formData.append("idno", idno);

    fetch("search_student.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text()) // Get raw response
    .then(text => {
        try {
            return JSON.parse(text); // Try parsing as JSON
        } catch (error) {
            throw new Error("Invalid JSON: " + text); // Log full response if parsing fails
        }
    })
    .then(data => {
        if (data.success) {
            document.getElementById("studentID").value = data.idno;
            document.getElementById("studentName").value = data.fullname;
            document.getElementById("studentSession").value = data.session;

            document.getElementById("myModal").style.display = "flex"; // Show modal
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred. Check console for details.");
    });
});

// Close modal when clicking on 'X'
document.querySelector(".close").addEventListener("click", function () {
  document.getElementById("myModal").style.display = "none";
});

// Close modal when clicking outside of modal-content
window.addEventListener("click", function (event) {
  var modal = document.getElementById("myModal");
  if (event.target === modal) {
      modal.style.display = "none";
  }
});

document.addEventListener("DOMContentLoaded", function () {
    fetch('get_reservation_counts.php')
        .then(response => response.json())
        .then(data => {
            // Define labels and colors
            const labels = Object.keys(data);
            const values = Object.values(data);
            const colors = ['red', 'yellow', 'pink', 'blue', 'green', 'orange']; // Adjust as needed

            // Get chart context
            var ctx = document.getElementById('pieChart').getContext('2d');
            // Create Pie Chart
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors
                    }]
                }
            });
        })
        .catch(error => console.error('Error fetching reservation counts:', error));
});

    
  
  </script>
</body>
</html>
