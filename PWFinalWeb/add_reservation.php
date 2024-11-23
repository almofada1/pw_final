<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['num_pessoas'])) {
        $_SESSION['error'] = "Number of guests is required.";
        header("Location: admin.php");
        exit();
    }

    $email = $_POST['email'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $num_pessoas = $_POST['num_pessoas'];

    if (strtotime($checkout_date) <= strtotime($checkin_date)) {
        $_SESSION['error'] = "Check-out date must be after the check-in date.";
        header("Location: admin.php");
        exit();
    }

    $prices_per_night = [
        1 => 100,
        2 => 150,
        3 => 200, 
        4 => 250  
    ];

    if (!isset($prices_per_night[$num_pessoas])) {
        $_SESSION['error'] = "Invalid number of guests selected.";
        header("Location: admin.php");
        exit();
    }

    $price_per_night = $prices_per_night[$num_pessoas];

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
        $_SESSION['error'] = "Guest not found. Please register as a guest first.";
        header("Location: admin.php");
        exit();
    }

    $row = $result->fetch_assoc();
    $hospede_id = $row['id_hospede'];

    $stmt = $conn->prepare("
        SELECT id_quarto 
        FROM quartos 
        WHERE num_pessoas >= ? 
        AND id_quarto NOT IN (
            SELECT id_quarto 
            FROM reservas 
            WHERE (data_checkin < ? AND data_checkout > ?)
        )
        LIMIT 1
    ");
    $stmt->bind_param("iss", $num_pessoas, $checkout_date, $checkin_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false || $result->num_rows == 0) {
        $_SESSION['error'] = "No available rooms for the selected dates.";
        header("Location: admin.php");
        exit();
    }

    $row = $result->fetch_assoc();
    $room_id = $row['id_quarto'];

    $stmt = $conn->prepare("INSERT INTO reservas (id_hospede, id_quarto, data_checkin, data_checkout, valor_total, num_pessoas) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissii", $hospede_id, $room_id, $checkin_date, $checkout_date, $valor_total, $num_pessoas);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Reservation made successfully!";
    } else {
        $_SESSION['error'] = "Failed to make the reservation.";
    }

    $stmt->close();
    $conn->close();

    header("Location: admin.php");
    exit();
}
?>
