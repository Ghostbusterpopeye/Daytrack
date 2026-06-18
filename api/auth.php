<?php
/**
 * DayTrack – Auth API
 * api/auth.php
 *
 * Actions:
 *   POST ?action=login    { email, password }
 *   POST ?action=logout
 *   POST ?action=register { name, email, password }
 *   GET  ?action=me
 */

// Prevent any stray output before JSON
ob_start();

if (session_status() === PHP_SESSION_NONE) session_start();

// Kill any buffered output (PHP notices/warnings) so only JSON comes out
ob_clean();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../config/database.php';

$action = isset($_GET['action'])  ? $_GET['action']  :
         (isset($_POST['action']) ? $_POST['action']  : '');
$method = $_SERVER['REQUEST_METHOD'];

// Parse JSON body
$input = [];
$raw = file_get_contents('php://input');
if ($raw) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) $input = $decoded;
}
$data = array_merge($_POST, $input);

/* ── Helpers ── */
function jsonOk(array $payload = []) {
    echo json_encode(array_merge(['success' => true], $payload));
    exit;
}
function jsonErr($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}
function setSession($user) {
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role']  = isset($user['role']) ? $user['role'] : 'Team Member';
    $_SESSION['user_bio']   = isset($user['bio'])  ? $user['bio']  : '';
}

/* ── Route ── */
switch ($action) {
    case 'login':    handleLogin($data);    break;
    case 'logout':   handleLogout();        break;
    case 'register': handleRegister($data); break;
    case 'me':       handleMe();            break;
    default:         jsonErr('Unknown action: ' . $action, 404);
}

/* ─────────────────────────────────────────── */
function handleLogin($d) {
    $email    = trim(isset($d['email'])    ? $d['email']    : '');
    $password = trim(isset($d['password']) ? $d['password'] : '');

    if (!$email || !$password) jsonErr('Email and password are required.');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jsonErr('Invalid email address.');

    $db   = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        jsonErr('Invalid email or password.', 401);
    }

    setSession($user);
    session_regenerate_id(true);

    jsonOk([
        'user' => [
            'id'    => (int)$user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
            'bio'   => $user['bio'],
        ]
    ]);
}

function handleLogout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    jsonOk(['message' => 'Logged out successfully.']);
}

function handleRegister($d) {
    $name     = trim(isset($d['name'])     ? $d['name']     : '');
    $email    = trim(isset($d['email'])    ? $d['email']    : '');
    $password = trim(isset($d['password']) ? $d['password'] : '');

    if (!$name || !$email || !$password) jsonErr('Name, email, and password are required.');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jsonErr('Invalid email address.');
    if (strlen($password) < 6) jsonErr('Password must be at least 6 characters.');

    $db   = getDB();
    $stmt = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) jsonErr('Email is already registered.');

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    $ins  = $db->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
    $ins->execute([$name, $email, $hash]);
    $id = (int)$db->lastInsertId();

    $user = ['id' => $id, 'name' => $name, 'email' => $email, 'role' => 'Team Member', 'bio' => ''];
    setSession($user);
    jsonOk(['user' => $user]);
}

function handleMe() {
    if (empty($_SESSION['user_id'])) jsonErr('Not authenticated.', 401);
    jsonOk([
        'user' => [
            'id'    => (int)$_SESSION['user_id'],
            'name'  => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role'  => $_SESSION['user_role'],
            'bio'   => $_SESSION['user_bio'],
        ]
    ]);
}
