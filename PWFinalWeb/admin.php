<?php
// Include the database connection file
include 'db.php';

// Start the session if it hasn't been started already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in by verifying the existence of 'user_id' in the session
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Check if the database connection ($conn) is successful
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Prepare a statement to fetch the 'is_admin' status of the logged-in user
$stmt = $conn->prepare("SELECT is_admin FROM hospedes WHERE id_hospede = ?");
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

// Bind the user ID parameter to the prepared statement
$stmt->bind_param("i", $user_id);

// Execute the prepared statement
$stmt->execute();

// Get the result from the statement execution
$result = $stmt->get_result();

// Check if the user exists in the database
if ($result->num_rows === 0) {
    // If no matching user is found, redirect to the homepage (or an error page)
    header("Location: index.php");
    exit();
}

// Fetch the result as an associative array
$row = $result->fetch_assoc();
$isAdmin = $row['is_admin'] ?? 0; // Use null coalescing to ensure $isAdmin is defined

// Free the result set
$result->free();

// Close the statement
$stmt->close();

// Close the database connection
$conn->close();

// If the user is not an admin, redirect them to the homepage
if ($isAdmin != 1) {
    header("Location: index.php");
    exit();
}
?>


<?php include "header.php"; ?>


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
                        <input type="text" id="searchHospedes" class="form-control" placeholder="Search" onkeyup="searchRecords('hospedesTable', 'searchHospedes')">
                    </div>

                    <!-- Admin Filter Checkbox -->
                    <div class="form-group">
                        <label for="adminFilter">Show Only Admins</label>
                        <input type="checkbox" id="adminFilter" onchange="searchRecords('hospedesTable', 'searchHospedes')">
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
                            <tr data-is-admin="<?php echo $row['is_admin'] == 1 ? '1' : '0'; ?>">
                                <td><?php echo $row['id_hospede']; ?></td>
                                <td><?php echo $row['nome']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['telefone']; ?></td>
                                <td><?php echo $row['endereco']; ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="openeditHospedeModal(<?php echo $row['id_hospede']; ?>)">Edit</button>
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
                $result = $conn->query("SELECT r.*, h.email FROM reservas r JOIN hospedes h ON r.id_hospede = h.id_hospede");
                ?>
                <div class="container mt-5">
                    <h2>Records</h2>
                    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addReservationModal">Add Reservation</button>
                    <div class="form-group">
                        <label for="searchReservations">Search:</label>
                        <input type="text" id="searchReservations" class="form-control" placeholder="Search" onkeyup="searchRecords('reservationsTable', 'searchReservations')">
                    </div>
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
                    <!-- Button to trigger Add Room modal -->
                    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addRoomModal">Add Room</button>
                    <div class="form-group">
                        <label for="searchRooms">Search:</label>
                        <input type="text" id="searchRooms" class="form-control" placeholder="Search" onkeyup="searchRecords('roomsTable', 'searchRooms')">
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
                                    <td><?php echo $row['num_pessoas'] . ' ' . ($row['num_pessoas'] > 1 ? 'pessoas' : 'pessoa'); ?></td>
                                    <td>
                                        <!-- Edit button -->
                                        <button class="btn btn-primary btn-sm" onclick="openEditRoomModal(<?php echo $row['id_quarto']; ?>, <?php echo $row['num_pessoas']; ?>)">Edit</button>
                                        
                                        <!-- Delete button -->
                                        <button class="btn btn-danger btn-sm" onclick="deleteRoom(<?php echo $row['id_quarto']; ?>)">Delete</button>
                                    </td>

                                    </td>
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
                    <!-- Admin Checkbox -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin">
                        <label class="form-check-label" for="is_admin">Is Admin</label>
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

<!-- Edit Hospede Modal -->
<div class="modal fade" id="editHospedeModal" tabindex="-1" aria-labelledby="editHospedeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editHospedeModalLabel">Edit Hospede</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
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
                    <!-- Admin Checkbox -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_is_admin" name="is_admin">
                        <label class="form-check-label" for="edit_is_admin">Is Admin</label>
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

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="add_room.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoomModalLabel">Add New Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="num_pessoas" class="form-label">Number of People</label>
                        <input type="number" class="form-control" id="num_pessoas" name="num_pessoas" required>
                    </div>
                    <!-- Additional fields for adding room if necessary -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Room Modal -->
