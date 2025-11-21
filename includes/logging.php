<?php
// Simple reusable logging helper for activity_logs table
// Usage: log_activity($conn, $user_id, 'Action name', 'Details here', 'success');

if (!function_exists('log_activity')) {
    function log_activity(mysqli $conn, ?int $user_id, string $action, string $details = '', string $status = 'success'): void
    {
        $action  = trim($action);
        $details = trim($details);

        if ($action === '') {
            // nothing to log
            return;
        }

        // normalize status
        $status = ($status === 'failed') ? 'failed' : 'success';

        // basic IP capture (fine for local XAMPP)
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;

        // get role based on user_id (can be null for failed login attempts)
        $role = null;
        if (!is_null($user_id)) {
            $stmtRole = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
            if ($stmtRole) {
                $stmtRole->bind_param("i", $user_id);
                if ($stmtRole->execute()) {
                    $stmtRole->bind_result($dbRole);
                    if ($stmtRole->fetch()) {
                        $role = $dbRole;
                    }
                }
                $stmtRole->close();
            }
        }

        $stmt = $conn->prepare("
            INSERT INTO activity_logs (user_id, action, details, ip_address, role, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            // silently ignore logging failure
            return;
        }

        // user_id can be null (for failed/unknown users)
        $stmt->bind_param(
            "isssss",
            $user_id,
            $action,
            $details,
            $ip_address,
            $role,
            $status
        );

        $stmt->execute();
        $stmt->close();
    }
}
