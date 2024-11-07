<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $conn = new mysqli("my-mysql", "root", "fragalha", "pw_final");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM hospedes WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id_hospede'];
            $_SESSION['email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password.";
        }
    } else {
        $_SESSION['error'] = "No user found with that email.";
    }

    $stmt->close();
    $conn->close();
}
?>
<?php include "header.php";?>
<section id="hero" class="hero section dark-background">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
</div>
<?php include "footer.php";?>