<div class="modal fade" id="editRoomModal" tabindex="-1" aria-labelledby="editRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="edit_room.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoomModalLabel">Edit Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_quarto" id="editRoomId"> <!-- Hidden input to store the room ID -->
                    
                    <div class="form-group">
                        <label for="editNumPessoas">Number of People:</label>
                        <input type="number" name="num_pessoas" id="editNumPessoas" class="form-control" required>
                    </div>

                    <!-- You can add other fields here if you need more information (e.g., room type, price, etc.) -->
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

    function searchRecords(tableId, searchId) {
        var input, filter, table, tr, td, i, j, txtValue, adminFilterChecked;
        
        // Get the search input value
        input = document.getElementById(searchId);
        filter = input.value.toUpperCase();  // Convert to uppercase for case-insensitive matching
        table = document.getElementById(tableId);
        tr = table.getElementsByTagName("tr");

        // Get the status of the admin filter checkbox
        adminFilterChecked = document.getElementById("adminFilter").checked;

        for (i = 1; i < tr.length; i++) {  // Start from 1 to skip the header row
            tr[i].style.display = "none";  // Initially hide the row
            td = tr[i].getElementsByTagName("td");

            var rowMatchesSearch = false;
            var rowIsAdmin = tr[i].getAttribute("data-is-admin") === "1";  // Check if this row is an admin

            // If the admin filter is checked, ensure this row is an admin
            if (adminFilterChecked && !rowIsAdmin) {
                continue;  // Skip non-admin rows
            }

            // Loop through each column to see if it matches the search term
            for (j = 0; j < td.length; j++) {  // Loop through columns in each row
                if (td[j]) {  // Check if the column exists
                    txtValue = td[j].textContent || td[j].innerText;  // Get the text content
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {  // Case-insensitive match
                        rowMatchesSearch = true;  // Row matches the search
                        break;
                    }
                }
            }

            // Show the row if it matches both the search term and admin filter
            if (rowMatchesSearch) {
                tr[i].style.display = "";  // Show the row
            }
        }
    }

    function openeditHospedeModal(id) {
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
                
                // Check if the user is an admin and set the checkbox accordingly
                document.getElementById('edit_is_admin').checked = data.is_admin == 1;

                // Show the modal
                var editHospedeModal = new bootstrap.Modal(document.getElementById('editHospedeModal'));
                editHospedeModal.show();
            })
            .catch(error => console.error('Error:', error));
    }

    document.getElementById('editForm').addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('edit_hospedes.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            alert(result);
            location.reload(); // This ensures the page reloads after saving changes
        })
        .catch(error => console.error('Error:', error));
    });

    function deleteRecord(id) {
        if (confirm('Are you sure you want to delete this record?')) {
            window.location.href = `delete_hospedes.php?id=${id}`;
        }
    }

    function openEditReservationModal(id, email, numGuests, checkinDate, checkoutDate) {
        // Populate the modal fields with the reservation data
        document.getElementById('editReservationId').value = id;
        document.getElementById('editEmail').value = email;
        document.getElementById('editNumGuests').value = numGuests;
        document.getElementById('editCheckinDate').value = checkinDate;
        document.getElementById('editCheckoutDate').value = checkoutDate;
        
        // Show the modal
        const editHospedeModal = new bootstrap.Modal(document.getElementById('editReservationModal'));
        editHospedeModal.show();
    }

    function deleteReservation(id) {
        if (confirm('Are you sure you want to delete this reservation?')) {
            // Redirect to delete_reservations.php with the reservation ID
            window.location.href = `delete_reservation.php?id=${id}`;
        }
    }

    function openEditRoomModal(id_quarto, num_pessoas) {
        // Populate the modal with existing room details
        document.getElementById('editRoomId').value = id_quarto;
        document.getElementById('editNumPessoas').value = num_pessoas;

        // Show the modal
        var myModal = new bootstrap.Modal(document.getElementById('editRoomModal'), {
            keyboard: false
        });
        myModal.show();
    }

    function deleteRoom(id_quarto) {
        if (confirm('Are you sure you want to delete this record?')) {
            window.location.href = `delete_room.php?id=${id_quarto}`;
        }
    }
</script>
<?php include "footer.php";?>