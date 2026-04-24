<?php
// ngo/update_appointment.php – Approve / Reject appointments
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('ngo');

$id     = (int)($_GET['id'] ?? 0);
$action = clean($_GET['action'] ?? '');
$validActions = ['approve' => 'approved', 'reject' => 'rejected', 'pending' => 'pending'];

if (!$id || !array_key_exists($action, $validActions)) {
    redirect('appointments.php', 'Invalid action.', 'error');
}

$newStatus = $validActions[$action];
$note = match($action) {
    'approve' => 'Your visit has been approved. Please arrive on time.',
    'reject'  => 'Sorry, your visit request could not be accommodated at this time.',
    default   => '',
};

$stmt = mysqli_prepare($conn, "UPDATE appointments SET status=?, admin_note=? WHERE id=?");
mysqli_stmt_bind_param($stmt, 'ssi', $newStatus, $note, $id);

if (mysqli_stmt_execute($stmt)) {
    redirect('appointments.php', "Appointment has been $newStatus.", 'success');
} else {
    redirect('appointments.php', 'Update failed. Please try again.', 'error');
}
?>
