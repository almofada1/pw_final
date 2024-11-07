<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $num_guests = $_POST['num_guests'];

    $prices_per_night = [ 
        1 => 100, 
        2 => 150, 
        3 => 200, 
        4 => 250 
    ]; 

    $price_per_night = $prices_per_night[$num_guests]; 

    $checkin = new DateTime($checkin_date); 
    $checkout = new DateTime($checkout_date); 

    $interval = $checkin->diff($checkout); 
    $nights = $interval->days;
    $valor_total = $price_per_night * $nights;

    $stmt = $conn->prepare("SELECT id_hospede FROM hospedes WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false || $result->num_rows == 0) {
        $_SESSION['error'] = "Guest not found.";
        header("Location: reservas.php");
        exit();
    }

    $row = $result->fetch_assoc();
    $hospede_id = $row['id_hospede'];

    $stmt = $conn->prepare("SELECT id_quarto FROM quartos WHERE num_pessoas = ? AND id_quarto NOT IN (SELECT id_quarto FROM reservas WHERE data_checkin < ? AND data_checkout > ?)");
    $stmt->bind_param("iss", $num_guests, $checkout_date, $checkin_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false || $result->num_rows == 0) {
        $_SESSION['error'] = "No available rooms for the selected dates.";
        header("Location: reservas.php");
        exit();
    }

    $row = $result->fetch_assoc();
    $room_id = $row['id_quarto'];

    $stmt = $conn->prepare("INSERT INTO reservas (id_hospede, id_quarto, data_checkin, data_checkout, valor_total) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissi", $hospede_id, $room_id, $checkin_date, $checkout_date, $valor_total);

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
                <label for="num_guests" class="form-label">Number of People</label> 
                <input type="number" class="form-control" id="num_guests" name="num_guests" required> 
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
<?php include "footer.php";?>