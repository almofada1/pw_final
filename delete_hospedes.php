<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id_hospede = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM hospedes WHERE id_hospede = ?");
    $stmt->bind_param("i", $id_hospede);

    if ($stmt->execute()) {
        header("Location: admin.php?success=Hospede deleted successfully");
    } else {
        header("Location: admin.php?error=Failed to delete hospede");
    }

    $stmt->close();
    $conn->close();
}
?>
