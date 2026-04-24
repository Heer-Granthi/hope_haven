<?php
// admin/appointments.php – Admin: Manage visit schedule
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('admin');

$pageTitle = 'Visit Schedule';
$user = currentUser();
$uid  = $user['id'];

$oRes = mysqli_query($conn, "SELECT id, name FROM orphanages WHERE admin_id = $uid LIMIT 1");
$orphanage = mysqli_fetch_assoc($oRes);

if (!$orphanage) {
    redirect('manage_orphanage.php', 'Please set up your orphanage profile first.', 'info');
}

$oid    = $orphanage['id'];
$filter = clean($_GET['tab'] ?? 'upcoming');

// Upcoming approved visits
if ($filter === 'upcoming') {
    $result = mysqli_query($conn,
        "SELECT a.*, u.name AS donor_name, u.email AS donor_email
         FROM appointments a JOIN users u ON u.id = a.donor_id
         WHERE a.orphanage_id = $oid AND a.visit_date >= CURDATE() AND a.status = 'approved'
         ORDER BY a.visit_date ASC"
    );
} elseif ($filter === 'pending') {
    $result = mysqli_query($conn,
        "SELECT a.*, u.name AS donor_name, u.email AS donor_email
         FROM appointments a JOIN users u ON u.id = a.donor_id
         WHERE a.orphanage_id = $oid AND a.status = 'pending'
         ORDER BY a.created_at DESC"
    );
} else {
    $result = mysqli_query($conn,
        "SELECT a.*, u.name AS donor_name, u.email AS donor_email
         FROM appointments a JOIN users u ON u.id = a.donor_id
         WHERE a.orphanage_id = $oid
         ORDER BY a.visit_date DESC"
    );
}
$appointments = mysqli_fetch_all($result, MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-user">
            <div class="sidebar-avatar">🏠</div>
            <h4><?= clean($user['name']) ?></h4>
            <p>Orphanage Admin</p>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">🏠 Dashboard</a></li>
            <li><a href="manage_orphanage.php">✏️ Manage Orphanage</a></li>
            <li><a href="donations.php">💝 Donation Requests</a></li>
            <li><a href="appointments.php" class="active">📅 Visit Schedule</a></li>
            <li><a href="/hope_haven/auth/logout.php" class="logout-link">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <div class="dash-header">
            <h2>📅 Visit Schedule</h2>
            <p>Manage visit appointments for <strong><?= clean($orphanage['name']) ?></strong></p>
        </div>

        <!-- Caring Connections note -->
        <div class="sunday-feature" style="margin-bottom:2rem;">
            <div class="sunday-icon">☀️</div>
            <div>
                <h3>Caring Connections – Sunday Visits</h3>
                <p>Every Sunday, donors and volunteers can visit your orphanage through the Caring Connections programme. Visits pending approval are coordinated by the NGO.</p>
            </div>
        </div>

        <!-- Tabs -->
        <div style="display:flex;gap:0.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
            <?php foreach (['upcoming'=>'Upcoming Approved','pending'=>'Pending Review','all'=>'All Visits'] as $tab => $label): ?>
            <a href="?tab=<?= $tab ?>"
               class="btn btn-sm <?= $filter===$tab ? 'btn-sky' : '' ?>"
               style="<?= $filter!==$tab ? 'background:var(--warm);color:var(--mid);' : '' ?>">
                <?= $label ?>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="table-wrap">
            <h3>🗓️ <?= ['upcoming'=>'Upcoming Approved Visits','pending'=>'Pending Visits','all'=>'All Visits'][$filter] ?>
                (<?= count($appointments) ?>)
            </h3>
            <?php if (empty($appointments)): ?>
                <p class="table-empty">No visits in this category.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Donor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Visitors</th>
                        <th>Purpose</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($appointments as $i => $a):
                    $isSunday = date('N', strtotime($a['visit_date'])) == 7;
                ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td>
                            <strong><?= clean($a['donor_name']) ?></strong><br>
                            <small style="color:var(--muted)"><?= clean($a['donor_email']) ?></small>
                        </td>
                        <td>
                            <?= date('D, d M Y', strtotime($a['visit_date'])) ?>
                            <?php if ($isSunday): ?>
                            <span class="badge" style="background:#FFF3E0;color:#E65100;font-size:0.65rem;">☀️ Sunday</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('h:i A', strtotime($a['visit_time'])) ?></td>
                        <td><?= $a['visitors_count'] ?> people</td>
                        <td style="max-width:160px;font-size:0.85rem;"><?= clean($a['purpose']) ?: '—' ?></td>
                        <td><?= statusBadge($a['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <div class="info-box">
            💡 Visit approvals are handled by the NGO coordinator. Contact your NGO to expedite pending visits.
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
