<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meams1"; // change to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Subject
if (isset($_POST['add_subject'])) {
    $name = $_POST['subject_name'];
    $room = $_POST['room'];

    $sql = "INSERT INTO subjects (subject_name, room) VALUES ('$name', '$room')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Subject added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Delete Subject
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM subjects WHERE subject_id=$id");
    echo "<script>alert('Subject deleted successfully!');</script>";
    echo "<script>window.location='subjects.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Subject Management</title>
<style>
body {
  font-family: Arial, sans-serif;
  padding: 30px;
  background-color: #fafafa;
}
form {
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  margin-bottom: 30px;
  width: 400px;
}
input[type=text] {
  width: 100%;
  padding: 10px;
  margin: 5px 0 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
button {
  background-color: #00bfff;
  border: none;
  color: white;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
}
button:hover {
  background-color: #0099cc;
}
table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
th, td {
  padding: 10px;
  border: 1px solid #ddd;
  text-align: left;
}
th {
  background-color: #f4f4f4;
}
.delete-btn {
  background-color: red;
  color: white;
  padding: 5px 10px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.delete-btn:hover {
  background-color: darkred;
}
</style>
</head>
<body>

<h2>Subject Management</h2>

<form method="POST" action="">
  <label>Subject Name:</label>
  <input type="text" name="subject_name" required>

  <label>Room:</label>
  <input type="text" name="room" required>

  <button type="submit" name="add_subject">Add Subject</button>
</form>

<h3>Subject List</h3>
<table>
  <tr>
    <th>ID</th>
    <th>Subject Name</th>
    <th>Room</th>
    <th>Actions</th>
  </tr>

  <?php
  $result = $conn->query("SELECT * FROM subjects");
  while ($row = $result->fetch_assoc()):
  ?>
  <tr>
    <td><?= $row['subject_id'] ?></td>
    <td><?= $row['subject_name'] ?></td>
    <td><?= $row['room'] ?></td>
    <td>
      <a href="?delete=<?= $row['subject_id'] ?>" 
         class="delete-btn" 
         onclick="return confirm('Are you sure you want to delete this subject?');">Delete</a>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

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