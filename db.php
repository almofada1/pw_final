<?php
$servername = "my-mysql";
$username = "root";
$password = "fragalha";
$dbname = "pw_final";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
