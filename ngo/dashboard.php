<?php
// ngo/dashboard.php – NGO Dashboard
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('ngo');

$pageTitle = 'NGO Dashboard';
$user = currentUser();

// Stats
$r1 = mysqli_query($conn, "SELECT COUNT(*) as c FROM donation_requests WHERE status='pending'");
$r2 = mysqli_query($conn, "SELECT COUNT(*) as c FROM donation_requests WHERE status='approved'");
$r3 = mysqli_query($conn, "SELECT COUNT(*) as c FROM appointments WHERE status='pending'");
$r4 = mysqli_query($conn, "SELECT COUNT(*) as c FROM donation_requests");
$s1 = mysqli_fetch_assoc($r1); $s2 = mysqli_fetch_assoc($r2);
$s3 = mysqli_fetch_assoc($r3); $s4 = mysqli_fetch_assoc($r4);

// Recent donation requests
$dResult = mysqli_query($conn,
    "SELECT dr.*, u.name AS donor_name, o.name AS orphanage_name
     FROM donation_requests dr
     JOIN users u ON u.id = dr.donor_id
     JOIN orphanages o ON o.id = dr.orphanage_id
     ORDER BY dr.created_at DESC LIMIT 8"
);
$donations = mysqli_fetch_all($dResult, MYSQLI_ASSOC);

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
            <li><a href="dashboard.php" class="active">🏠 Dashboard</a></li>
            <li><a href="donations.php">💝 All Donations</a></li>
            <li><a href="appointments.php">📅 Appointments</a></li>
            <li><a href="/hope_haven/orphanages.php">🏡 Orphanages</a></li>
            <li><a href="/hope_haven/auth/logout.php" class="logout-link">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <div class="dash-header">
            <h2>NGO Control Centre 🌐</h2>
            <p>Review and manage donation requests and visit appointments across all orphanages.</p>
        </div>

        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div><h3><?= $s1['c'] ?></h3><p>Pending Donations</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div><h3><?= $s2['c'] ?></h3><p>Approved</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div><h3><?= $s3['c'] ?></h3><p>Pending Visits</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div><h3><?= $s4['c'] ?></h3><p>Total Requests</p></div>
            </div>
        </div>

        <!-- Pending Donation Requests -->
        <div class="table-wrap">
            <h3>⏳ Recent Donation Requests
                <a href="donations.php" class="btn btn-sky btn-sm" style="margin-left:auto;">View All</a>
            </h3>
            <?php if (empty($donations)): ?>
                <p class="table-empty">No donation requests yet.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>Donor</th><th>Orphanage</th><th>Type</th><th>Amount</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($donations as $d): ?>
                    <tr>
                        <td><?= clean($d['donor_name']) ?></td>
                        <td><?= clean($d['orphanage_name']) ?></td>
                        <td><?= ucfirst($d['type']) ?></td>
                        <td><?= $d['amount'] ? '₹'.number_format($d['amount'],2) : '—' ?></td>
                        <td><?= statusBadge($d['status']) ?></td>
                        <td>
                            <?php if ($d['status'] === 'pending'): ?>
                            <div class="action-btns">
                                <a href="update_donation.php?id=<?= $d['id'] ?>&action=approve" class="btn btn-green btn-sm">Approve</a>
                                <a href="update_donation.php?id=<?= $d['id'] ?>&action=reject" class="btn btn-danger btn-sm"
                                   data-confirm="Reject this donation request?">Reject</a>
                            </div>
                            <?php else: ?>
                                <span style="color:var(--muted);font-size:0.82rem;">No actions</span>
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
