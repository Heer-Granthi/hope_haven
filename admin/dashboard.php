<?php
// admin/dashboard.php – Orphanage Admin Dashboard
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('admin');

$pageTitle = 'Admin Dashboard';
$user = currentUser();
$uid  = $user['id'];

// Get this admin's orphanage
$oRes  = mysqli_query($conn, "SELECT * FROM orphanages WHERE admin_id = $uid LIMIT 1");
$orphanage = mysqli_fetch_assoc($oRes);

if ($orphanage) {
    $oid = $orphanage['id'];
    $r1  = mysqli_query($conn, "SELECT COUNT(*) as c FROM donation_requests WHERE orphanage_id=$oid");
    $r2  = mysqli_query($conn, "SELECT COUNT(*) as c FROM donation_requests WHERE orphanage_id=$oid AND status='pending'");
    $r3  = mysqli_query($conn, "SELECT COUNT(*) as c FROM appointments WHERE orphanage_id=$oid");
    $r4  = mysqli_query($conn, "SELECT COUNT(*) as c FROM appointments WHERE orphanage_id=$oid AND status='pending'");
    $s1  = mysqli_fetch_assoc($r1); $s2 = mysqli_fetch_assoc($r2);
    $s3  = mysqli_fetch_assoc($r3); $s4 = mysqli_fetch_assoc($r4);

    // Recent donations for this orphanage
    $dResult = mysqli_query($conn,
        "SELECT dr.*, u.name AS donor_name
         FROM donation_requests dr JOIN users u ON u.id = dr.donor_id
         WHERE dr.orphanage_id = $oid ORDER BY dr.created_at DESC LIMIT 6"
    );
    $donations = mysqli_fetch_all($dResult, MYSQLI_ASSOC);

    // Upcoming approved visits
    $aResult = mysqli_query($conn,
        "SELECT a.*, u.name AS donor_name
         FROM appointments a JOIN users u ON u.id = a.donor_id
         WHERE a.orphanage_id = $oid AND a.visit_date >= CURDATE()
         ORDER BY a.visit_date ASC LIMIT 6"
    );
    $appointments = mysqli_fetch_all($aResult, MYSQLI_ASSOC);
}

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
            <li><a href="dashboard.php" class="active">🏠 Dashboard</a></li>
            <li><a href="manage_orphanage.php">✏️ Manage Orphanage</a></li>
            <li><a href="donations.php">💝 Donation Requests</a></li>
            <li><a href="appointments.php">📅 Visit Schedule</a></li>
            <li><a href="/hope_haven/auth/logout.php" class="logout-link">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <div class="dash-header">
            <h2>Welcome, <?= clean($user['name']) ?> 🏠</h2>
            <p>Manage your orphanage details, incoming donations, and visit schedule.</p>
        </div>

        <?php if (!$orphanage): ?>
        <!-- No orphanage registered yet -->
        <div class="form-card" style="text-align:center;padding:3rem;">
            <div style="font-size:3rem;margin-bottom:1rem;">🏡</div>
            <h3>No Orphanage Profile Yet</h3>
            <p style="margin-bottom:1.5rem;">You haven't set up your orphanage profile. Create one now to start receiving donations.</p>
            <a href="manage_orphanage.php" class="btn btn-green">+ Create Orphanage Profile</a>
        </div>
        <?php else: ?>

        <!-- Orphanage Profile Quick View -->
        <div style="background:var(--white);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:2rem;border:1px solid var(--sand);box-shadow:var(--shadow-sm);display:flex;gap:1.5rem;align-items:flex-start;flex-wrap:wrap;">
            <div style="font-size:3.5rem;">🏡</div>
            <div style="flex:1;">
                <h3 style="margin-bottom:0.3rem;"><?= clean($orphanage['name']) ?></h3>
                <p style="margin-bottom:0.5rem;">📍 <?= clean($orphanage['location']) ?></p>
                <p style="font-size:0.88rem;margin-bottom:0.5rem;"><strong>Needs:</strong> <?= clean($orphanage['needs']) ?></p>
                <p style="font-size:0.85rem;color:var(--muted);"><?= $orphanage['current_children'] ?>/<?= $orphanage['capacity'] ?> children</p>
            </div>
            <a href="manage_orphanage.php" class="btn btn-sky btn-sm">Edit Profile</a>
        </div>

        <!-- Stats -->
        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-icon">💝</div>
                <div><h3><?= $s1['c'] ?></h3><p>Total Donations</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div><h3><?= $s2['c'] ?></h3><p>Pending</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div><h3><?= $s3['c'] ?></h3><p>Total Visits</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🔔</div>
                <div><h3><?= $s4['c'] ?></h3><p>Pending Visits</p></div>
            </div>
        </div>

        <!-- Recent Donations -->
        <div class="table-wrap">
            <h3>💝 Incoming Donations
                <a href="donations.php" class="btn btn-green btn-sm" style="margin-left:auto;">View All</a>
            </h3>
            <?php if (empty($donations)): ?>
                <p class="table-empty">No donation requests yet for your orphanage.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>Donor</th><th>Type</th><th>Description</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                <?php foreach ($donations as $d): ?>
                    <tr>
                        <td><?= clean($d['donor_name']) ?></td>
                        <td><?= ucfirst($d['type']) ?></td>
                        <td><?= clean($d['description']) ?: '—' ?></td>
                        <td><?= statusBadge($d['status']) ?></td>
                        <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- Upcoming Visits -->
        <div class="table-wrap">
            <h3>📅 Upcoming Visits
                <a href="appointments.php" class="btn btn-sky btn-sm" style="margin-left:auto;">View All</a>
            </h3>
            <?php if (empty($appointments)): ?>
                <p class="table-empty">No upcoming visits scheduled.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>Donor</th><th>Date</th><th>Time</th><th>Visitors</th><th>Purpose</th><th>Status</th></tr>
                </thead>
                <tbody>
                <?php foreach ($appointments as $a):
                    $isSunday = date('N', strtotime($a['visit_date'])) == 7;
                ?>
                    <tr>
                        <td><?= clean($a['donor_name']) ?></td>
                        <td>
                            <?= date('D, d M Y', strtotime($a['visit_date'])) ?>
                            <?php if ($isSunday): ?>
                            <span class="badge" style="background:#FFF3E0;color:#E65100;font-size:0.65rem;">☀️</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('h:i A', strtotime($a['visit_time'])) ?></td>
                        <td><?= $a['visitors_count'] ?></td>
                        <td><?= clean($a['purpose']) ?: '—' ?></td>
                        <td><?= statusBadge($a['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <?php endif; ?>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
