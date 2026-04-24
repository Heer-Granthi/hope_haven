<?php
// auth/login.php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$pageTitle = 'Login';
$error = '';

// Redirect if already logged in
if (isLoggedIn()) {
    $dash = ['donor'=>'../donor/dashboard.php','ngo'=>'../ngo/dashboard.php','admin'=>'../admin/dashboard.php'];
    redirect($dash[$_SESSION['role']] ?? '../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['email']   = $user['email'];

            // Redirect per role
            $dest = [
                'donor' => '../donor/dashboard.php',
                'ngo'   => '../ngo/dashboard.php',
                'admin' => '../admin/dashboard.php',
            ];
            redirect($dest[$user['role']] ?? '../index.php', 'Welcome back, ' . $user['name'] . '!');
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

include '../includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">🏠</div>
        <h2>Welcome Back</h2>
        <p class="subtitle">Sign in to your Hope Haven account</p>

        <?php if ($error): ?>
        <div class="flash flash-error">✗ <?= clean($error) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
        <div class="flash flash-error">✗ You are not authorized to view that page.</div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="you@example.com" value="<?= clean($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Your password">
            </div>
            <button type="submit" class="btn btn-green" style="width:100%;justify-content:center;">Sign In →</button>
        </form>

        <div class="info-box" style="margin-top:1.5rem;">
            <strong>Demo Accounts (password: password123)</strong><br>
            🤝 Donor: donor@hopehaven.com<br>
            🌐 NGO: ngo@hopehaven.com<br>
            🏠 Admin: admin@hopehaven.com
        </div>

        <p class="auth-footer">
            Don't have an account? <a href="/hope_haven/auth/register.php">Register here</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
