<?php
$servername = "localhost";
$username = "root"; // change if needed
$password = "";
$dbname = "meams1";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Add Employee
if (isset($_POST['add_employee'])) {
    $employee_id = $_POST['employee_id'];
    $name = $_POST['employee_name'];
    $email = $_POST['email'];
    $department = $_POST['department'];

    $query = "INSERT INTO employees (employee_id, name, email, department)
              VALUES ('$employee_id', '$name', '$email', '$department')";
    mysqli_query($conn, $query);
}

// Delete Employee
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM schedule WHERE employee_id='$delete_id'");
    mysqli_query($conn, "DELETE FROM employees WHERE employee_id='$delete_id'");
    header('location: employees.php');
}

// Fetch employees with assigned subjects & schedules
$query = "
SELECT e.employee_id, e.name, e.email, e.department,
       GROUP_CONCAT(CONCAT(su.subject_name, ' (', sc.day, ' ', 
       DATE_FORMAT(sc.start_time, '%H:%i'), '-', DATE_FORMAT(sc.end_time, '%H:%i'),
       ', ', su.room, ')') SEPARATOR ', ') AS subjects_assigned
FROM employees e
LEFT JOIN schedule sc ON e.employee_id = sc.employee_id
LEFT JOIN subjects su ON sc.subject_id = su.subject_id
GROUP BY e.employee_id
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Instructor Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h2>Instructor Management</h2>

    <form method="post" class="mb-4">
        <label>Instructor Name:</label>
        <input type="text" name="employee_name" class="form-control" required>

        <label>Instructor ID:</label>
        <input type="text" name="employee_id" class="form-control" required>

        <label>Email:</label>
        <input type="email" name="email" class="form-control" required>

        <label>Department:</label>
        <input type="text" name="department" class="form-control" required>

        <button type="submit" name="add_employee" class="btn btn-primary mt-3">Add Employee</button>
    </form>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Instructor ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Subjects Assigned</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $row['employee_id']; ?></td>
                <td><?= $row['name']; ?></td>
                <td><?= $row['email']; ?></td>
                <td><?= $row['department']; ?></td>
                <td><?= $row['subjects_assigned'] ?: 'No subjects assigned'; ?></td>
                <td>
                    <a href="?delete=<?= $row['employee_id']; ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <!-- ======= Pagination Section ======= -->
<?php
$pages = [
    "home.php",
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
