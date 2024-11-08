<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id_hospede']);
    $nome = htmlspecialchars($_POST['nome']);
    $email = htmlspecialchars($_POST['email']);
    $telefone = htmlspecialchars($_POST['telefone']);
    $endereco = htmlspecialchars($_POST['endereco']);

    $stmt = $conn->prepare("UPDATE hospedes SET nome = ?, email = ?, telefone = ?, endereco = ? WHERE id_hospede = ?");
    $stmt->bind_param("ssssi", $nome, $email, $telefone, $endereco, $id);

    if ($stmt->execute()) {
        echo "Hospede updated successfully!";
    } else {
        echo "Failed to update hospede!";
    }
    $stmt->close();
}
$conn->close();
?>
