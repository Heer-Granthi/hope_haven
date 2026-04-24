<?php
// donor/book_appointment.php – Book a visit appointment
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('donor');

$pageTitle = 'Book Visit';
$user = currentUser();
$uid  = $user['id'];
$error = '';

$oResult = mysqli_query($conn, "SELECT id, name, location FROM orphanages ORDER BY name");
$orphanages = mysqli_fetch_all($oResult, MYSQLI_ASSOC);

$preselect  = (int)($_GET['oid'] ?? 0);
$nextSunday = nextSunday();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orphanage_id   = (int)($_POST['orphanage_id'] ?? 0);
    $visit_date     = clean($_POST['visit_date'] ?? '');
    $visit_time     = clean($_POST['visit_time'] ?? '');
    $purpose        = clean($_POST['purpose'] ?? '');
    $visitors_count = max(1, (int)($_POST['visitors_count'] ?? 1));

    if (!$orphanage_id || !$visit_date || !$visit_time) {
        $error = 'Please fill in all required fields.';
    } elseif (strtotime($visit_date) < strtotime(date('Y-m-d'))) {
        $error = 'Visit date cannot be in the past.';
    } else {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO appointments (donor_id, orphanage_id, visit_date, visit_time, purpose, visitors_count, status)
             VALUES (?, ?, ?, ?, ?, ?, 'pending')"
        );
        mysqli_stmt_bind_param($stmt, 'iisssi', $uid, $orphanage_id, $visit_date, $visit_time, $purpose, $visitors_count);

        if (mysqli_stmt_execute($stmt)) {
            redirect('my_appointments.php', 'Visit booked! Awaiting NGO/Admin approval.', 'success');
        } else {
            $error = 'Failed to book appointment. Please try again.';
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
            <li><a href="donate.php">💝 Make Donation</a></li>
            <li><a href="my_donations.php">📋 My Donations</a></li>
            <li><a href="book_appointment.php" class="active">📅 Book Visit</a></li>
            <li><a href="my_appointments.php">🗓️ My Visits</a></li>
            <li><a href="/hope_haven/orphanages.php">🏡 Orphanages</a></li>
            <li><a href="/hope_haven/auth/logout.php" class="logout-link">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <div class="dash-header">
            <h2>📅 Book a Visit</h2>
            <p>Schedule a visit to an orphanage and connect with the children.</p>
        </div>

        <!-- Caring Connections info -->
        <div class="sunday-feature">
            <div class="sunday-icon">☀️</div>
            <div>
                <h3>Caring Connections – Sunday Visits</h3>
                <p>We recommend Sunday visits through our Caring Connections programme. The next available Sunday is <strong><?= date('D, d M Y', strtotime($nextSunday)) ?></strong>. All visits require prior NGO approval.</p>
            </div>
        </div>

        <?php if ($error): ?>
        <div class="flash flash-error">✗ <?= $error ?></div>
        <?php endif; ?>

        <div class="form-card" style="max-width:600px;">
            <h3>Visit Appointment Form</h3>
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

                <div class="form-row">
                    <div class="form-group">
                        <label for="visit_date">Visit Date *</label>
                        <input type="date" id="visit_date" name="visit_date" required
                               value="<?= clean($_POST['visit_date'] ?? $nextSunday) ?>"
                               min="<?= date('Y-m-d') ?>">
                        <p class="form-hint" id="sunday-note">Sundays recommended for Caring Connections.</p>
                    </div>
                    <div class="form-group">
                        <label for="visit_time">Preferred Time *</label>
                        <select id="visit_time" name="visit_time" required>
                            <?php
                            $times = ['09:00','10:00','11:00','14:00','15:00','16:00'];
                            foreach ($times as $t):
                                $sel = ($_POST['visit_time'] ?? '10:00') === $t ? 'selected' : '';
                            ?>
                            <option value="<?= $t ?>" <?= $sel ?>><?= date('h:i A', strtotime($t)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="visitors_count">Number of Visitors</label>
                    <input type="number" id="visitors_count" name="visitors_count" min="1" max="20"
                           value="<?= (int)($_POST['visitors_count'] ?? 1) ?>">
                </div>

                <div class="form-group">
                    <label for="purpose">Purpose of Visit</label>
                    <textarea id="purpose" name="purpose" placeholder="E.g. Donation handover, Sunday caring visit, volunteer work..."><?= clean($_POST['purpose'] ?? '') ?></textarea>
                </div>

                <div class="info-box">
                    Your appointment request will be reviewed by the NGO or Orphanage Admin. You'll receive a status update on this portal.
                </div>

                <button type="submit" class="btn btn-sky">Book Appointment →</button>
            </form>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
