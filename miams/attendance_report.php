<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meams1";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// =======================================================
// GET SEARCH INPUTS (Search, NOT dropdowns anymore!)
// =======================================================
$employee_search = $_GET['employee'] ?? '';
$subject_search  = $_GET['subject'] ?? '';
$room_search     = $_GET['room'] ?? '';
$start_date      = $_GET['start_date'] ?? '';
$end_date        = $_GET['end_date'] ?? '';

// =======================================================
// SQL QUERY (FIXED version)
// Works even if room is NULL
// Search using LIKE
// =======================================================
$sql = "
    SELECT 
        a.attendance_id,
        e.name AS employee_name,
        s.subject_name,
        COALESCE(r.room_name, 'No Room Assigned') AS room_name,
        a.date,
        TIME_FORMAT(sch.start_time, '%H:%i') AS scheduled_start,
        TIME_FORMAT(sch.end_time, '%H:%i') AS scheduled_end,
        TIME_FORMAT(a.time_marked, '%H:%i') AS time_marked,
        a.status
    FROM attendance a
    LEFT JOIN employees e ON a.employee_id = e.employee_id
    LEFT JOIN schedule sch ON a.schedule_id = sch.schedule_id
    LEFT JOIN subjects s ON sch.subject_id = s.subject_id
    LEFT JOIN rooms r ON sch.room_id = r.room_id
    WHERE 1=1
";

// ================= SEARCH FILTERS =====================
if (!empty($employee_search)) {
    $emp = $conn->real_escape_string($employee_search);
    $sql .= " AND e.name LIKE '%$emp%'";
}
if (!empty($subject_search)) {
    $sub = $conn->real_escape_string($subject_search);
    $sql .= " AND s.subject_name LIKE '%$sub%'";
}
if (!empty($room_search)) {
    $room = $conn->real_escape_string($room_search);
    $sql .= " AND (r.room_name LIKE '%$room%' OR r.room_name IS NULL)";
}

if (!empty($start_date)) { $sql .= " AND a.date >= '$start_date'"; }
if (!empty($end_date))   { $sql .= " AND a.date <= '$end_date'"; }

$sql .= " ORDER BY a.date DESC";

$result = $conn->query($sql);
if (!$result) { die("Query failed: " . $conn->error); }

// =======================================================
// SUMMARY COUNTERS
// =======================================================
$rows = [];
$total = $present = $late = $absent = 0;

while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
    $total++;

    if ($row['status'] == 'PRESENT') $present++;
    elseif ($row['status'] == 'LATE') $late++;
    elseif ($row['status'] == 'ABSENT') $absent++;
}

$attendance_rate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Attendance Reports</title>

<style>
body { font-family: Arial, sans-serif; background: #f8f9fb; margin: 30px; }
h2 { margin-bottom: 20px; }
input[type="text"], input[type="date"] {
    padding: 6px; border-radius: 5px; border: 1px solid #ccc; width: 180px; margin-right: 5px;
}
button { padding: 8px 15px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
.btn-csv { background: #28a745; color: white; }
.btn-json { background: #ffc107; color: black; }
.btn-print { background: #6c757d; color: white; }
.card { display: inline-block; width: 160px; background: white; border-radius: 10px; padding: 10px; margin: 10px; text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
.card h3 { margin: 5px 0; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; border-radius: 10px; overflow: hidden; }
th, td { border: 1px solid #ddddddff; padding: 8px; text-align: center; vertical-align: middle;}
th { background: #007bff; color: white; }
.status-present { color: #28a745; font-weight: bold; }
.status-late { color: #ffc107; font-weight: bold; }
.status-absent { color: #dc3545; font-weight: bold; }
</style>
</head>

<body>

<h2>Attendance Reports</h2>

<!-- ========================= SEARCH INPUTS ========================= -->
<form method="GET" action="attendance_report.php" class="filters">

<input type="text" name="employee" placeholder="Search employee..."
       value="<?= htmlspecialchars($employee_search) ?>">

<input type="text" name="subject" placeholder="Search subject..."
       value="<?= htmlspecialchars($subject_search) ?>">

<input type="text" name="room" placeholder="Search room..."
       value="<?= htmlspecialchars($room_search) ?>">

<input type="date" name="start_date" value="<?= $start_date ?>">
<input type="date" name="end_date" value="<?= $end_date ?>">

<button type="submit" name="generate_report">Generate Report</button>
<button type="button" onclick="exportCSV()" class="btn-csv">Export CSV</button>
<button type="button" onclick="exportJSON()" class="btn-json">Export JSON</button>
<button type="button" onclick="window.print()" class="btn-print">Print</button>

</form>

<!-- ========================= SUMMARY CARDS ========================= -->
<div>
  <div class="card"><h3><?= $total ?></h3><p>Total</p></div>
  <div class="card"><h3><?= $present ?></h3><p>Present</p></div>
  <div class="card"><h3><?= $late ?></h3><p>Late</p></div>
  <div class="card"><h3><?= $absent ?></h3><p>Absent</p></div>
  <div class="card"><h3><?= $attendance_rate ?>%</h3><p>Attendance Rate</p></div>
</div>

<!-- ========================= TABLE OUTPUT ========================= -->
<?php if (!empty($rows)): ?>
<table>
    <thead>
        <tr>
            <th>Employee</th>
            <th>Subject</th>
            <th>Room</th>
            <th>Date</th>
            <th>Scheduled Time</th>
            <th>Actual Time</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['employee_name']) ?></td>
            <td><?= htmlspecialchars($r['subject_name']) ?></td>
            <td><?= htmlspecialchars($r['room_name']) ?></td>
            <td><?= htmlspecialchars($r['date']) ?></td>
            <td><?= $r['scheduled_start'] ?> - <?= $r['scheduled_end'] ?></td>
            <td><?= htmlspecialchars($r['time_marked']) ?></td>
            <td class="status-<?= strtolower($r['status']) ?>"><?= $r['status'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p><strong>No records found.</strong></p>
<?php endif; ?>

<script>
function exportCSV() {
  const csv = [];
  const rows = document.querySelectorAll("table tr");
  rows.forEach(tr => {
      let cols = tr.querySelectorAll("th,td");
      let row = [];
      cols.forEach(td => row.push(td.innerText));
      csv.push(row.join(","));
  });

  let blob = new Blob([csv.join("\n")], { type: "text/csv" });
  let link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = "attendance_report.csv";
  link.click();
}

function exportJSON() {
  const data = [];
  const headers = Array.from(document.querySelectorAll("table th")).map(th => th.innerText);
  const rows = document.querySelectorAll("table tbody tr");

  rows.forEach(tr => {
    const record = {};
    const cells = tr.querySelectorAll("td");
    cells.forEach((td, index) => record[headers[index]] = td.innerText);
    data.push(record);
  });

  let blob = new Blob([JSON.stringify(data, null, 2)], { type: "application/json" });
  let link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = "attendance_report.json";
  link.click();
}
</script>

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
