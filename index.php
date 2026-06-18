<?php
/**
 * DayTrack – Entry Point
 * index.php
 */
if (session_status() === PHP_SESSION_NONE) session_start();

if (!empty($_SESSION['user_id'])) {
    header('Location: frontend/pages/dashboard.php');
} else {
    header('Location: frontend/pages/login.php');
}
exit;
