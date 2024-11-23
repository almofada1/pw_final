<?php
$servername = "pwFinalDB";
$username = "fragalha";
$password = "fragalha";
$dbname = "pwFinalDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
