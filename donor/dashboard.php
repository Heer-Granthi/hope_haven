<?php
// donor/dashboard.php – Donor's main dashboard
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('donor');

$pageTitle = 'Donor Dashboard';
$user = currentUser();
$uid  = $user['id'];

// Stats
$r1 = mysqli_query($conn, "SELECT COUNT(*) as c FROM donation_requests WHERE donor_id=$uid");
$r2 = mysqli_query($conn, "SELECT COUNT(*) as c FROM donation_requests WHERE donor_id=$uid AND status='approved'");
$r3 = mysqli_query($conn, "SELECT COUNT(*) as c FROM appointments WHERE donor_id=$uid");
$r4 = mysqli_query($conn, "SELECT COUNT(*) as c FROM appointments WHERE donor_id=$uid AND status='approved'");
$s1 = mysqli_fetch_assoc($r1); $s2 = mysqli_fetch_assoc($r2);
$s3 = mysqli_fetch_assoc($r3); $s4 = mysqli_fetch_assoc($r4);

// Recent donation requests
$dResult = mysqli_query($conn,
    "SELECT dr.*, o.name AS orphanage_name
     FROM donation_requests dr
     JOIN orphanages o ON o.id = dr.orphanage_id
     WHERE dr.donor_id = $uid
     ORDER BY dr.created_at DESC LIMIT 5"
);
$donations = mysqli_fetch_all($dResult, MYSQLI_ASSOC);

// Recent appointments
$aResult = mysqli_query($conn,
    "SELECT a.*, o.name AS orphanage_name
     FROM appointments a
     JOIN orphanages o ON o.id = a.orphanage_id
     WHERE a.donor_id = $uid
     ORDER BY a.created_at DESC LIMIT 5"
);
$appointments = mysqli_fetch_all($aResult, MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-user">
            <div class="sidebar-avatar">🤝</div>
            <h4><?= clean($user['name']) ?></h4>
            <p>Donor Account</p>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active">🏠 Dashboard</a></li>
            <li><a href="donate.php">💝 Make Donation</a></li>
            <li><a href="my_donations.php">📋 My Donations</a></li>
            <li><a href="book_appointment.php">📅 Book Visit</a></li>
            <li><a href="my_appointments.php">🗓️ My Visits</a></li>
            <li><a href="/hope_haven/orphanages.php">🏡 Orphanages</a></li>
            <li><a href="/hope_haven/auth/logout.php" class="logout-link">🚪 Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="dash-content">
        <div class="dash-header">
            <h2>Good to see you, <?= clean($user['name']) ?>! 👋</h2>
            <p>Here's an overview of your contributions and upcoming visits.</p>
        </div>

        <!-- Stat Cards -->
        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-icon">💝</div>
                <div><h3><?= $s1['c'] ?></h3><p>Total Donations</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div><h3><?= $s2['c'] ?></h3><p>Approved</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div><h3><?= $s3['c'] ?></h3><p>Visits Booked</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🎉</div>
                <div><h3><?= $s4['c'] ?></h3><p>Visits Approved</p></div>
            </div>
        </div>

        <!-- Caring Connections Banner -->
        <div class="sunday-feature">
            <div class="sunday-icon">☀️</div>
            <div>
                <h3>Caring Connections – Sunday Visit</h3>
                <p>Schedule your next Sunday visit. Spend quality time with the children and bring joy into their lives.</p>
                <a href="book_appointment.php" class="btn btn-ghost btn-sm" style="margin-top:0.75rem;">Schedule a Sunday Visit →</a>
            </div>
        </div>

        <!-- Recent Donations -->
        <div class="table-wrap">
            <h3>💝 Recent Donations
                <a href="donate.php" class="btn btn-green btn-sm" style="margin-left:auto;">+ New Donation</a>
            </h3>
            <?php if (empty($donations)): ?>
                <p class="table-empty">No donations yet. <a href="donate.php">Make your first donation →</a></p>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>Orphanage</th><th>Type</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                <?php foreach ($donations as $d): ?>
                    <tr>
                        <td><?= clean($d['orphanage_name']) ?></td>
                        <td><?= ucfirst($d['type']) ?></td>
                        <td><?= statusBadge($d['status']) ?></td>
                        <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- Recent Appointments -->
        <div class="table-wrap">
            <h3>📅 Recent Visits
                <a href="book_appointment.php" class="btn btn-sky btn-sm" style="margin-left:auto;">+ Book Visit</a>
            </h3>
            <?php if (empty($appointments)): ?>
                <p class="table-empty">No visits booked yet. <a href="book_appointment.php">Book a visit →</a></p>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>Orphanage</th><th>Date</th><th>Time</th><th>Status</th></tr>
                </thead>
                <tbody>
                <?php foreach ($appointments as $a): ?>
                    <tr>
                        <td><?= clean($a['orphanage_name']) ?></td>
                        <td><?= date('D, d M Y', strtotime($a['visit_date'])) ?></td>
                        <td><?= date('h:i A', strtotime($a['visit_time'])) ?></td>
                        <td><?= statusBadge($a['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
