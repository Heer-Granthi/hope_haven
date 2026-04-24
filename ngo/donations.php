<?php
// ngo/donations.php – NGO: Manage all donation requests
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('ngo');

$pageTitle = 'Manage Donations';
$user = currentUser();

// Filter by status
$filter = clean($_GET['status'] ?? 'all');
$where  = ($filter !== 'all') ? "WHERE dr.status = '$filter'" : '';

$result = mysqli_query($conn,
    "SELECT dr.*, u.name AS donor_name, u.email AS donor_email, o.name AS orphanage_name
     FROM donation_requests dr
     JOIN users u ON u.id = dr.donor_id
     JOIN orphanages o ON o.id = dr.orphanage_id
     $where
     ORDER BY dr.created_at DESC"
);
$donations = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
            <li><a href="donations.php" class="active">💝 All Donations</a></li>
            <li><a href="appointments.php">📅 Appointments</a></li>
            <li><a href="/hope_haven/orphanages.php">🏡 Orphanages</a></li>
            <li><a href="/hope_haven/auth/logout.php" class="logout-link">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <div class="dash-header">
            <h2>💝 Donation Requests</h2>
            <p>Review, approve or reject donation requests from donors.</p>
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
            <h3>📋 <?= ucfirst($filter) ?> Requests (<?= count($donations) ?>)</h3>
            <?php if (empty($donations)): ?>
                <p class="table-empty">No <?= $filter ?> donation requests found.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Donor</th>
                        <th>Orphanage</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
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
                        <td><?= clean($d['orphanage_name']) ?></td>
                        <td><?= ucfirst($d['type']) ?></td>
                        <td><?= $d['amount'] ? '₹'.number_format($d['amount'],2) : '—' ?></td>
                        <td style="max-width:180px;font-size:0.85rem;"><?= clean($d['description']) ?: '—' ?></td>
                        <td><?= statusBadge($d['status']) ?></td>
                        <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
                        <td>
                            <?php if ($d['status'] === 'pending'): ?>
                            <div class="action-btns">
                                <a href="update_donation.php?id=<?= $d['id'] ?>&action=approve"
                                   class="btn btn-green btn-sm">✓ Approve</a>
                                <a href="update_donation.php?id=<?= $d['id'] ?>&action=reject"
                                   class="btn btn-danger btn-sm"
                                   data-confirm="Reject this donation request?">✗ Reject</a>
                            </div>
                            <?php else: ?>
                                <a href="update_donation.php?id=<?= $d['id'] ?>&action=pending"
                                   class="btn btn-sm" style="background:var(--warm);color:var(--mid);"
                                   data-confirm="Reset this request to pending?">Reset</a>
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
