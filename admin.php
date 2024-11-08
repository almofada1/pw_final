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
                    <!-- Button to trigger Add Hospede modal -->
                    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addHospedeModal">Add Hospede</button>

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
                                        <button class="btn btn-primary btn-sm" onclick="openEditModal(<?php echo $row['id_hospede']; ?>)">Edit</button>
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
            window.location.href = `delete_hospedes.php?id=${id}`;
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
</script>

<?php include "footer.php";?>

<!-- Add Hospede Modal -->
<div class="modal fade" id="addHospedeModal" tabindex="-1" aria-labelledby="addHospedeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addHospedeForm" action="add_hospedes.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addHospedeModalLabel">Add New Hospede</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" required>
                    </div>
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço</label>
                        <input type="text" class="form-control" id="endereco" name="endereco" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Hospede</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Hospede</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_id_hospede" name="id_hospede">
                    <div class="form-group">
                        <label for="edit_nome">Nome:</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email:</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_telefone">Telefone:</label>
                        <input type="text" class="form-control" id="edit_telefone" name="telefone" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_endereco">Endereço:</label>
                        <input type="text" class="form-control" id="edit_endereco" name="endereco" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
function openEditModal(id) {
    // Fetch existing data via AJAX
    fetch('get_hospede.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            // Populate modal fields with existing data
            document.getElementById('edit_id_hospede').value = data.id_hospede;
            document.getElementById('edit_nome').value = data.nome;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_telefone').value = data.telefone;
            document.getElementById('edit_endereco').value = data.endereco;
            
            // Show the modal
            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        })
        .catch(error => console.error('Error:', error));
}
</script>

<!-- Your HTML content, modals, and forms above -->

<!-- JavaScript code to handle the edit form submission -->
<script>
document.getElementById('editForm').addEventListener('submit', function (event) {
    event.preventDefault();

    // Prepare form data
    const formData = new FormData(this);

    // Send data via AJAX
    fetch('edit_hospedes.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        alert(result); // Show success or error message
        location.reload(); // Reload the page to reflect changes
    })
    .catch(error => console.error('Error:', error));
});
</script>

</body>
</html>
