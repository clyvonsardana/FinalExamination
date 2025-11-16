<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meams"; // change if needed

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$type = $_GET['type'];

if ($type === 'employees') {
    $result = $conn->query("SELECT id, name FROM employees");
} elseif ($type === 'subjects') {
    $result = $conn->query("SELECT id, subject_name FROM subjects");
} elseif ($type === 'rooms') {
    $result = $conn->query("SELECT id, room_name FROM rooms");
} elseif ($type === 'schedules') {
    $employee_id = $_GET['employee_id'];
    $result = $conn->query("
        SELECT s.id, sub.subject_name, r.room_name, s.start_time, s.end_time 
        FROM schedules s
        JOIN subjects sub ON s.subject_id = sub.id
        JOIN rooms r ON s.room_id = r.id
        WHERE s.employee_id = $employee_id
    ");
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>