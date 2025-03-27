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

$sql = "SELECT * FROM studentinfo WHERE idno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$studentData = $result->num_rows > 0 ? $result->fetch_assoc() : null;
$stmt->close();

// Fetch active reservations
$reservations = [];
$sql = "SELECT * FROM reservations WHERE status = 'Active'";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
   
    .main-content { flex: 1; padding: 20px; }
    header { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
    header h1 { font-size: 24px; }
    header p { font-size: 16px; color: #555; }
    .search-bar { margin-bottom: 20px;display: flex; justify-content: space-between; }
    .search-bar input { padding: 10px; width: 250px; border: 1px solid #ccc; border-radius: 4px; }
    .reservation-table { width: 100%; margin-top: 20px; border-collapse: collapse; background: white; }
    .reservation-table th, .reservation-table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
    .reservation-table th { background-color: #333; color: white; }
  </style>
  <style>
    .logout-btn {
    background-color: #d9534f; /* Bootstrap 'danger' color */
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s ease-in-out;
      }

      .logout-btn:hover {
          background-color: #c9302c; /* Slightly darker red on hover */
      }
  </style>
</head>
<body>
  <div class="container">
  <div class="sidebar">
      <h2>ADMIN Dashboard</h2>
      <ul>
        <li><a href="admin.php#">Home</a></li>
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
    <div class="main-content">
      <header>
        <h1>Welcome, <?php echo htmlspecialchars($studentData['username']); ?></h1>
        <p>Your Dashboard</p>
      </header>
      
      <div class="search-bar">
      <h2>Current Sit-in</h2>

        <input type="text" id="search-id" placeholder="Search by ID Number...">
      </div>
      
      <table class="reservation-table" id="reservation-table">
        <tr>
          <th>Sit-in ID Number</th>
          <th>ID Number</th>
          <th>Name</th>
          <th>Remaining Sessions</th>
          <th>Purpose</th>
          <th>Lab</th>
          <th>Status</th>
          <th>Action</th>

        </tr>
        <?php foreach ($reservations as $res) : ?>
        <tr class="reservation-row">
          <td><?php echo htmlspecialchars($res['sit-in_id-number']); ?></td>
          <td><?php echo htmlspecialchars($res['idno']); ?></td>
          <td><?php echo htmlspecialchars($res['name']); ?></td>
          <td><?php echo htmlspecialchars($res['remaining_sessions']); ?></td>
          <td><?php echo htmlspecialchars($res['purpose']); ?></td>
          <td><?php echo htmlspecialchars($res['lab']); ?></td>
          <td><?php echo htmlspecialchars($res['status']); ?></td>
          <td>
            <button class="logout-btn" data-id="<?php echo $res['idno']; ?>">Logout</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
  
  <script>
      $(document).ready(function() {
        // Search filter
        $('#search-id').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.reservation-row').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Logout button function (Delete reservation)
        $('.logout-btn').on('click', function() {
            let idno = $(this).data('id');

            if (confirm("Are you sure you want to log out this user?")) {
                $.ajax({
                    url: "delete_reservation.php",
                    type: "POST",
                    data: { idno: idno },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function() {
                        alert("Something went wrong. Please try again.");
                    }
                });
            }
        });
    });
  </script>
</body>
</html>
