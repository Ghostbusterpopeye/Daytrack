<?php
/**
 * DayTrack – Auth Guard
 * includes/auth_check.php
 *
 * Include this at the TOP of any protected page or API file.
 * For API files pass $apiMode = true before including to get JSON 401 instead of redirect.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$apiMode = $apiMode ?? false;

if (empty($_SESSION['user_id'])) {
    if ($apiMode) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Unauthorized. Please log in.']);
        exit;
    } else {
        // Calculate depth-aware redirect path
        $script = $_SERVER['SCRIPT_FILENAME'] ?? '';
        $root   = realpath(__DIR__ . '/../') ?: '';
        $rel    = $root ? ltrim(str_replace('\\', '/', substr($script, strlen($root))), '/') : '';
        $depth  = substr_count($rel, '/');
        $prefix = str_repeat('../', $depth);
        header('Location: ' . $prefix . 'frontend/pages/login.php');
        exit;
    }
}

// Provide convenience variable for current user
$currentUser = [
    'id'    => $_SESSION['user_id'],
    'name'  => $_SESSION['user_name']  ?? 'User',
    'email' => $_SESSION['user_email'] ?? '',
    'role'  => $_SESSION['user_role']  ?? 'Team Member',
    'bio'   => $_SESSION['user_bio']   ?? '',
];
