<?php
// ============================================
// Session & Auth Helper Functions – Hope Haven
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /hope_haven/auth/login.php");
        exit();
    }
}

// Require specific role
function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        header("Location: /hope_haven/auth/login.php?error=unauthorized");
        exit();
    }
}

// Get logged-in user's info
function currentUser() {
    return [
        'id'   => $_SESSION['user_id']   ?? null,
        'name' => $_SESSION['name']      ?? '',
        'role' => $_SESSION['role']      ?? '',
        'email'=> $_SESSION['email']     ?? '',
    ];
}

// Sanitize input
function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Redirect with message
function redirect($url, $msg = '', $type = 'success') {
    if ($msg) {
        $_SESSION['flash_msg']  = $msg;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $url");
    exit();
}

// Show flash message and clear it
function flashMsg() {
    if (isset($_SESSION['flash_msg'])) {
        $type = $_SESSION['flash_type'] ?? 'success';
        $msg  = $_SESSION['flash_msg'];
        unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
        $icon = $type === 'success' ? '✓' : ($type === 'error' ? '✗' : 'ℹ');
        echo "<div class='flash flash-$type'>$icon $msg</div>";
    }
}

// Status badge HTML
function statusBadge($status) {
    $classes = [
        'pending'  => 'badge-pending',
        'approved' => 'badge-approved',
        'rejected' => 'badge-rejected',
    ];
    $class = $classes[$status] ?? 'badge-pending';
    return "<span class='badge $class'>" . ucfirst($status) . "</span>";
}

// Get next Sunday date
function nextSunday() {
    $today = date('N'); // 1=Mon, 7=Sun
    $daysUntilSunday = (7 - $today) % 7;
    if ($daysUntilSunday === 0) $daysUntilSunday = 7;
    return date('Y-m-d', strtotime("+$daysUntilSunday days"));
}
?>
