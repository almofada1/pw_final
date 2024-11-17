<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    // Get the reservation ID from POST data
    $id_reserva = $_POST['id_reserva'];

    // Get the updated reservation data from the form
    $email = $_POST['email'];  // New email input by the user
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $num_pessoas = $_POST['num_pessoas'];

    // Validate dates, number of guests, etc.
    if (strtotime($checkout_date) <= strtotime($checkin_date)) {
        $_SESSION['error'] = "Check-out date must be after the check-in date.";
        header("Location: edit_reservation.php?id_reserva=$id_reserva");
        exit();
    }

    // Find the guest by email
    $stmt = $conn->prepare("SELECT id_hospede FROM hospedes WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $_SESSION['error'] = "Guest not found. Please register as a guest first.";
        header("Location: edit_reservation.php?id_reserva=$id_reserva");
        exit();
    }

    // Fetch guest ID
    $row = $result->fetch_assoc();
    $hospede_id = $row['id_hospede'];

    // Find an available room based on the number of guests and no reservation conflicts
    $stmt_room = $conn->prepare("
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
    $stmt_room->bind_param("iss", $num_pessoas, $checkout_date, $checkin_date);
    $stmt_room->execute();
    $room = $stmt_room->get_result()->fetch_assoc();

    if ($room) {
        // Get the available room ID
        $id_quarto = $room['id_quarto'];

        // Update reservation details
        $stmt = $conn->prepare("UPDATE reservas SET id_hospede = ?, data_checkin = ?, data_checkout = ?, num_pessoas = ?, id_quarto = ? WHERE id_reserva = ?");
        $stmt->bind_param("issiii", $hospede_id, $checkin_date, $checkout_date, $num_pessoas, $id_quarto, $id_reserva);

        if ($stmt->execute()) {
            $stmt_email = $conn->prepare("UPDATE hospedes SET email = ? WHERE id_hospede = ?");
            $stmt_email->bind_param("si", $email, $hospede_id);
            $stmt_email->execute();

            $_SESSION['success'] = "Reservation updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update the reservation.";
        }

        $stmt->close();
        $stmt_room->close();
    } else {
        $_SESSION['error'] = "No available room found for the new number of guests.";
    }

    $conn->close();


    // Redirect back to the previous page (referer)
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();

}
?>
