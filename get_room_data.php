<?php
include 'config.php';  // Include your database connection

if (isset($_GET['id_quarto'])) {
    $id_quarto = $_GET['id_quarto'];
    
    $sql = "SELECT * FROM quartos WHERE id_quarto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_quarto);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    }
}
?>
