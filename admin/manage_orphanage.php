<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireRole('admin');
$pageTitle = 'Manage Orphanage';
$user = currentUser();
$uid  = $user['id'];
$error = '';

$oRes = mysqli_query($conn, "SELECT * FROM orphanages WHERE admin_id = $uid LIMIT 1");
$o = mysqli_fetch_assoc($oRes);

$nandedTalukas = ['Nanded','Ardhapur','Bhokar','Mudkhed','Biloli','Naigaon','Degloor','Mukhed','Dharmabad','Umari','Hadgaon','Himayatnagar','Kandhar','Loha','Kinwat','Mahur'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name             = clean($_POST['name']             ?? '');
    $taluka           = clean($_POST['taluka']           ?? '');
    $location         = clean($_POST['location']         ?? '');
    $description      = clean($_POST['description']      ?? '');
    $needs            = clean($_POST['needs']            ?? '');
    $contact          = clean($_POST['contact']          ?? '');
    $email            = clean($_POST['email']            ?? '');
    $website          = clean($_POST['website']          ?? '');
    $capacity         = (int)($_POST['capacity']         ?? 0);
    $current_children = (int)($_POST['current_children'] ?? 0);
    $established_year = (int)($_POST['established_year'] ?? 0);
    $image_url        = clean($_POST['image_url']        ?? '');

    if (empty($name) || empty($location)) {
        $error = 'Name and location are required.';
    } else {
        if ($o) {
            $stmt = mysqli_prepare($conn,
                "UPDATE orphanages SET name=?,taluka=?,location=?,description=?,needs=?,contact=?,email=?,website=?,capacity=?,current_children=?,established_year=?,image_url=? WHERE admin_id=?");
            mysqli_stmt_bind_param($stmt,'ssssssssiiii',$name,$taluka,$location,$description,$needs,$contact,$email,$website,$capacity,$current_children,$established_year,$image_url,$uid); // note: wrong count — fix:
            // rebind properly
            $stmt = mysqli_prepare($conn,
                "UPDATE orphanages SET name=?,taluka=?,location=?,description=?,needs=?,contact=?,email=?,website=?,capacity=?,current_children=?,established_year=?,image_url=? WHERE admin_id=?");
            mysqli_stmt_bind_param($stmt,'ssssssssiiisi',
                $name,$taluka,$location,$description,$needs,$contact,$email,$website,$capacity,$current_children,$established_year,$image_url,$uid);
        } else {
            $stmt = mysqli_prepare($conn,
                "INSERT INTO orphanages (admin_id,name,taluka,location,description,needs,contact,email,website,capacity,current_children,established_year,image_url) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt,'issssssssiiii',
                $uid,$name,$taluka,$location,$description,$needs,$contact,$email,$website,$capacity,$current_children,$established_year,$image_url); // fix count
            $stmt = mysqli_prepare($conn,
                "INSERT INTO orphanages (admin_id,name,taluka,location,description,needs,contact,email,website,capacity,current_children,established_year,image_url) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt,'issssssssiiisi',
                $uid,$name,$taluka,$location,$description,$needs,$contact,$email,$website,$capacity,$current_children,$established_year,$image_url);
        }
        if (mysqli_stmt_execute($stmt)) {
            redirect('dashboard.php', 'Orphanage profile saved!', 'success');
        } else {
            $error = 'Save failed. Please try again. '.mysqli_error($conn);
        }
    }
}
include '../includes/header.php';
?>
<div class="dashboard-layout">
<aside class="sidebar">
    <div class="sidebar-user"><div class="sidebar-avatar">🏠</div><h4><?= clean($user['name']) ?></h4><p>Orphanage Admin</p></div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="manage_orphanage.php" class="active">✏️ Manage Orphanage</a></li>
        <li><a href="donations.php">💝 Donations</a></li>
        <li><a href="appointments.php">📅 Visit Schedule</a></li>
        <li><a href="/hope_haven/auth/logout.php" class="logout-link">🚪 Logout</a></li>
    </ul>
</aside>
<main class="dash-content">
    <div class="dash-header">
        <h2><?= $o ? '✏️ Edit Orphanage Profile' : '🏡 Create Orphanage Profile' ?></h2>
        <p>Keep your profile updated so donors know exactly how to help.</p>
    </div>
    <?php if($error): ?><div class="flash flash-error">✗ <?= $error ?></div><?php endif; ?>
    <div class="form-card" style="max-width:700px;">
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Orphanage Name *</label>
                <input type="text" name="name" required placeholder="e.g. Anand Balgram" value="<?= clean($o['name'] ?? $_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Taluka (Nanded District)</label>
                <select name="taluka">
                    <option value="">-- Select Taluka --</option>
                    <?php foreach($nandedTalukas as $t): $sel=($o['taluka']??'')===$t?'selected':''; ?>
                    <option value="<?= $t ?>" <?= $sel ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Full Address / Location *</label>
            <input type="text" name="location" required placeholder="e.g. Sharadanagar, Sagroli, Biloli Taluka, Nanded – 431731" value="<?= clean($o['location'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>About the Orphanage</label>
            <textarea name="description" style="min-height:110px;" placeholder="Describe your home, history, mission..."><?= clean($o['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>Current Needs</label>
            <textarea name="needs" style="min-height:70px;" placeholder="e.g. Rice, winter clothes, medicines, school bags..."><?= clean($o['needs'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact" placeholder="+91 98765 43210" value="<?= clean($o['contact'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="orphanage@example.com" value="<?= clean($o['email'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Website (optional)</label>
            <input type="url" name="website" placeholder="https://yourorphanage.org" value="<?= clean($o['website'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Cover Image URL</label>
            <input type="url" name="image_url" placeholder="https://images.unsplash.com/..." value="<?= clean($o['image_url'] ?? '') ?>">
            <p class="form-hint">Paste any public image URL. Recommended: Unsplash.com (free, high-quality photos).</p>
            <?php if(!empty($o['image_url'])): ?>
            <img src="<?= clean($o['image_url']) ?>" alt="Preview" style="width:100%;max-height:160px;object-fit:cover;border-radius:8px;margin-top:.5rem;">
            <?php endif; ?>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Total Capacity</label>
                <input type="number" name="capacity" min="0" value="<?= (int)($o['capacity'] ?? 0) ?>">
            </div>
            <div class="form-group">
                <label>Current Children</label>
                <input type="number" name="current_children" min="0" value="<?= (int)($o['current_children'] ?? 0) ?>">
            </div>
            <div class="form-group">
                <label>Year Established</label>
                <input type="number" name="established_year" min="1900" max="2030" placeholder="e.g. 1990" value="<?= (int)($o['established_year'] ?? 0) ?: '' ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-green"><?= $o ? '💾 Save Changes' : '🏡 Create Profile' ?></button>
    </form>
    </div>
</main>
</div>
<?php include '../includes/footer.php'; ?>
