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

// Join reservations and user_logs tables to get merged data
$reservations = [];
$sql = "SELECT r.idno, r.name, r.purpose, r.lab, u.login, u.logout, u.date 
        FROM reservations r 
        INNER JOIN user_logs u ON r.idno = u.idno
        WHERE r.status = 'Active'";

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
}

// Generate data for the Pie Chart (Lab Counts)
$labCounts = [];
foreach ($reservations as $reservation) {
    $lab = $reservation['lab'];
    if (isset($labCounts[$lab])) {
        $labCounts[$lab]++;
    } else {
        $labCounts[$lab] = 1;
    }
}

// Prepare data for the pie chart
$labels = json_encode(array_keys($labCounts));
$values = json_encode(array_values($labCounts));

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
    body { font-family: Arial, sans-serif; background-color: #f4f4f4 }
  
    .main-content { flex: 1; padding: 20px; }
    header { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
    header h1 { font-size: 24px; }
    header p { font-size: 16px; color: #555; }
    .search-bar { margin-bottom: 20px;display: flex; justify-content: space-between; }
    .search-bar input { padding: 10px; width: 250px; border: 1px solid #ccc; border-radius: 4px; }
    .reservation-table { width: 100%; margin-top: 20px; border-collapse: collapse; background: white; }
    .reservation-table th, .reservation-table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
    .reservation-table th { background-color: #333; color: white; }
    .download-buttons {margin-bottom: 20px;}
    .download-buttons button {padding: 10px 15px;margin-right: 10px;background-color: #333;color: white;border: none;cursor: pointer;border-radius: 5px; }
    .download-buttons button:hover {background-color: #0056b3;}
    .chart-container { max-width: 600px; margin: 20px auto; }

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
        <p>Sit-in Records</p>
      </header>
      <div style ="display:flex;justify-content:space-between;width:500;height:500">        
        <div class="chart-container">
            <canvas id="pieChart" ></canvas>
          </div>
        <div class="chart-container">
        <canvas id="labChart"></canvas>
        </div>
      </div>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

      <script>
        // Get data for the pie chart
        var labLabels = <?php echo $labels; ?>;
        var labValues = <?php echo $values; ?>;

        // Create the pie chart
        var ctx = document.getElementById('labChart').getContext('2d');
        var labChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labLabels, // Dynamic labels (lab names)
                datasets: [{
                    label: 'Labs Used',
                    data: labValues, // Dynamic data (lab counts)
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#ff9f40', '#ffcd56'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
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

      
      <div class="search-bar">
        <!-- <div class="download-buttons">
        <button onclick="downloadCSV()">Download CSV</button>
        <button onclick="downloadExcel()">Download Excel</button>
        <button onclick="downloadPDF()">Download PDF</button>
        <button onclick="printTable()">Print</button>
        </div> -->
        <input type="text" id="search-id" placeholder="Search by ID Number...">
      </div>
      
      <table class="reservation-table" id="reservation-table">
    <thead>
        <tr>
            <th>ID Number</th>
            <th>Name</th>
            <th>Purpose</th>
            <th>Laboratory</th>
            <th>Login</th>
            <th>Logout</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reservations as $reservation) { ?>
        <tr class="reservation-row">
            <td><?php echo htmlspecialchars($reservation['idno']); ?></td>
            <td><?php echo htmlspecialchars($reservation['name']); ?></td>
            <td><?php echo htmlspecialchars($reservation['purpose']); ?></td>
            <td><?php echo htmlspecialchars($reservation['lab']); ?></td>
            <td><?php echo htmlspecialchars($reservation['login']); ?></td>
            <td><?php echo htmlspecialchars($reservation['logout']); ?></td>
            <td><?php echo htmlspecialchars($reservation['date']); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div id="pagination" style="margin-top: 50px; margin-left: 70%; text-align: center;">
    <button id="prevPage"><i class="fas fa-chevron-left"></i></button>
    <span id="pageNumbers"></span>
    <button id="nextPage"><i class="fas fa-chevron-right"></i></button>
</div>

<style>
  #pagination {
    margin-top: 30px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
}

#pagination button {
    border: none;
    padding: 10px 15px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}

#pagination button:hover {
    background: white;
    color: black;
}

#pagination button:disabled {
    background: #ccc;
    cursor: not-allowed;
}

#pageNumbers {
    display: flex;
    gap: 5px;
}
    .page-btn {
        margin: 5px;
        padding: 5px 10px;
        border: none;
        cursor: pointer;
        background: #eee;
        border-radius: 4px;
    }
    
    .page-btn.active {
        background: #333;
        color: white;
        font-weight: bold;
    }

    button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.5/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.5/vfs_fonts.js"></script>

<script>
  
    $(document).ready(function() {
        var rowsPerPage = 10;
        var rows = $("#reservation-table tbody tr");
        var totalPages = Math.ceil(rows.length / rowsPerPage);
        var currentPage = 1;

        function showPage(page) {
            if (page < 1 || page > totalPages) return; // Prevent invalid pages

            rows.hide();
            var start = (page - 1) * rowsPerPage;
            var end = start + rowsPerPage;
            rows.slice(start, end).show();

            // Highlight current page
            $("#pageNumbers button").removeClass("active");
            $("#pageNumbers button[data-page='" + page + "']").addClass("active");

            // Disable prev/next buttons when necessary
            $("#prevPage").prop("disabled", page === 1);
            $("#nextPage").prop("disabled", page === totalPages);
        }

        function createPaginationButtons() {
            var paginationContainer = $("#pageNumbers");
            paginationContainer.empty();

            for (var i = 1; i <= totalPages; i++) {
                paginationContainer.append('<button class="page-btn" data-page="' + i + '">' + i + '</button>');
            }

            $(".page-btn").click(function() {
                currentPage = parseInt($(this).attr("data-page"));
                showPage(currentPage);
            });
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

        createPaginationButtons();
        showPage(currentPage);
    });
</script>


    </div>
  </div>
  
  <script>
    $(document).ready(function() {
        $('#search-id').on('keyup', function() {
            var value = $(this).val().toLowerCase().trim(); // Convert input to lowercase and remove spaces
            
            $("#reservation-table tr").each(function(index) {
                if (index !== 0) { // Skip the header row
                    var rowText = $(this).text().toLowerCase(); // Convert row text to lowercase
                    if (rowText.includes(value)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                }
            });
        });
    });
</script>
<script>
    function downloadCSV() {
      let csv = [];
      let rows = document.querySelectorAll("#reservation-table tr");
      rows.forEach(row => {
        let cols = row.querySelectorAll("td, th");
        let data = [];
        cols.forEach(col => data.push(col.innerText));
        csv.push(data.join(","));
      });
      let csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
      saveAs(csvFile, "table_data.csv");
    }

    function downloadExcel() {
      let table = document.getElementById("reservation-table");
      let wb = XLSX.utils.table_to_book(table, {sheet:"Sheet1"});
      XLSX.writeFile(wb, "table_data.xlsx");
    }

    function downloadPDF() {
      let docDefinition = { content: [{ table: { body: [] } }] };
      let rows = document.querySelectorAll("#reservation-table tr");
      rows.forEach(row => {
        let cols = row.querySelectorAll("td, th");
        let data = [];
        cols.forEach(col => data.push(col.innerText));
        docDefinition.content[0].table.body.push(data);
      });
      pdfMake.createPdf(docDefinition).download("table_data.pdf");
    }

    function printTable() {
      let printWindow = window.open('', '', 'width=800,height=600');
      printWindow.document.write('<html><head><title>Print Table</title></head><body>');
      printWindow.document.write(document.getElementById("reservation-table").outerHTML);
      printWindow.document.write('</body></html>');
      printWindow.document.close();
      printWindow.print();
    }
  </script>
</body>
</html>
