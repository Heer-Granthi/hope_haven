<?php
// ngo/appointments.php – NGO: Manage all visit appointments
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('ngo');

$pageTitle = 'Manage Appointments';
$user = currentUser();

$filter = clean($_GET['status'] ?? 'all');
$where  = ($filter !== 'all') ? "WHERE a.status = '$filter'" : '';

$result = mysqli_query($conn,
    "SELECT a.*, u.name AS donor_name, u.email AS donor_email, o.name AS orphanage_name
     FROM appointments a
     JOIN users u ON u.id = a.donor_id
     JOIN orphanages o ON o.id = a.orphanage_id
     $where
     ORDER BY a.visit_date ASC"
);
$appointments = mysqli_fetch_all($result, MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-user">
            <div class="sidebar-avatar">🌐</div>
            <h4><?= clean($user['name']) ?></h4>
            <p>NGO Account</p>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">🏠 Dashboard</a></li>
            <li><a href="donations.php">💝 All Donations</a></li>
            <li><a href="appointments.php" class="active">📅 Appointments</a></li>
            <li><a href="/hope_haven/orphanages.php">🏡 Orphanages</a></li>
            <li><a href="/hope_haven/auth/logout.php" class="logout-link">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <div class="dash-header">
            <h2>📅 Visit Appointments</h2>
            <p>Review and manage all orphanage visit bookings including Sunday Caring Connections.</p>
        </div>

        <!-- Sunday Caring Connections summary -->
        <div class="sunday-feature" style="margin-bottom:2rem;">
            <div class="sunday-icon">☀️</div>
            <div>
                <h3>Caring Connections – Weekly Sunday Visits</h3>
                <p>Approve Sunday visit requests to enable donors to spend quality time with children. Pending requests appear below.</p>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div style="display:flex;gap:0.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
            <?php foreach (['all','pending','approved','rejected'] as $s): ?>
            <a href="?status=<?= $s ?>"
               class="btn btn-sm <?= $filter===$s ? 'btn-sky' : '' ?>"
               style="<?= $filter!==$s ? 'background:var(--warm);color:var(--mid);' : '' ?>">
                <?= ucfirst($s) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="table-wrap">
            <h3>🗓️ <?= ucfirst($filter) ?> Visits (<?= count($appointments) ?>)</h3>
            <?php if (empty($appointments)): ?>
                <p class="table-empty">No <?= $filter ?> appointments found.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Donor</th>
                        <th>Orphanage</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Visitors</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Actions</th>
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
                        <td><?= clean($a['orphanage_name']) ?></td>
                        <td>
                            <?= date('D, d M Y', strtotime($a['visit_date'])) ?>
                            <?php if ($isSunday): ?>
                            <span class="badge" style="background:#FFF3E0;color:#E65100;font-size:0.65rem;">☀️ Sunday</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('h:i A', strtotime($a['visit_time'])) ?></td>
                        <td><?= $a['visitors_count'] ?> people</td>
                        <td style="max-width:150px;font-size:0.85rem;"><?= clean($a['purpose']) ?: '—' ?></td>
                        <td><?= statusBadge($a['status']) ?></td>
                        <td>
                            <?php if ($a['status'] === 'pending'): ?>
                            <div class="action-btns">
                                <a href="update_appointment.php?id=<?= $a['id'] ?>&action=approve"
                                   class="btn btn-green btn-sm">✓</a>
                                <a href="update_appointment.php?id=<?= $a['id'] ?>&action=reject"
                                   class="btn btn-danger btn-sm"
                                   data-confirm="Reject this visit?">✗</a>
                            </div>
                            <?php else: ?>
                                <a href="update_appointment.php?id=<?= $a['id'] ?>&action=pending"
                                   class="btn btn-sm" style="background:var(--warm);color:var(--mid);"
                                   data-confirm="Reset to pending?">Reset</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
