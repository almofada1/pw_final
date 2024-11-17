<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id_quarto = $_GET['id'];

    // Delete room from the database
    $stmt = $conn->prepare("DELETE FROM quartos WHERE id_quarto = ?");
    $stmt->bind_param("i", $id_quarto);

    if ($stmt->execute()) {
        header("Location: admin.php");
    } else {
        echo "Error deleting room.";
    }
}
?>
