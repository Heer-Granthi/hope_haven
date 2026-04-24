<?php
// donor/donate.php – Submit a donation request
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('donor');

$pageTitle = 'Make a Donation';
$user = currentUser();
$uid  = $user['id'];
$error = '';

// Fetch orphanages for dropdown
$oResult = mysqli_query($conn, "SELECT id, name, location FROM orphanages ORDER BY name");
$orphanages = mysqli_fetch_all($oResult, MYSQLI_ASSOC);

// Pre-select orphanage if coming from card button
$preselect = (int)($_GET['oid'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orphanage_id = (int)($_POST['orphanage_id'] ?? 0);
    $type         = clean($_POST['type'] ?? '');
    $description  = clean($_POST['description'] ?? '');
    $amount       = $type === 'money' ? floatval($_POST['amount'] ?? 0) : null;
    $validTypes   = ['food','clothes','money','medicines','books','other'];

    if (!$orphanage_id || !in_array($type, $validTypes)) {
        $error = 'Please fill in all required fields correctly.';
    } elseif ($type === 'money' && $amount <= 0) {
        $error = 'Please enter a valid donation amount.';
    } else {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO donation_requests (donor_id, orphanage_id, type, description, amount, status)
             VALUES (?, ?, ?, ?, ?, 'pending')"
        );
        mysqli_stmt_bind_param($stmt, 'iissd', $uid, $orphanage_id, $type, $description, $amount);

        if (mysqli_stmt_execute($stmt)) {
            redirect('my_donations.php', 'Donation request submitted! The NGO will review it shortly.', 'success');
        } else {
            $error = 'Failed to submit request. Please try again.';
        }
    }
}

include '../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-user">
            <div class="sidebar-avatar">🤝</div>
            <h4><?= clean($user['name']) ?></h4>
            <p>Donor Account</p>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">🏠 Dashboard</a></li>
            <li><a href="donate.php" class="active">💝 Make Donation</a></li>
            <li><a href="my_donations.php">📋 My Donations</a></li>
            <li><a href="book_appointment.php">📅 Book Visit</a></li>
            <li><a href="my_appointments.php">🗓️ My Visits</a></li>
            <li><a href="/hope_haven/orphanages.php">🏡 Orphanages</a></li>
            <li><a href="/hope_haven/auth/logout.php" class="logout-link">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <div class="dash-header">
            <h2>💝 Make a Donation</h2>
            <p>Your generosity helps children grow, learn, and thrive.</p>
        </div>

        <?php if ($error): ?>
        <div class="flash flash-error">✗ <?= $error ?></div>
        <?php endif; ?>

        <div class="form-card" style="max-width:600px;">
            <h3>Donation Request Form</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="orphanage_id">Select Orphanage *</label>
                    <select id="orphanage_id" name="orphanage_id" required>
                        <option value="">-- Choose an orphanage --</option>
                        <?php foreach ($orphanages as $o): ?>
                        <option value="<?= $o['id'] ?>" <?= ($preselect === $o['id'] || (int)($_POST['orphanage_id']??0) === $o['id']) ? 'selected' : '' ?>>
                            <?= clean($o['name']) ?> – <?= clean($o['location']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="donation_type">Donation Type *</label>
                    <select id="donation_type" name="type" required>
                        <option value="">-- Select type --</option>
                        <option value="food"      <?= ($_POST['type']??'')==='food'      ?'selected':'' ?>>🍚 Food & Groceries</option>
                        <option value="clothes"   <?= ($_POST['type']??'')==='clothes'   ?'selected':'' ?>>👕 Clothes</option>
                        <option value="money"     <?= ($_POST['type']??'')==='money'     ?'selected':'' ?>>💵 Money</option>
                        <option value="medicines" <?= ($_POST['type']??'')==='medicines' ?'selected':'' ?>>💊 Medicines</option>
                        <option value="books"     <?= ($_POST['type']??'')==='books'     ?'selected':'' ?>>📚 Books & Stationery</option>
                        <option value="other"     <?= ($_POST['type']??'')==='other'     ?'selected':'' ?>>🎁 Other</option>
                    </select>
                </div>

                <div class="form-group" id="amount-group" style="display:none;">
                    <label for="amount">Amount (₹) *</label>
                    <input type="number" id="amount" name="amount" min="1" step="1" placeholder="e.g. 1000" value="<?= (int)($_POST['amount']??0) ?: '' ?>">
                    <p class="form-hint">Enter amount in Indian Rupees</p>
                </div>

                <div class="form-group">
                    <label for="description">Description / Details</label>
                    <textarea id="description" name="description" placeholder="Describe what you wish to donate, quantities, etc."><?= clean($_POST['description'] ?? '') ?></textarea>
                </div>

                <div class="info-box">
                    Your request will be reviewed by the NGO. Once approved, you'll be contacted for handover coordination.
                </div>

                <button type="submit" class="btn btn-green">Submit Donation Request →</button>
            </form>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
