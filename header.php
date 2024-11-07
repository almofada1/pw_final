<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
function isActive($page) {
    return basename($_SERVER['PHP_SELF']) == $page ? 'active' : '';
}
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $conn = new mysqli("my-mysql", "root", "fragalha", "pw_final");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT nome FROM hospedes WHERE id_hospede = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($nome);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Hotel</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">

    <!-- Custom CSS for Circle Link -->
    <style>
        .circle-link {
            border: 2px solid white;
            border-radius: 50%;
            padding: 5px 10px;
        }
        .navbar-nav {
            margin-bottom: 20px; /* Add bottom margin to navbar */
        }
        .header {
            height: auto; /* Adjust header height */
            padding: 10px 0; /* Reduce padding */
        }
        .main-content {
            margin-top: 70px; /* Add top margin to main content to avoid overlap */
        }
    </style>
</head>

<style>
    .circle-link {
        border: 2px solid white;
        border-radius: 50%;
        padding: 5px 10px;
    }
    .navbar-nav {
        margin-bottom: 20px; /* Add bottom margin to navbar */
    }
    .header {
        height: auto; /* Adjust header height */
        padding: 10px 0; /* Reduce padding */
    }
    .main-content {
        margin-top: 70px; /* Add top margin to main content to avoid overlap */
    }
    body {
        padding-top: 70px; /* Adjust this value based on the height of your header */
    }
</style>

<body class="index-page">

<header id="header" class="header d-flex align-items-center fixed-top">
	<div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

	<nav id="navmenu" class="navmenu">
		<ul>
            <li><a href="#hero" class="active">Home</a></li>
            <?php
                if (!isset($_SESSION['user_id'])) {
                    echo '<li class="nav-item"><a href="login.php" class="nav-link">Log in</a></li>';
                    echo '<li class="nav-item"><a href="register.php" class="nav-link circle-link">Register</a></li>';
                } else {
                    echo '<li class="nav-item me-2"><a href="reservas.php" class="btn btn-primary">Make a Reservation</a></li>';
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
                    echo $nome;
                    echo '</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
                    echo '<li><a class="dropdown-item" href="profile.php">Profile</a></li>';
                    echo '<li><a class="dropdown-item" href="settings.php">Settings</a></li>';
                    echo '<li><hr class="dropdown-divider"></li>';
                    echo '<li><a class="dropdown-item" href="logout.php">Logout</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }
            ?>
        </ul>
		<i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
	</nav>

	</div>
</header>
