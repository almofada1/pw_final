<?php
session_start();
ob_start();
include "header.php";
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = htmlspecialchars($_POST['nome']);
    $email = htmlspecialchars($_POST['email']);
    $telefone = htmlspecialchars($_POST['telefone']);
    $endereco = htmlspecialchars($_POST['endereco']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn = new mysqli("my-mysql", "root", "fragalha", "pw_final");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id_hospede, password FROM hospedes WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If user already exists but doesn't have a password, update the password
        $stmt->bind_result($id_hospede, $existing_password);
        $stmt->fetch();

        if (empty($existing_password)) {
            $stmt->close();
            $stmt = $conn->prepare("UPDATE hospedes SET nome=?, telefone=?, endereco=?, password=? WHERE id_hospede=?");
            $stmt->bind_param("ssssi", $nome, $telefone, $endereco, $password, $id_hospede);
            $stmt->execute();
        }

        $_SESSION['user_id'] = $id_hospede;
        $_SESSION['user_name'] = $nome;
        header("Location: index.php");
        exit();
    } else {
        $stmt->close();

        // Insert the new user
        $stmt = $conn->prepare("INSERT INTO hospedes (nome, email, telefone, endereco, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $email, $telefone, $endereco, $password);

        if ($stmt->execute()) {
            // Log in the user automatically after registration
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['user_name'] = $nome;
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $conn->close();
}

if (ob_get_length()) {
    ob_end_flush();
}
?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Registration successful!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <form method="post" action="register.php">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone:</label>
                    <input type="text" class="form-control" id="telefone" name="telefone" required>
                </div>
                <div class="form-group">
                    <label for="endereco">Endereço:</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
        </div>
    </div>
</div>

</main>

<?php include "footer.php"; ?>
