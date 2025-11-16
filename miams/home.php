<?php
session_start();

include("connection.php");

if (!isset($_SESSION['username'])) {
    header("location:login.php");
    header("location:home.php");
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <!-- navbar section   -->

    <header class="navbar-section">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="">MEAMS</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="employees.php">instructor</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="room.php">room</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="subjects.php">subject</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="schedule.php">class schedule</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="attendance.php">attendance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="attendance_report.php">attendance report</a>
                        </li>
                        <li class="nav-item">
                            <div class="dropdown">
                                <a class='nav-link dropdown-toggle' href='edit.php?id=$res_id' id='dropdownMenuLink'
                                    data-bs-toggle='dropdown' aria-expanded='false'>
                                    <i class='bi bi-person'></i>
                                </a>


                                <ul class="dropdown-menu mt-2 mr-0" aria-labelledby="dropdownMenuLink">

                                    <li>
                                        <?php

                                        $id = $_SESSION['id'];
                                        $query = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");

                                        while ($result = mysqli_fetch_assoc($query)) {
                                            $res_username = $result['username'];
                                            $res_email = $result['email'];
                                            $res_id = $result['id'];
                                        }


                                        echo "<a class='dropdown-item' href='edit.php?id=$res_id'>Change Profile</a>";


                                        ?>

                                    </li>
                                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                </ul>
                            </div>

                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>


    <div class="name">
        <center>Welcome
            <?php
            // echo $_SESSION['valid'];
            
            echo $_SESSION['username'];

            ?>
            !
        </center>
    </div>

    <!-- hero section  -->

    <section id="home" class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-12 text-content">
                    <h2>The Attendance Monitoring you really want</h2>
                    <p>We develop innovative attendance monitoring systems designed to streamline tracking, reduce errors, and give you real-time insights into employee presence and performance.
                    </p>
                    <button class="btn"><a href="#">Estimate Attendance</a></button>
                </div>
                <div class="col-lg-8 col-md-12 col-sm-12">
                    <img src="images/hero-image.png" alt="" class="img-fluid">
                </div>

            </div>
        </div>
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
    </section>


    <!-- footer section  -->

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-12 col-sm-12">
                    <p class="logo"><i class="glyphicon glyphicon-calendar"></i> MEAMS</p>
                </div>
                </div>

                <div class="col-lg-1 col-md-12 col-sm-12">
                    <!-- back to top  -->

                    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
                            class="bi bi-arrow-up-short"></i></a>
                            
                </div>

            </div>

        </div>

    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
        crossorigin="anonymous"></script>
</body>

</html>