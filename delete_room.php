<?php
include 'db.php'; // Include database connection

// Check if the room ID is passed
if (isset($_POST['id_quarto'])) {
    $id_quarto = $_POST['id_quarto'];

    // Prepare the DELETE SQL statement
    $stmt = $conn->prepare("DELETE FROM quartos WHERE id_quarto = ?");
    $stmt->bind_param("i", $id_quarto);

    // Execute the query
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>