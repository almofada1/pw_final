<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id_quarto = $_GET['id'];

    $stmt = $conn->prepare("SELECT id_quarto FROM quartos WHERE num_pessoas = (SELECT num_pessoas FROM quartos WHERE id_quarto = ?) AND id_quarto != ? AND id_quarto NOT IN (SELECT id_quarto FROM reservas WHERE (data_checkin BETWEEN (SELECT data_checkin FROM reservas WHERE id_quarto = ?) AND (SELECT data_checkout FROM reservas WHERE id_quarto = ?)) OR (data_checkout BETWEEN (SELECT data_checkin FROM reservas WHERE id_quarto = ?) AND (SELECT data_checkout FROM reservas WHERE id_quarto = ?))) LIMIT 1");
    $stmt->bind_param("iiiiii", $id_quarto, $id_quarto, $id_quarto, $id_quarto, $id_quarto, $id_quarto);
    $stmt->execute();
    $stmt->bind_result($new_id_quarto);
    $stmt->fetch();
    $stmt->close();

    if ($new_id_quarto) {
        $stmt = $conn->prepare("UPDATE reservas SET id_quarto = ? WHERE id_quarto = ?");
        $stmt->bind_param("ii", $new_id_quarto, $id_quarto);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM quartos WHERE id_quarto = ?");
        $stmt->bind_param("i", $id_quarto);

        if ($stmt->execute()) {
            header("Location: admin.php?success=Room deleted and reservations reassigned successfully");
        } else {
            echo "Error deleting room.";
        }
        $stmt->close();
    } else {
        echo "No available room found to reassign reservations.";
    }

    $conn->close();
}
?>
