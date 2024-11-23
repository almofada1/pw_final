<?php
    include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_quarto = $_POST['id_quarto'];
    $num_pessoas = $_POST['num_pessoas'];

    $sql = "UPDATE quartos SET num_pessoas = ? WHERE id_quarto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $num_pessoas, $id_quarto);
    $stmt->execute();

    header("Location: admin.php");
}
?>