<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_hospede = $_POST['id_hospede'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE hospedes SET nome = ?, email = ?, telefone = ?, endereco = ?, is_admin = ? WHERE id_hospede = ?");
    $stmt->bind_param("ssssii", $nome, $email, $telefone, $endereco, $is_admin, $id_hospede);
    if ($stmt->execute()) {
        echo "Hospede updated successfully";
    } else {
        echo "Error updating hospede";
    }
    $stmt->close();
}
?>
