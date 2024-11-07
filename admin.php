<?php include 'db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])){ 
    header("Location: login.php"); 
    exit(); 
    }
    $user_id = $_SESSION['user_id']; 
    $stmt = $conn->prepare("SELECT * FROM admin WHERE user_id = ?"); 
    $stmt->bind_param("i", $user_id); $stmt->execute(); 
    $result = $stmt->get_result(); 
    if ($result->num_rows == 0) {
        header("Location: index.php"); 
        exit();
    } 
    $stmt->close();
?>
<?php include "header.php";?>

<main class="main">
    <div class="container mt-5">
        <h2>Dashboard</h2>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="hospedes-tab" data-bs-toggle="tab" data-bs-target="#hospedes" type="button" role="tab" aria-controls="hospedes" aria-selected="true">Hospedes</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reservations-tab" data-bs-toggle="tab" data-bs-target="#reservations" type="button" role="tab" aria-controls="reservations" aria-selected="false">Reservations</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rooms-tab" data-bs-toggle="tab" data-bs-target="#rooms" type="button" role="tab" aria-controls="rooms" aria-selected="false">Rooms</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="hospedes" role="tabpanel" aria-labelledby="hospedes-tab">
                <?php include 'db.php';
                $result = $conn->query("SELECT * FROM hospedes");
                ?>
                <div class="container mt-5">
                    <h2>Records</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Endereço</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id_hospede']; ?></td>
                                    <td><?php echo $row['nome']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['telefone']; ?></td>
                                    <td><?php echo $row['endereco']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="reservations" role="tabpanel" aria-labelledby="reservations-tab">
                <?php
                $result = $conn->query("SELECT * FROM reservas");
                ?>
                <div class="container mt-5">
                    <h2>Records</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID Reserva</th>
                                <th>ID Hospede</th>
                                <th>ID Quarto</th>
                                <th>Duração</th>
                                <th>Status</th>
                                <th>Montante</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id_reserva']; ?></td>
                                    <td><?php echo $row['id_hospede']; ?></td>
                                    <td><?php echo $row['id_quarto']; ?></td>
                                    <td><?php echo $row['data_checkin']; ?> até <?php echo $row['data_checkout']; ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                    <td><?php echo $row['valor_total']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="rooms" role="tabpanel" aria-labelledby="rooms-tab">
                <?php
                $result = $conn->query("SELECT * FROM quartos");
                ?>
                <div class="container mt-5">
                    <h2>Records</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id_quarto']; ?></td>
                                    <td><?php echo $row['tipo']; ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include "footer.php";?>
