<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';
?>
<?php include "header.php";?>

<main class="main">
    <div class="container mt-5">
        <h2>Make a Reservation</h2>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <form action="reservas.php" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="checkin_date" class="form-label">Check-in Date</label>
                <input type="date" class="form-control" id="checkin_date" name="checkin_date" required>
            </div>
            <div class="mb-3">
                <label for="checkout_date" class="form-label">Check-out Date</label>
                <input type="date" class="form-control" id="checkout_date" name="checkout_date" required>
            </div>
            <button type="submit" class="btn btn-primary">Reserve</button>
        </form>
    </div>
</main>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];

    // Get the hospede ID from the email
    $stmt = $conn->prepare("SELECT id_hospede FROM hospedes WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // No hospede found with that email
        $_SESSION['error'] = "No hospede found with that email.";
        header("Location: reservas.php");
        exit();
    }

    $row = $result->fetch_assoc();
    $hospede_id = $row['id_hospede'];

    // Find an available room
    $stmt = $conn->prepare("SELECT id_quarto FROM quartos WHERE id_quarto NOT IN (SELECT room_id FROM reservas WHERE checkin_date < ? AND checkout_date > ?)");
    $stmt->bind_param("ss", $checkout_date, $checkin_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // No available rooms
        $_SESSION['error'] = "No available rooms for the selected dates.";
        header("Location: reservas.php");
        exit();
    }

    $row = $result->fetch_assoc();
    $room_id = $row['id_quarto'];

    // Insert the new reservation
    $stmt = $conn->prepare("INSERT INTO reservas (hospede_id, room_id, checkin_date, checkout_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $hospede_id, $room_id, $checkin_date, $checkout_date);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Reservation made successfully!";
    } else {
        $_SESSION['error'] = "Failed to make the reservation.";
    }

    $stmt->close();
    $conn->close();

    header("Location: reservas.php");
    exit();
}
?>


<?php include "footer.php";?>
