<?php
// includes/header.php – Shared navbar for all pages
if (session_status() === PHP_SESSION_NONE) session_start();
$user = currentUser();
$role = $user['role'] ?? '';

// Dashboard links per role
$dashLinks = [
    'donor' => '/hope_haven/donor/dashboard.php',
    'ngo'   => '/hope_haven/ngo/dashboard.php',
    'admin' => '/hope_haven/admin/dashboard.php',
];
$dashUrl = $dashLinks[$role] ?? '/hope_haven/index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Hope Haven' ?> – Hope Haven</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/hope_haven/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="/hope_haven/index.php" class="nav-brand">
        <span class="brand-icon">🏠</span>
        <span>Hope <strong>Haven</strong></span>
    </a>

    <button class="hamburger" onclick="toggleNav()" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>

    <ul class="nav-links" id="navLinks">
        <li><a href="/hope_haven/index.php">Home</a></li>
        <li><a href="/hope_haven/orphanages.php">Orphanages</a></li>

        <?php if (isLoggedIn()): ?>
            <li><a href="<?= $dashUrl ?>">Dashboard</a></li>
            <li class="nav-user">
                <span>👤 <?= clean($user['name']) ?></span>
                <span class="role-pill"><?= ucfirst($role) ?></span>
            </li>
            <li><a href="/hope_haven/auth/logout.php" class="btn-nav btn-outline">Logout</a></li>
        <?php else: ?>
            <li><a href="/hope_haven/auth/login.php" class="btn-nav btn-outline">Login</a></li>
            <li><a href="/hope_haven/auth/register.php" class="btn-nav btn-primary">Join Now</a></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="flash-wrap">
    <?php if (function_exists('flashMsg')) flashMsg(); ?>
</div>
