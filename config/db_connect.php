<?php
// -----------------------------------------
// SECURE SESSION COOKIE SETTINGS
// -----------------------------------------

// Prevent JavaScript from accessing the session cookie
ini_set('session.cookie_httponly', 1);

// Prevent cross-site cookie sending
ini_set('session.cookie_samesite', 'Strict');

// IMPORTANT: Enable this ONLY when deployed on HTTPS
// ini_set('session.cookie_secure', 1);

// -----------------------------------------
// DATABASE CONNECTION
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
