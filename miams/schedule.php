<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meams1";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

function fixTimeFormat($timeInput) {
    // Convert HTML input (like "12:00") into proper MySQL 24-hour time
    $time = date("H:i:s", strtotime($timeInput));

    // Special fix for 12:00 case (so 12:00 means noon, not midnight)
    if (strpos($timeInput, "12:") === 0 && date("H", strtotime($timeInput)) == "00") {
        $time = "12:" . date("i:s", strtotime($timeInput));
    }

    return $time;
}

// ✅ Assign Schedule
if (isset($_POST['assign'])) {
    $employee_id = $_POST['employee_id'];
    $subject_id = $_POST['subject_id'];
    $day_of_week = $_POST['day']; // form field uses "day"
    $start_time = fixTimeFormat($_POST['start_time']);
    $end_time = fixTimeFormat($_POST['end_time']);

    // 🟦 Check for overlapping schedules (same day and overlapping times)
    $check = $conn->query("
        SELECT * FROM schedule 
        WHERE employee_id='$employee_id' 
        AND day='$day_of_week'
        AND (
            (start_time < '$end_time' AND end_time > '$start_time')
        )
    ");

    if ($check->num_rows > 0) {
        echo "<script>alert('⚠️ Schedule conflict detected for this employee on $day_of_week!');</script>";
    } else {
        // ✅ Insert new schedule
        $sql = "INSERT INTO schedule (employee_id, subject_id, day, start_time, end_time)
                VALUES ('$employee_id', '$subject_id', '$day_of_week', '$start_time', '$end_time')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>
                alert('✅ Schedule assigned successfully!');
                window.location='schedule.php?employee_id=$employee_id';
            </script>";
        } else {
            echo "<script>alert('❌ Error: " . addslashes($conn->error) . "');</script>";
        }
    }
}

// 🗑️ Delete schedule
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $emp_id = isset($_GET['employee_id']) ? $_GET['employee_id'] : '';

    // Delete related attendance first
    $conn->query("DELETE FROM attendance WHERE schedule_id='$id'");

    // Delete schedule
    if ($conn->query("DELETE FROM schedule WHERE schedule_id='$id'")) {
        echo "<script>
            alert('🗑️ Schedule deleted successfully!');
            window.location='schedule.php?employee_id=$emp_id';
        </script>";
    } else {
        echo "<script>alert('Error deleting schedule: " . addslashes($conn->error) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Employee Subject Schedule</title>
<style>
body {
  font-family: Arial, sans-serif;
  padding: 30px;
  background-color: #fafafa;
}
select, input[type=time], input[type=text] {
  width: 100%;
  padding: 10px;
  margin: 5px 0 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
button {
  background-color: #00bfff;
  border: none;
  padding: 10px 20px;
  color: white;
  border-radius: 5px;
  cursor: pointer;
}
button:hover { background-color: #0099cc; }
.employee-card {
  background-color: #f0f8ff;
  border-radius: 10px;
  padding: 10px;
  margin-top: 20px;
}
</style>
</head>
<body>

<h2>Employee Subject Schedule</h2>

<form method="POST" action="schedule.php<?php echo isset($_GET['employee_id']) ? '?employee_id=' . $_GET['employee_id'] : ''; ?>">
  <label>Select Employee:</label>
  <select name="employee_id" onchange="window.location='schedule.php?employee_id=' + this.value" required>
    <option value="">Select employee</option>
    <?php
    $emp = $conn->query("SELECT * FROM employees");
    $selected_emp = isset($_GET['employee_id']) ? $_GET['employee_id'] : '';
    while ($e = $emp->fetch_assoc()) {
        $selected = ($selected_emp == $e['employee_id']) ? 'selected' : '';
        echo "<option value='{$e['employee_id']}' $selected>{$e['name']} ({$e['employee_id']})</option>";
    }
    ?>
  </select>

  <?php if (!empty($selected_emp)) { ?>
  <label>Subject:</label>
  <select name="subject_id" required>
    <option value="">Select subject</option>
    <?php
    $sub = $conn->query("SELECT * FROM subjects");
    while ($s = $sub->fetch_assoc()) {
        echo "<option value='{$s['subject_id']}'>{$s['subject_name']}</option>";
    }
    ?>
  </select>

  <label>Day:</label>
  <input type="text" name="day" placeholder="e.g. Monday" required>

  <label>Start Time:</label>
  <input type="time" name="start_time" required>

  <label>End Time:</label>
  <input type="time" name="end_time" required>

  <button type="submit" name="assign">Assign</button>
  <?php } ?>
</form>

<?php
if (!empty($selected_emp)) {
    $emp = $conn->query("SELECT * FROM employees WHERE employee_id = '$selected_emp'")->fetch_assoc();
    $employee_name = $emp['name'];

    echo "<h4 style='margin-top: 30px; font-weight: bold;'>{$employee_name}'s Schedule:</h4>";

    $schedule_query = $conn->query("
        SELECT 
            s.schedule_id,
            s.employee_id,
            e.name AS employee_name,
            sub.subject_name,
            s.day,
            s.start_time,
            s.end_time
        FROM schedule s
        JOIN employees e ON s.employee_id = e.employee_id
        JOIN subjects sub ON s.subject_id = sub.subject_id
        WHERE s.employee_id = '$selected_emp'
        ORDER BY s.day, s.start_time
    ");

    if ($schedule_query->num_rows > 0) {
        echo "<div class='employee-card'>";
        while ($row = $schedule_query->fetch_assoc()) {
    // Format start and end times in 12-hour format with AM/PM
    $start = date("h:i A", strtotime($row['start_time']));
    $end = date("h:i A", strtotime($row['end_time']));

    echo "
    <div style='display:flex;align-items:center;gap:10px;margin-bottom:8px;'>
        <div style='background-color:#e8f4ff;border-radius:25px;padding:6px 12px;display:flex;align-items:center;gap:10px;'>
            <span style='font-weight:500;'>{$row['subject_name']}</span>
            <span>{$row['day']} | $start - $end</span>
            <a href='schedule.php?delete={$row['schedule_id']}&employee_id=$selected_emp' 
               onclick=\"return confirm('Delete this schedule?');\" 
               style='color:red;text-decoration:none;font-size:18px;font-weight:bold;'>✖</a>
        </div>
    </div>";

        }
        echo "</div>";
    } else {
        echo "<p style='color:gray;'>No schedules assigned yet.</p>";
    }
}
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
</body>
</html>

<?php $conn->close(); ?>
