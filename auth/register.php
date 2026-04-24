<?php
// auth/register.php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$pageTitle = 'Register';
$error = '';

if (isLoggedIn()) redirect('../index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = clean($_POST['name']     ?? '');
    $email    = clean($_POST['email']    ?? '');
    $password = $_POST['password']       ?? '';
    $confirm  = $_POST['confirm']        ?? '';
    $role     = clean($_POST['role']     ?? 'donor');

    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!in_array($role, ['donor','ngo','admin'])) {
        $error = 'Invalid role selected.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check email uniqueness
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, 's', $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = 'An account with this email already exists.';
        } else {
            // Insert user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $hash, $role);

            if (mysqli_stmt_execute($stmt)) {
                redirect('/hope_haven/auth/login.php', 'Account created! Please login to continue.', 'success');
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card" style="max-width:520px;">
        <div class="auth-logo">💝</div>
        <h2>Join Hope Haven</h2>
        <p class="subtitle">Create your account and start making a difference</p>

        <?php if ($error): ?>
        <div class="flash flash-error">✗ <?= clean($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>I am joining as</label>
                <div class="role-select-group">
                    <?php
                    $roles = [
                        'donor' => ['🤝','Donor'],
                        'ngo'   => ['🌐','NGO'],
                        'admin' => ['🏠','Orphanage Admin'],
                    ];
                    $selected = $_POST['role'] ?? 'donor';
                    foreach ($roles as $val => [$icon, $label]):
                    ?>
                    <input type="radio" name="role" id="role_<?= $val ?>" value="<?= $val ?>" class="role-option" <?= $selected === $val ? 'checked' : '' ?>>
                    <label for="role_<?= $val ?>" class="role-label">
                        <span class="role-icon"><?= $icon ?></span>
                        <?= $label ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="name">Full Name / Organisation Name</label>
                <input type="text" id="name" name="name" required placeholder="Your name" value="<?= clean($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="you@example.com" value="<?= clean($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Min. 6 characters">
                </div>
                <div class="form-group">
                    <label for="confirm">Confirm Password</label>
                    <input type="password" id="confirm" name="confirm" required placeholder="Repeat password">
                </div>
            </div>

            <button type="submit" class="btn btn-terra" style="width:100%;justify-content:center;margin-top:0.5rem;">Create Account →</button>
        </form>

        <p class="auth-footer">
            Already have an account? <a href="/hope_haven/auth/login.php">Login here</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
