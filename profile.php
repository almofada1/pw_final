<?php
include 'db.php';
session_start();
$userId = $_SESSION['user_id']; // Assuming you already have the user ID in session

// Check if the connection is open
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch user details
$result = $conn->query("SELECT * FROM hospedes WHERE id_hospede = $userId");
if ($result === false) {
    die("Error fetching user details: " . $conn->error);
}

$user = $result->fetch_assoc();

// Fetch user's reservations
$reservations = $conn->query("SELECT r.*, q.num_pessoas FROM reservas r
                              JOIN quartos q ON r.id_quarto = q.id_quarto
                              WHERE r.id_hospede = $userId");

if ($reservations === false) {
    die("Error fetching reservations: " . $conn->error);
}

?>
<?php include "header.php"; ?>

<div class="container mt-5">
    <h2>User Profile</h2>

    <!-- User Information (Editable) -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Profile Information</h5>
            <form action="update_profile.php" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['telefone']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['endereco']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <!-- User's Reservations -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Your Reservations</h5>
            <?php if ($reservations->num_rows > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Reservation ID</th>
                            <th>Room ID</th>
                            <th>Num People</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $reservations->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id_reserva']; ?></td>
                                <td><?php echo $row['id_quarto']; ?></td>
                                <td><?php echo $row['num_pessoas']; ?></td>
                                <td><?php echo $row['data_checkin']; ?></td>
                                <td><?php echo $row['data_checkout']; ?></td>
                                <td><?php echo $row['valor_total']; ?></td>
                                <td>
                                    <button onclick="openEditReservationModal(
                                        <?= $row['id_reserva']; ?>,
                                        '<?= htmlspecialchars($user['email']); ?>',
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
            <?php else: ?>
                <p>No reservations found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal for Editing Reservation -->
<div class="modal fade" id="editReservationModal" tabindex="-1" aria-labelledby="editReservationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="edit_reservation.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReservationLabel">Edit Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_reserva" id="editReservationId"> <!-- Reservation ID -->
                    <input type="hidden" name="email" id="editEmail"> <!-- Hidden Email Field -->
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
    function openEditReservationModal(id, email, numGuests, checkinDate, checkoutDate) {
        document.getElementById('editReservationId').value = id;
        document.getElementById('editEmail').value = email; // Set the email as a hidden field
        document.getElementById('editNumGuests').value = numGuests;
        document.getElementById('editCheckinDate').value = checkinDate;
        document.getElementById('editCheckoutDate').value = checkoutDate;
        
        const editHospedeModal = new bootstrap.Modal(document.getElementById('editReservationModal'));
        editHospedeModal.show();
    }

    function deleteReservation(id) {
        if (confirm('Are you sure you want to delete this reservation?')) {
            window.location.href = `delete_reservation.php?id=${id}`;
        }
    }
</script>

<?php include "footer.php"; ?>