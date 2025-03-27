<?php
include('../dbconnect.php');

$sql = "SELECT * FROM announcements ORDER BY date  DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

if ($result->num_rows > 0) {
    echo "<div class='announcement-container'>";
    echo "<div id='announcement-list'>"; // Wrapper for pagination
    while ($row = $result->fetch_assoc()) {
        echo '<div class="announcement-card" data-id="' . $row["annc_id"] . '">';
        echo "<p class='announcement-date'><i class='fas fa-calendar-alt'></i> " . date("F j, Y", strtotime($row['date'])) . "</p>";
        echo "<p class='announcement-name'><i class='fas fa-user'></i> " . htmlspecialchars($row['admin_name']) . "</p>";
        echo "<p class='announcement-description'><i class='fas fa-bullhorn'></i> " . nl2br(htmlspecialchars($row['description'])) . "</p>";
        echo "</div>";
    }   
    echo "</div>"; // Close announcement-list
    echo "<div class='pagination-buttons'>";
    echo "<button id='prevPage' disabled>Previous</button>";
    echo "<button id='nextPage'>Next</button>";
    echo "</div>";
    echo "</div>"; // Close announcement-container
} else {
    echo "<p class='no-announcement'>No announcements available.</p>";
}

$conn->close();
?>

<style>
.announcement-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
    padding: 15px;
}

.announcement-card {
    background: #f8f9fa;
    padding: 15px;
    border-left: 5px solid #007bff;
    border-radius: 8px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
}

.announcement-card:hover {
    transform: scale(1.02);
}

.announcement-date, .announcement-name, .announcement-description {
    font-size: 16px;
    color: #333;
    margin: 5px 0;
}

.announcement-date {
    font-weight: bold;
    color: #007bff;
}

.announcement-name {
    font-weight: bold;
    color: #28a745;
}

.announcement-description {
    font-style: italic;
}

.no-announcement {
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    color: #dc3545;
}

.pagination-buttons {
    display: flex;
    justify-content: center;
    margin-top: 10px;
}

.pagination-buttons button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 15px;
    margin: 5px;
    cursor: pointer;
    border-radius: 5px;
}

.pagination-buttons button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    var rowsPerPage = 5;
    var rows = $(".announcement-card");
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
</script>
