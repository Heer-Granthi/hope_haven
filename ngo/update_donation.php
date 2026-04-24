<?php
// ngo/update_donation.php – Approve / Reject donation requests
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('ngo');

$id     = (int)($_GET['id'] ?? 0);
$action = clean($_GET['action'] ?? '');
$validActions = ['approve' => 'approved', 'reject' => 'rejected', 'pending' => 'pending'];

if (!$id || !array_key_exists($action, $validActions)) {
    redirect('donations.php', 'Invalid action.', 'error');
}

$newStatus = $validActions[$action];
$note      = match($action) {
    'approve' => 'Your donation request has been approved. Our coordinator will contact you.',
    'reject'  => 'Unfortunately, this request could not be fulfilled at this time.',
    default   => '',
};

$stmt = mysqli_prepare($conn, "UPDATE donation_requests SET status=?, ngo_note=? WHERE id=?");
mysqli_stmt_bind_param($stmt, 'ssi', $newStatus, $note, $id);

if (mysqli_stmt_execute($stmt)) {
    redirect('donations.php', "Donation request has been $newStatus.", 'success');
} else {
    redirect('donations.php', 'Update failed. Please try again.', 'error');
}
?>
