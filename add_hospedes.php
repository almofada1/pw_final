<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];

    $stmt = $conn->prepare("INSERT INTO hospedes (nome, email, telefone, endereco) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome, $email, $telefone, $endereco);

    if ($stmt->execute()) {
        header("Location: admin.php?success=Hospede added successfully");
    } else {
        header("Location: admin.php?error=Failed to add hospede");
    }

    $stmt->close();
    $conn->close();
}
?>
