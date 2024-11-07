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
$stmt->bind_param("i", $user_id); 
$stmt->execute(); 
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
                    <div class="form-group">
                        <label for="searchHospedes">Search:</label>
                        <input type="text" id="searchHospedes" class="form-control" placeholder="Search" onkeyup="searchRecords('hospedesTable', 'searchHospedes', ['id_hospede', 'nome', 'email', 'telefone', 'endereco'])">
                    </div>
                    <div class="form-group">
                        <label>Filter by:</label><br>
                        <input type="checkbox" id="id_hospede" checked> ID
                        <input type="checkbox" id="nome" checked> Nome
                        <input type="checkbox" id="email" checked> Email
                        <input type="checkbox" id="telefone" checked> Telefone
                        <input type="checkbox" id="endereco" checked> Endereço
                    </div>
                    <table class="table table-bordered" id="hospedesTable">
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
                                    <td> 
                                        <button class="btn btn-primary btn-sm" onclick="editRecord(<?php echo $row['id_hospede']; ?>)">Edit</button> 
                                        <button class="btn btn-danger btn-sm" onclick="deleteRecord(<?php echo $row['id_hospede']; ?>)">Delete</button> 
                                    </td>
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
                    <div class="form-group">
                        <label for="searchReservations">Search:</label>
                        <input type="text" id="searchReservations" class="form-control" placeholder="Search" onkeyup="searchRecords('reservationsTable', 'searchReservations', ['id_reserva', 'id_hospede', 'id_quarto', 'data_checkin', 'data_checkout', 'valor_total'])">
                    </div>
                    <div class="form-group">
                        <label>Filter by:</label><br>
                        <input type="checkbox" id="id_reserva" checked> ID Reserva
                        <input type="checkbox" id="id_hospede" checked> ID Hospede
                        <input type="checkbox" id="id_quarto" checked> ID Quarto
                        <input type="checkbox" id="data_checkin" checked> Check-in Date
                        <input type="checkbox" id="data_checkout" checked> Check-out Date
                        <input type="checkbox" id="valor_total" checked> Montante
                    </div>
                    <table class="table table-bordered" id="reservationsTable">
                        <thead>
                            <tr>
                                <th>ID Reserva</th>
                                <th>ID Hospede</th>
                                <th>ID Quarto</th>
                                <th>Duração</th>
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
                    <div class="form-group">
                        <label for="searchRooms">Search:</label>
                        <input type="text" id="searchRooms" class="form-control" placeholder="Search" onkeyup="searchRecords('roomsTable', 'searchRooms', ['id_quarto', 'num_pessoas', 'status'])">
                    </div>
                    <div class="form-group">
                        <label>Filter by:</label><br>
                        <input type="checkbox" id="id_quarto" checked> ID
                        <input type="checkbox" id="num_pessoas" checked> Tamanho
                        <input type="checkbox" id="status" checked> Status
                    </div>
                    <table class="table table-bordered" id="roomsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tamanho</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id_quarto']; ?></td>
                                    <td><?php echo $row['num_pessoas'] . ' ' . ($row['num_pessoas'] > 1 ? 'pessoas' : 'pessoa');?></td>
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

<script>
    function editRecord(id) {
        
    }

    function deleteRecord(id) {
        if (confirm('Are you sure you want to delete this record?')) {
            window.location.href = 'delete_hospede.php?id=' + id;
        }
    }

    function searchRecords(tableId, searchId, fields) {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById(searchId);
        filter = input.value.toUpperCase();
        table = document.getElementById(tableId);
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) {
            tr[i].style.display = "none";
            td = tr[i].getElementsByTagName("td");
            for (j = 0; j < td.length; j++) {
                if (td[j] && document.getElementById(fields[j]).checked) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        break;
                    }
                }
            }
        }
    }

    function changeRecordsPerPage() {
        var recordsPerPage = document.getElementById('recordsPerPage').value;
        window.location.href = 'admin.php?page=1&records_per_page=' + recordsPerPage + '#rooms';
    }
</script>



<?php include "footer.php";?>
