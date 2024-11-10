<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id_reserva = $_GET['id'];

    // Delete reservation from the database
    $stmt = $conn->prepare("DELETE FROM reservas WHERE id_reserva = ?");
    $stmt->bind_param("i", $id_reserva);

    if ($stmt->execute()) {
        header("Location: admin.php?tab=reservations");
    } else {
        echo "Error deleting reservation.";
    }
}
?>
