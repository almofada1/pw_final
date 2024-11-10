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
                // Fetch reservation records, including num_pessoas and email
                $result = $conn->query("SELECT r.*, h.email FROM reservas r 
                                        JOIN hospedes h ON r.id_hospede = h.id_hospede");
                ?>
                <div class="container mt-5">
                    <h2>Records</h2>
                    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addReservationModal">Add Reservation</button>
                    <table class="table table-bordered" id="reservationsTable">
                        <thead>
                            <tr>
                                <th>ID Reserva</th>
                                <th>Email</th>
                                <th>ID Quarto</th>
                                <th>Num Pessoas</th>
                                <th>Duração</th>
                                <th>Montante</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id_reserva']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['id_quarto']; ?></td>
                                    <td><?php echo $row['num_pessoas']; ?></td>
                                    <td><?php echo $row['data_checkin']; ?> até <?php echo $row['data_checkout']; ?></td>
                                    <td><?php echo $row['valor_total']; ?></td>
                                    <td>
                                        <button onclick="openEditReservationModal(
                                            <?= $row['id_reserva']; ?>, 
                                            '<?= addslashes($row['email']); ?>', 
                                            <?= $row['num_pessoas']; ?>, 
                                            '<?= $row['data_checkin']; ?>', 
                                            '<?= $row['data_checkout']; ?>'
                                        )" class="btn btn-primary">Edit</button>
                                        <button onclick="deleteReservation(<?= $row['id_reserva']; ?>)" class="btn btn-danger">Delete</button>
                                    </td>
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
                        <input type="text" id="searchRooms" class="form-control" placeholder="Search" onkeyup="searchRecords('roomsTable', 'searchRooms', ['id_quarto', 'num_pessoas'])">
                    </div>
                    <div class="form-group">
                        <label>Filter by:</label><br>
                        <input type="checkbox" id="id_quarto" checked> ID
                        <input type="checkbox" id="num_pessoas" checked> Tamanho
                    </div>
                    <table class="table table-bordered" id="roomsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tamanho</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id_quarto']; ?></td>
                                    <td><?php echo $row['num_pessoas'] . ' ' . ($row['num_pessoas'] > 1 ? 'pessoas' : 'pessoa');?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

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

<!-- Edit hospedes Modal -->
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

<!-- Add Reservation Modal -->
<div class="modal fade" id="addReservationModal" tabindex="-1" aria-labelledby="addReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="add_reservation.php" method="post" onsubmit="return validateDates()">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="num_pessoas" class="form-label">Number of People</label>
                <input type="number" class="form-control" id="num_pessoas" name="num_pessoas" min="1" max="4" required>
            </div>
            <div class="mb-3">
                <label for="checkin_date" class="form-label">Check-in Date</label>
                <input type="date" class="form-control" id="checkin_date" name="checkin_date" required>
            </div>
            <div class="mb-3">
                <label for="checkout_date" class="form-label">Check-out Date</label>
                <input type="date" class="form-control" id="checkout_date" name="checkout_date" required>
            </div>
            <button type="submit" class="btn btn-primary">Reserve</button>
        </form>
        </div>
    </div>
</div>

<!-- Edit Reservation Modal -->
<div class="modal fade" id="editReservationModal" tabindex="-1" aria-labelledby="editReservationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="edit_reservation.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReservationLabel">Edit Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_reserva" id="editReservationId">
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="editEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="editNumGuests" class="form-label">Number of Guests</label>
                        <input type="number" class="form-control" name="num_pessoas" id="editNumGuests" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCheckinDate" class="form-label">Check-in Date</label>
                        <input type="date" class="form-control" name="checkin_date" id="editCheckinDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCheckoutDate" class="form-label">Check-out Date</label>
                        <input type="date" class="form-control" name="checkout_date" id="editCheckoutDate" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>

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

function deleteRecord(id) {
    if (confirm('Are you sure you want to delete this record?')) {
        window.location.href = `delete_hospedes.php?id=${id}`;
    }
}

</script>

<script>
    // Function to open the edit reservation modal
    function openEditReservationModal(id, email, numGuests, checkinDate, checkoutDate) {
        // Populate the modal fields with the reservation data
        document.getElementById('editReservationId').value = id;
        document.getElementById('editEmail').value = email;
        document.getElementById('editNumGuests').value = numGuests;
        document.getElementById('editCheckinDate').value = checkinDate;
        document.getElementById('editCheckoutDate').value = checkoutDate;
        
        // Show the modal
        const editModal = new bootstrap.Modal(document.getElementById('editReservationModal'));
        editModal.show();
    }

    // Function to confirm and delete a reservation
    function deleteReservation(id) {
        if (confirm('Are you sure you want to delete this reservation?')) {
            // Redirect to delete_reservations.php with the reservation ID
            window.location.href = `delete_reservation.php?id=${id}`;
        }
    }


</script>


<?php include "footer.php";?>