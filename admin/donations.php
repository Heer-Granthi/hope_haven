<?php
// admin/donations.php – Admin: View donations for their orphanage
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('admin');

$pageTitle = 'Donation Requests';
$user = currentUser();
$uid  = $user['id'];

// Get admin's orphanage
$oRes = mysqli_query($conn, "SELECT id, name FROM orphanages WHERE admin_id = $uid LIMIT 1");
$orphanage = mysqli_fetch_assoc($oRes);

if (!$orphanage) {
    redirect('manage_orphanage.php', 'Please set up your orphanage profile first.', 'info');
}

$oid    = $orphanage['id'];
$filter = clean($_GET['status'] ?? 'all');
$where  = ($filter !== 'all') ? "AND dr.status = '$filter'" : '';

$result = mysqli_query($conn,
    "SELECT dr.*, u.name AS donor_name, u.email AS donor_email
     FROM donation_requests dr
     JOIN users u ON u.id = dr.donor_id
     WHERE dr.orphanage_id = $oid $where
     ORDER BY dr.created_at DESC"
);
$donations = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
            <li><a href="donations.php" class="active">💝 Donation Requests</a></li>
            <li><a href="appointments.php">📅 Visit Schedule</a></li>
            <li><a href="/hope_haven/auth/logout.php" class="logout-link">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <div class="dash-header">
            <h2>💝 Donation Requests</h2>
            <p>Incoming donations for <strong><?= clean($orphanage['name']) ?></strong></p>
        </div>

        <!-- Filter Tabs -->
        <div style="display:flex;gap:0.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
            <?php foreach (['all','pending','approved','rejected'] as $s): ?>
            <a href="?status=<?= $s ?>"
               class="btn btn-sm <?= $filter===$s ? 'btn-green' : '' ?>"
               style="<?= $filter!==$s ? 'background:var(--warm);color:var(--mid);' : '' ?>">
                <?= ucfirst($s) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="table-wrap">
            <h3>📋 <?= ucfirst($filter) ?> (<?= count($donations) ?>)</h3>
            <?php if (empty($donations)): ?>
                <p class="table-empty">No <?= $filter !== 'all' ? $filter : '' ?> donation requests yet.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Donor</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>NGO Note</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($donations as $i => $d): ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td>
                            <strong><?= clean($d['donor_name']) ?></strong><br>
                            <small style="color:var(--muted)"><?= clean($d['donor_email']) ?></small>
                        </td>
                        <td><?= ucfirst($d['type']) ?></td>
                        <td><?= $d['amount'] ? '₹'.number_format($d['amount'],2) : '—' ?></td>
                        <td style="max-width:200px;font-size:0.85rem;"><?= clean($d['description']) ?: '—' ?></td>
                        <td><?= statusBadge($d['status']) ?></td>
                        <td style="font-size:0.83rem;"><?= clean($d['ngo_note']) ?: '—' ?></td>
                        <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <div class="info-box">
            💡 Donation request statuses are managed by the NGO. Contact your NGO coordinator to update any requests.
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
