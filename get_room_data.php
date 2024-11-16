<?php
include 'config.php';  // Include your database connection

if (isset($_GET['id_quarto'])) {
    $id_quarto = $_GET['id_quarto'];
    
    // Prepare SQL query to fetch room data
    $sql = "SELECT * FROM quartos WHERE id_quarto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_quarto);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Return the data as JSON
        echo json_encode($row);
    }
}
?>
