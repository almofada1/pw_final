<?php
session_start();
include 'db.php';  // Include your DB connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

$userId = $_SESSION['user_id'];  // Get logged-in user's ID

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get updated profile information from the form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Sanitize and validate the inputs
    $name = trim($name);
    $email = trim($email);
    $phone = trim($phone);
    $address = trim($address);

    // Check if the email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: profile.php"); // Redirect back to profile page
        exit();
    }

    // Update the profile information in the database
    $stmt = $conn->prepare("UPDATE hospedes SET nome = ?, email = ?, telefone = ?, endereco = ? WHERE id_hospede = ?");
    $stmt->bind_param("ssssi", $name, $email, $phone, $address, $userId);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update profile.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the profile page with a success or error message
    header("Location: profile.php");
    exit();
}
?>