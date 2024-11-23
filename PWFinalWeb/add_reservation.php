<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ensure 'num_pessoas' exists in the POST data
    if (!isset($_POST['num_pessoas'])) {
        $_SESSION['error'] = "Number of guests is required.";
        header("Location: reservas.php");
        exit();
    }

    $email = $_POST['email'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $num_pessoas = $_POST['num_pessoas'];

    // Validate check-in and check-out dates (Server-side validation)
    if (strtotime($checkout_date) <= strtotime($checkin_date)) {
        $_SESSION['error'] = "Check-out date must be after the check-in date.";
        header("Location: reservas.php");
        exit();
    }

    // Define room rates based on the number of guests
    $prices_per_night = [
        1 => 100, // 1 guest: $100 per night
        2 => 150, // 2 guests: $150 per night
        3 => 200, // 3 guests: $200 per night
        4 => 250  // 4 guests: $250 per night
    ];

    // Check if the number of guests has a valid price defined
    if (!isset($prices_per_night[$num_pessoas])) {
        $_SESSION['error'] = "Invalid number of guests selected.";
        header("Location: reservas.php");
        exit();
    }

    $price_per_night = $prices_per_night[$num_pessoas];

    // Calculate the number of nights
    $checkin = new DateTime($checkin_date);
    $checkout = new DateTime($checkout_date);
    $interval = $checkin->diff($checkout);
    $nights = $interval->days;

    // Calculate the total amount
    $valor_total = $price_per_night * $nights;

    // Check if the guest exists in the database
    $stmt = $conn->prepare("SELECT id_hospede FROM hospedes WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false || $result->num_rows == 0) {
        $_SESSION['error'] = "Guest not found. Please register as a guest first.";
        header("Location: reservas.php");
        exit();
    }

    // Fetch guest ID
    $row = $result->fetch_assoc();
    $hospede_id = $row['id_hospede'];

    // Find an available room based on the number of guests and no reservation conflicts
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
        header("Location: reservas.php");
        exit();
    }

    // Fetch available room ID
    $row = $result->fetch_assoc();
    $room_id = $row['id_quarto'];

    // Insert the reservation into the database, including num_pessoas
    $stmt = $conn->prepare("INSERT INTO reservas (id_hospede, id_quarto, data_checkin, data_checkout, valor_total, num_pessoas) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissii", $hospede_id, $room_id, $checkin_date, $checkout_date, $valor_total, $num_pessoas);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['success'] = "Reservation made successfully!";
    } else {
        $_SESSION['error'] = "Failed to make the reservation.";
    }

    $stmt->close();
    $conn->close();

    // After successful submission, redirect back to reservas.php
    header("Location: reservas.php");
    exit();
}
?>
