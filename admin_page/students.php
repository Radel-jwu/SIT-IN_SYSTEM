<?php
session_start();
include('../dbconnect.php');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(["success" => false, "message" => "Unauthorized access. Please log in."]);
  echo "<script> window.location.href = '../login.html'; </script>";
  exit;
}

function getStudentRecords() {
  global $conn;
  $sql = "SELECT studentinfo.idno, studentinfo.year_lvl, studentinfo.course, studentinfo.lastname, reservations.remaining_sessions 
  FROM studentinfo 
  LEFT JOIN reservations ON studentinfo.idno = reservations.idno 
  WHERE studentinfo.idno != 1;";
  $result = $conn->query($sql);
  $students = [];
  
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          $students[] = $row;
      }
  }
  return $students;
}

$students = getStudentRecords();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../css/styles.css">

  <style>
    body {
      font-family: Arial, sans-serif;
      display: flex;
      margin: 0;
      background: #f8f9fa;
    }

    .main-content {
      flex: 1;
      padding: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: white;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }

    th {
      background: #343a40;
      color: white;
    }

    .action-btn {
      padding: 5px 10px;
      border-radius: 4px;
      cursor: pointer;
      margin-right: 5px;
    }

    .edit-btn {
      background: #ffc107;
      color: black;
    }

    .delete-btn {
      background: #dc3545;
      color: white;
    }

    .edit-btn:hover {
      background: #e0a800;
    }

    .delete-btn:hover {
      background: #c82333;
    }

    .pagination {
      margin-top: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
    }

    .pagination button {
      background: #007bff;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .pagination button:hover {
      background: #0056b3;
    }

    .pagination button:disabled {
      background: #ccc;
      cursor: not-allowed;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      padding-top: 100px;
    }

    .modal-content {
      background: white;
      margin: auto;
      padding: 20px;
      width: 40%;
      border-radius: 5px;
    }

    .close {
      color: red;
      float: right;
      font-size: 28px;
      cursor: pointer;
    }

    .close:hover {
      color: darkred;
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="sidebar">
      <h2>ADMIN Dashboard</h2>
      <ul>
       
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
    <div class="main-content">
      <h2>Student Records</h2>

      <table id="student-table">
    <thead>
        <tr>
            <th>ID No</th>
            <th>Name</th>
            <th>Year Level</th>
            <th>Course</th>
            <th>Remaining Sessions</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['idno']) ?></td>
                <td><?= htmlspecialchars($student['lastname']) ?></td>
                <td><?= htmlspecialchars($student['year_lvl']) ?></td>
                <td><?= htmlspecialchars($student['course']) ?></td>
                <td><?= htmlspecialchars($student['remaining_sessions'] ?? 'N/A') ?></td>
                <td>
                    <button class="action-btn edit-btn" onclick="editStudent('<?= $student['idno'] ?>', '<?= $student['lastname'] ?>', '<?= $student['year_lvl'] ?>', '<?= $student['course'] ?>', '<?= $student['remaining_sessions'] ?>')">Edit</button>
                    <button class="action-btn delete-btn" onclick="deleteStudent('<?= $student['idno'] ?>')">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

      <div class="pagination">
        <button id="prevPage"><i class="fas fa-chevron-left"></i></button>
        <span id="pageNumbers"></span>
        <button id="nextPage"><i class="fas fa-chevron-right"></i></button>
      </div>
    </div>
  </div>

  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Edit Student</h2>
      <form id="editForm">
        <input type="hidden" id="editIdno">
        <label>Name:</label>
        <input type="text" id="editName">
        <label>Year Level:</label>
        <input type="text" id="editYear">
        <label>Course:</label>
        <input type="text" id="editCourse">
        <label>Remaining Sessions:</label>
        <input type="number" id="editSessions">
        <button type="submit">Save Changes</button>
      </form>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      var rowsPerPage = 3;
      var rows = $("#student-table tbody tr");
      var totalPages = Math.ceil(rows.length / rowsPerPage);
      var currentPage = 1;

      function showPage(page) {
        rows.hide();
        rows.slice((page - 1) * rowsPerPage, page * rowsPerPage).show();
        $("#prevPage").prop("disabled", page === 1);
        $("#nextPage").prop("disabled", page === totalPages);
      }

      $("#nextPage").click(function() {
        if (currentPage < totalPages) {
          currentPage++;
          showPage(currentPage);
        }
      });

      $("#prevPage").click(function() {
        if (currentPage > 1) {
          currentPage--;
          showPage(currentPage);
        }
      });

      showPage(currentPage);
    });

    function editStudent(idno, name, year, course, sessions) {
      $("#editIdno").val(idno);
      $("#editName").val(name);
      $("#editYear").val(year);
      $("#editCourse").val(course);
      $("#editSessions").val(sessions);
      $("#editModal").fadeIn();
    }

    function closeModal() {
      $("#editModal").fadeOut();
    }

  
  </script>
  <script>
  $(document).ready(function () {
    $("#editForm").submit(function (event) {
      event.preventDefault();

      let idno = $("#editIdno").val();
      let name = $("#editName").val();
      let year_lvl = $("#editYear").val();
      let course = $("#editCourse").val();
      let remaining_sessions = $("#editSessions").val();

      $.ajax({
        url: "update_student.php",
        type: "POST",
        data: {
          idno: idno,
          name: name,
          year_lvl: year_lvl,
          course: course,
          remaining_sessions: remaining_sessions
        },
        dataType: "json",
        success: function (response) {
          alert(response.message);
          if (response.success) {
            location.reload(); // Refresh the page after successful update
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error: " + status + " - " + error);
          alert("Failed to update student. Check console for details.");
        }
      });
    });
  });
</script>

   
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  function deleteStudent(idno) {
    if (confirm("Are you sure you want to delete this student?")) {
        $.ajax({
            url: "delete_student.php",
            type: "POST",
            data: { idno: idno },
            dataType: "json",
            success: function(response) {
                alert(response.message);
                if (response.success) {
                    location.reload(); // Refresh the page after successful deletion
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + " - " + error);
                alert("Failed to delete student. Check console for details.");
            }
        });
    }
}

</script>
   
</body>
</html>
