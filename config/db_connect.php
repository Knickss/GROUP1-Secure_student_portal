<?php
$host = "localhost";
$user = "root";  // use Laragon's default user
$pass = "";      // no password in Laragon by default
$dbname = "escolink_centra";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
