<?php
date_default_timezone_set('Asia/Manila');

$servername = "localhost";
$username = "root"; // Change if needed
$password = "";
$dbname = "meams1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);



$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');


// Handle attendance update
if (isset($_POST['mark_attendance'])) {
    $employee_id = $_POST['employee_id'];
    $schedule_id = $_POST['schedule_id'];
    $status = $_POST['status'];
    $time = date("H:i:s");

    // Check if already marked
    $check = $conn->query("SELECT * FROM attendance WHERE date='$date' AND schedule_id='$schedule_id'");
    if ($check->num_rows > 0) {
    $conn->query("UPDATE attendance SET status='$status', time_marked='$time' WHERE date='$date' AND schedule_id='$schedule_id'");
   } else {
    $conn->query("INSERT INTO attendance (employee_id, schedule_id, date, status, time_marked) 
              VALUES ('$employee_id', '$schedule_id', '$date', '$status', '$time')");
    }
    header("Location: attendance.php?date=$date");
    exit;
}

// Fetch employee schedules
$employees = $conn->query("
    SELECT 
        s.schedule_id, 
        s.employee_id,             
        e.name AS employee_name, 
        sub.subject_name, 
        r.room_name, 
        s.day, 
        s.start_time, 
        s.end_time
    FROM schedule s
    JOIN employees e ON s.employee_id = e.employee_id
    JOIN subjects sub ON s.subject_id = sub.subject_id
    JOIN rooms r ON sub.room = r.room_name
    ORDER BY e.employee_id, s.start_time
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; margin: 20px; }
        .employee-card { background: white; border-radius: 10px; padding: 20px; margin: 10px; display: inline-block; vertical-align: top; width: 30%; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .subject { margin-top: 10px; padding: 10px; border-top: 1px solid #ddd; }
        .btn { border: none; padding: 30px 35px; margin-right: 5px; cursor: pointer; border-radius: 10px; color: white; }
        .present { background-color: #28a745; }
        .late { background-color: #ffc107; color: black; }
        .absent { background-color: #dc3545; }
        .button1 {background-color: #04AA6D;  padding: 10px 30px; font-size: 14px;}
        
    </style>
</head>
<body>
<h2>Mark Attendance</h2>

<form method="get">
    <label>Date:</label>
    <input type="date" style="width: 250px; font-size: 23px;" name="date" value="<?= $date ?>">
    <button type="submit" class="button button1">Go</button>
</form>

<br>

<?php
$current_emp = "";
if ($employees->num_rows > 0) {
    while ($row = $employees->fetch_assoc()) {
        if ($current_emp != $row['employee_id']) {
            if ($current_emp != "") echo "</div>"; // close previous card
            echo "<div class='employee-card'>";
            echo "<h3>{$row['employee_name']} ({$row['employee_id']})</h3>";
            $current_emp = $row['employee_id'];
        }

        // Check attendance status
        $schedule_id = $row['schedule_id'];
        $att = $conn->query("SELECT * FROM attendance WHERE schedule_id='$schedule_id' AND date='$date'");
        $status_text = "Not marked";
        $time_text = "";

        if ($att && $att->num_rows > 0) {
            $att_data = $att->fetch_assoc();
            $status_text = $att_data['status'];

            if ($att_data['status'] === 'PRESENT' || $att_data['status'] === 'LATE') {
                $time_text = date("h:i A", strtotime($att_data['time_marked']));
            }
        }

        echo "<div class='subject'>
    <b>{$row['subject_name']}</b><br>
    {$row['start_time']} - {$row['end_time']}<br>
    Room: {$row['room_name']}<br>

    <form method='post' style='margin-top:8px;'>
        <input type='hidden' name='employee_id' value='{$row['employee_id']}'>
        <input type='hidden' name='schedule_id' value='{$row['schedule_id']}'>
        <input type='hidden' name='date' value='$date'>
        <input type='hidden' name='status' value=''>
        
        <button 
            class='btn present' 
            type='submit'
            name='mark_attendance' 
            value='1'
            data-employee='{$row['employee_name']}'
            data-status='P'
            onclick=\"if(confirmAttendanceDynamic(this)){ this.form.status.value='PRESENT'; } else { return false; }\"
        >P</button>

        <button 
            class='btn late' 
            type='submit'
            name='mark_attendance' 
            value='1'
            data-employee='{$row['employee_name']}'
            data-status='L'
            onclick=\"if(confirmAttendanceDynamic(this)){ this.form.status.value='LATE'; } else { return false; }\"
        >L</button>

        <button 
            class='btn absent' 
            type='submit'
            name='mark_attendance' 
            value='1'
            data-employee='{$row['employee_name']}'
            data-status='A'
            onclick=\"if(confirmAttendanceDynamic(this)){ this.form.status.value='ABSENT'; } else { return false; }\"
        >A</button>
    </form>

    <small>Status: <b>$status_text</b> $time_text</small>
</div>";
    }
    echo "</div>"; // close last employee card
} else {
    echo "<p>No schedule data found.</p>";
}


?>
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- ======= Pagination Section ======= -->
<?php
$pages = [
    "employees.php",
    "room.php",
    "subjects.php",
    "schedule.php",
    "attendance.php",
    "attendance_report.php"
];

$current_page = basename($_SERVER['PHP_SELF']);
$current_index = array_search($current_page, $pages);
?>

<nav aria-label="Page navigation example">
  <ul class="pagination" style="display: flex; justify-content: center; list-style: none; padding: 0;">
    <!-- Previous Button -->
    <li class="page-item" style="margin: 0 5px; <?= $current_index <= 0 ? 'pointer-events:none; opacity:0.5;' : '' ?>">
      <a class="page-link" href="<?= $current_index > 0 ? $pages[$current_index - 1] : '#' ?>">Previous</a>
    </li>

    <!-- Numbered Page Links -->
    <?php foreach ($pages as $index => $page): ?>
      <li class="page-item <?= $page == $current_page ? 'active' : '' ?>" style="margin: 0 3px;">
        <a class="page-link" href="<?= $page ?>" style="<?= $page == $current_page ? 'background:#007bff; color:white; border-radius:5px; padding:5px 10px;' : 'padding:5px 10px; text-decoration:none; border:1px solid #ccc; border-radius:5px;' ?>">
          <?= $index + 1 ?>
        </a>
      </li>
    <?php endforeach; ?>

    <!-- Next Button -->
    <li class="page-item" style="margin: 0 5px; <?= $current_index >= count($pages) - 1 ? 'pointer-events:none; opacity:0.5;' : '' ?>">
      <a class="page-link" href="<?= $current_index < count($pages) - 1 ? $pages[$current_index + 1] : '#' ?>">Next</a>
    </li>
  </ul>
</nav>
<!-- ======= End Pagination Section ======= -->
<script>
function confirmAttendanceDynamic(button) {
    const employeeName = button.getAttribute("data-employee");
    const status = button.getAttribute("data-status");

    const statusText =
        status === "P" ? "Present" :
        status === "L" ? "Late" :
        "Absent";

    return confirm(`Are you sure you want to mark ${employeeName} as ${statusText}?`);
}
</script>
</script>
</body>
</html>
