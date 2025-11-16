<?php
$servername = "localhost";
$username = "root"; // Change if needed
$password = "";
$dbname = "meams1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Room
if (isset($_POST['add_room'])) {
    $room_number = $_POST['room_number'];
    $room_name = $_POST['room_name'];
    $capacity = $_POST['capacity'];
    $room_type = $_POST['room_type'];
    $building = $_POST['building'];
    $floor = $_POST['floor'];

    $sql = "INSERT INTO rooms (room_number, room_name, capacity, room_type, building, floor)
            VALUES ('$room_number', '$room_name', '$capacity', '$room_type', '$building', '$floor')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Room added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Delete Room
if (isset($_GET['delete'])) {
    $room_number = $_GET['delete']; // define variable first
    $room_number = $conn->real_escape_string($room_number); // prevent SQL injection

    $delete_query = "DELETE FROM rooms WHERE room_number = '$room_number'";
    if ($conn->query($delete_query)) {
        echo "<script>alert('Room deleted successfully'); window.location='room.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Room Management</title>
<style>
body { font-family: Arial, sans-serif; padding: 30px; }
input[type=text], input[type=number], select {
  width: 100%; padding: 10px; margin: 5px 0 10px;
  border: 1px solid #ccc; border-radius: 5px;
}
button { background-color: #00bfff; border: none; padding: 10px 20px; color: white; border-radius: 5px; cursor: pointer; }
button:hover { background-color: #0099cc; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
th { background-color: #f4f4f4; }
.delete-btn { background-color: red; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; }
.delete-btn:hover { background-color: darkred; }
</style>
</head>
<body>

<h2>Room Management</h2>
<form method="POST" action="">
  <label>Room Number:</label>
  <input type="text" name="room_number" required>

  <label>Room Name:</label>
  <input type="text" name="room_name" required>

  <label>Capacity:</label>
  <input type="number" name="capacity" required>

  <label>Room Type:</label>
  <select name="room_type" required>
    <option value="">Select room type</option>
    <option value="classroom">Classroom</option>
    <option value="laboratory">Laboratory</option>
    <option value="office">Office</option>
    <option value="other">Other</option>
  </select>

  <label>Building:</label>
  <input type="text" name="building" required>

  <label>Floor:</label>
  <input type="number" name="floor" required>

  <button type="submit" name="add_room">Add Room</button>
</form>

<h3>Room List</h3>
<table>
  <tr>
    <th>Room Number</th>
    <th>Room Name</th>
    <th>Type</th>
    <th>Capacity</th>
    <th>Building</th>
    <th>Floor</th>
    <th>Availability</th>
    <th>Actions</th>
  </tr>

  <?php
  $result = $conn->query("SELECT * FROM rooms");
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['room_number'] . "</td>";
    echo "<td>" . $row['room_name'] . "</td>";
    echo "<td>" . $row['room_type'] . "</td>";
    echo "<td>" . $row['capacity'] . "</td>";
    echo "<td>" . $row['building'] . "</td>";
    echo "<td>" . $row['floor'] . "</td>";
    echo "<td>" . $row['availability'] . "</td>";
    echo "<td><a href='room.php?delete=" . $row['room_number'] . "' class='delete-btn' onclick='return confirm(\"Delete this room?\");'>Delete</a></td>";
    echo "</tr>";
}
?>
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