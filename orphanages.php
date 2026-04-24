<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
$pageTitle = 'Orphanages – Nanded District';
$filterTaluka = clean($_GET['taluka'] ?? 'all');
if ($filterTaluka !== 'all') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM orphanages WHERE taluka = ? ORDER BY name ASC");
    mysqli_stmt_bind_param($stmt, 's', $filterTaluka);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT * FROM orphanages ORDER BY taluka, name ASC");
}
$orphanages = mysqli_fetch_all($result, MYSQLI_ASSOC);
$tResult = mysqli_query($conn, "SELECT DISTINCT taluka FROM orphanages ORDER BY taluka");
$talukas = mysqli_fetch_all($tResult, MYSQLI_ASSOC);
$r1 = mysqli_query($conn,"SELECT COUNT(*) as c FROM orphanages"); $s1=mysqli_fetch_assoc($r1);
$r2 = mysqli_query($conn,"SELECT SUM(current_children) as c FROM orphanages"); $s2=mysqli_fetch_assoc($r2);
include 'includes/header.php';
?>
<style>
.card-img-wrap{position:relative;overflow:hidden;height:210px;}
.card-img-wrap img{width:100%;height:210px;object-fit:cover;transition:transform 0.4s ease;display:block;}
.orphanage-card:hover .card-img-wrap img{transform:scale(1.06);}
.card-taluka-badge{position:absolute;top:.7rem;left:.7rem;background:rgba(45,80,22,.88);color:#fff;font-size:.7rem;font-weight:700;padding:.22rem .65rem;border-radius:20px;}
.card-year-badge{position:absolute;top:.7rem;right:.7rem;background:rgba(176,94,58,.88);color:#fff;font-size:.7rem;font-weight:600;padding:.22rem .65rem;border-radius:20px;}
.cap-bar{height:5px;background:var(--sand);border-radius:3px;margin:.5rem 0;}
.cap-fill{height:100%;border-radius:3px;background:linear-gradient(90deg,var(--sage),var(--forest));}
.filter-bar{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:2rem;align-items:center;}
.filter-btn{padding:.38rem .95rem;border-radius:20px;border:1.5px solid var(--sand);background:var(--white);color:var(--mid);font-size:.82rem;font-weight:500;text-decoration:none;transition:all .2s;}
.filter-btn:hover,.filter-btn.active{background:var(--forest);color:#fff;border-color:var(--forest);}
.stat-pills{display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:2rem;}
.stat-pill{display:flex;align-items:center;gap:.55rem;background:var(--white);border:1px solid var(--sand);border-radius:30px;padding:.5rem 1.1rem;box-shadow:var(--shadow-sm);}
.stat-pill strong{font-family:'Playfair Display',serif;font-size:1.25rem;color:var(--terracotta);}
.stat-pill span{font-size:.8rem;color:var(--muted);}
</style>

<div class="page-banner">
    <h1>Orphanages of Nanded District</h1>
    <p>10 registered children's homes across 8 talukas of Marathwada, Maharashtra</p>
</div>

<section class="section">
<div class="container">

    <div class="stat-pills">
        <div class="stat-pill"><strong><?= $s1['c'] ?></strong><span>Homes Registered</span></div>
        <div class="stat-pill"><strong><?= number_format($s2['c']) ?>+</strong><span>Children Sheltered</span></div>
        <div class="stat-pill"><strong>8</strong><span>Talukas Covered</span></div>
        <div class="stat-pill"><strong>65+</strong><span>Yrs Combined Service</span></div>
    </div>

    <div class="filter-bar">
        <span style="font-weight:600;color:var(--mid);font-size:.88rem;">Filter by Taluka:</span>
        <a href="?taluka=all" class="filter-btn <?= $filterTaluka==='all'?'active':'' ?>">All</a>
        <?php foreach($talukas as $t): ?>
        <a href="?taluka=<?= urlencode($t['taluka']) ?>" class="filter-btn <?= $filterTaluka===$t['taluka']?'active':'' ?>"><?= clean($t['taluka']) ?></a>
        <?php endforeach; ?>
    </div>

    <?php if(empty($orphanages)): ?>
        <p class="table-empty">No orphanages found for this taluka.</p>
    <?php else: ?>
    <div class="cards-grid">
    <?php foreach($orphanages as $o):
        $pct = $o['capacity']>0 ? round(($o['current_children']/$o['capacity'])*100) : 0;
    ?>
        <div class="orphanage-card" style="display:flex;flex-direction:column;">
            <div class="card-img-wrap">
                <img src="<?= clean($o['image_url']) ?>" alt="<?= clean($o['name']) ?>"
                     onerror="this.src='https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&q=80'">
                <span class="card-taluka-badge">📍 <?= clean($o['taluka']) ?> Taluka</span>
                <?php if($o['established_year']): ?>
                <span class="card-year-badge">Est. <?= $o['established_year'] ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body" style="flex:1;display:flex;flex-direction:column;">
                <h3 style="font-size:1.02rem;margin-bottom:.3rem;"><?= clean($o['name']) ?></h3>
                <p style="font-size:.8rem;color:var(--muted);margin-bottom:.5rem;">📍 <?= clean($o['location']) ?></p>
                <?php if($o['website']): ?>
                <a href="<?= clean($o['website']) ?>" target="_blank" style="font-size:.78rem;color:var(--sky);margin-bottom:.5rem;display:inline-block;">🌐 Official Website ↗</a>
                <?php endif; ?>
                <p style="font-size:.87rem;color:var(--mid);line-height:1.55;margin-bottom:.75rem;flex:1;"><?= clean(substr($o['description'],0,165)) ?>...</p>
                <div style="margin-bottom:.8rem;">
                    <div style="display:flex;justify-content:space-between;font-size:.76rem;color:var(--muted);margin-bottom:.25rem;">
                        <span>🧒 <?= $o['current_children'] ?> / <?= $o['capacity'] ?> children</span>
                        <span><?= $pct ?>% capacity</span>
                    </div>
                    <div class="cap-bar"><div class="cap-fill" style="width:<?= $pct ?>%"></div></div>
                </div>
                <div class="card-needs" style="margin-bottom:.8rem;font-size:.8rem;"><strong>Needs: </strong><?= clean($o['needs']) ?></div>
                <div style="font-size:.78rem;color:var(--muted);margin-bottom:.9rem;">
                    📞 <?= clean($o['contact']) ?> &nbsp;|&nbsp; ✉️ <?= clean($o['email']) ?>
                </div>
                <div class="card-footer" style="margin-top:auto;">
                    <?php if(isLoggedIn() && $_SESSION['role']==='donor'): ?>
                    <a href="/hope_haven/donor/donate.php?oid=<?= $o['id'] ?>" class="btn btn-green btn-sm">💝 Donate</a>
                    <a href="/hope_haven/donor/book_appointment.php?oid=<?= $o['id'] ?>" class="btn btn-sky btn-sm">📅 Visit</a>
                    <?php else: ?>
                    <a href="/hope_haven/auth/login.php" class="btn btn-green btn-sm">Login to Donate</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="info-box" style="margin-top:3rem;max-width:700px;">
        <strong>About Nanded District:</strong> Nanded is a district in the Marathwada region of Maharashtra with 16 talukas and a population of over 33 lakh. The district, home to the sacred Hazur Sahib Gurudwara (one of the five Sikh Takhts), borders Telangana and Karnataka. Recurring agricultural distress in Marathwada makes children's welfare homes here critically important lifelines.
    </div>
</div>
</section>
<?php include 'includes/footer.php'; ?>
