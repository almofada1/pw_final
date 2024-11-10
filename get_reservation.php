<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id_reserva = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM reservas WHERE id_reserva = ?");
    $stmt->bind_param("i", $id_reserva);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();

    echo json_encode($reservation);
}
?>
