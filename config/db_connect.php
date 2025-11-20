<?php
// -----------------------------------------
// DATABASE CONNECTION ONLY
// -----------------------------------------

$host = "localhost";
$user = "root";   // Laragon default
$pass = "";       // Laragon default
$dbname = "escolink_centra";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

