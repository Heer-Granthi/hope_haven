<?php
// donor/my_appointments.php
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('donor');

$pageTitle = 'My Visits';
$user = currentUser();
$uid  = $user['id'];

$result = mysqli_query($conn,
    "SELECT a.*, o.name AS orphanage_name, o.location
     FROM appointments a
     JOIN orphanages o ON o.id = a.orphanage_id
     WHERE a.donor_id = $uid
     ORDER BY a.visit_date DESC"
);
$appointments = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
            <li><a href="book_appointment.php">📅 Book Visit</a></li>
            <li><a href="my_appointments.php" class="active">🗓️ My Visits</a></li>
            <li><a href="/hope_haven/orphanages.php">🏡 Orphanages</a></li>
            <li><a href="/hope_haven/auth/logout.php" class="logout-link">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <div class="dash-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <h2>🗓️ My Visit Appointments</h2>
                <p>Track the status of all your orphanage visit bookings.</p>
            </div>
            <a href="book_appointment.php" class="btn btn-sky">+ Book Visit</a>
        </div>

        <div class="table-wrap">
            <?php if (empty($appointments)): ?>
                <p class="table-empty">No visits booked yet.<br><a href="book_appointment.php">Book your first visit →</a></p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Orphanage</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Visitors</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Admin Note</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($appointments as $i => $a): ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td>
                            <strong><?= clean($a['orphanage_name']) ?></strong><br>
                            <small style="color:var(--muted)"><?= clean($a['location']) ?></small>
                        </td>
                        <td><?= date('D, d M Y', strtotime($a['visit_date'])) ?></td>
                        <td><?= date('h:i A', strtotime($a['visit_time'])) ?></td>
                        <td><?= $a['visitors_count'] ?></td>
                        <td><?= clean($a['purpose']) ?: '—' ?></td>
                        <td><?= statusBadge($a['status']) ?></td>
                        <td><?= clean($a['admin_note']) ?: '—' ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
