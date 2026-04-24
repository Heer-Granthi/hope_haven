<?php
// donor/my_donations.php – View all my donation requests
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('donor');

$pageTitle = 'My Donations';
$user = currentUser();
$uid  = $user['id'];

$result = mysqli_query($conn,
    "SELECT dr.*, o.name AS orphanage_name, o.location
     FROM donation_requests dr
     JOIN orphanages o ON o.id = dr.orphanage_id
     WHERE dr.donor_id = $uid
     ORDER BY dr.created_at DESC"
);
$donations = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
            <li><a href="my_donations.php" class="active">📋 My Donations</a></li>
            <li><a href="book_appointment.php">📅 Book Visit</a></li>
            <li><a href="my_appointments.php">🗓️ My Visits</a></li>
            <li><a href="/hope_haven/orphanages.php">🏡 Orphanages</a></li>
            <li><a href="/hope_haven/auth/logout.php" class="logout-link">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <div class="dash-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <h2>📋 My Donations</h2>
                <p>Track the status of all your donation requests.</p>
            </div>
            <a href="donate.php" class="btn btn-green">+ New Donation</a>
        </div>

        <div class="table-wrap">
            <?php if (empty($donations)): ?>
                <p class="table-empty">You haven't made any donations yet.<br><a href="donate.php">Start donating →</a></p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Orphanage</th>
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
                            <strong><?= clean($d['orphanage_name']) ?></strong><br>
                            <small style="color:var(--muted)"><?= clean($d['location']) ?></small>
                        </td>
                        <td><?= ucfirst($d['type']) ?></td>
                        <td><?= $d['amount'] ? '₹'.number_format($d['amount'],2) : '—' ?></td>
                        <td style="max-width:200px;"><?= clean($d['description']) ?: '—' ?></td>
                        <td><?= statusBadge($d['status']) ?></td>
                        <td style="max-width:160px;font-size:0.83rem;"><?= clean($d['ngo_note']) ?: '—' ?></td>
                        <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
