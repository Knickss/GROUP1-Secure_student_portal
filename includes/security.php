<?php

// Escape output safely
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Clean input values (trim + strip tags + escape)
function clean($value) {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

// Email validation
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Prevent dangerous file uploads
function validateImageUpload($file) {
    $allowed = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) return false;
    if ($file['size'] > 5 * 1024 * 1024) return false; // max 5MB

    return true;
}

// Generate CSRF token
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf($token) {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}
