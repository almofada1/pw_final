<?php
include 'db.php';  // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $num_pessoas = $_POST['num_pessoas'];

    // Sanitize input data to prevent SQL injection
    $num_pessoas = mysqli_real_escape_string($conn, $num_pessoas);

    // Insert the new room into the 'quartos' table
    $query = "INSERT INTO quartos (num_pessoas) VALUES ('$num_pessoas')";

    if ($conn->query($query) === TRUE) {
        // If the query was successful, redirect back to the page with a success message
        header("Location: admin.php?success=Room added successfully");
        exit();
    } else {
        // If the query failed, display an error message
        echo "Error: " . $conn->error;
    }
}
?>
