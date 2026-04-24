<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db.php';
require_once 'includes/functions.php';
$pageTitle = 'Welcome';
$result = mysqli_query($conn, "SELECT * FROM orphanages ORDER BY established_year ASC LIMIT 6");
$orphanages = mysqli_fetch_all($result, MYSQLI_ASSOC);
$r1=mysqli_query($conn,"SELECT COUNT(*) as c FROM orphanages"); $s1=mysqli_fetch_assoc($r1);
$r2=mysqli_query($conn,"SELECT COUNT(*) as c FROM donation_requests WHERE status='approved'"); $s2=mysqli_fetch_assoc($r2);
$r3=mysqli_query($conn,"SELECT SUM(current_children) as c FROM orphanages"); $s3=mysqli_fetch_assoc($r3);
$r4=mysqli_query($conn,"SELECT COUNT(DISTINCT taluka) as c FROM orphanages"); $s4=mysqli_fetch_assoc($r4);
include 'includes/header.php';
?>

<style>
.hero-map-badge{display:inline-block;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);border-radius:20px;padding:.3rem .9rem;font-size:.75rem;letter-spacing:.12em;text-transform:uppercase;margin-right:.5rem;margin-bottom:.5rem;}
.card-img-wrap{position:relative;overflow:hidden;height:195px;}
.card-img-wrap img{width:100%;height:195px;object-fit:cover;display:block;transition:transform .4s ease;}
.orphanage-card:hover .card-img-wrap img{transform:scale(1.05);}
.card-taluka-badge{position:absolute;top:.65rem;left:.65rem;background:rgba(45,80,22,.88);color:#fff;font-size:.68rem;font-weight:700;padding:.2rem .6rem;border-radius:20px;}
.card-year-badge{position:absolute;top:.65rem;right:.65rem;background:rgba(176,94,58,.88);color:#fff;font-size:.68rem;padding:.2rem .6rem;border-radius:20px;}
.cap-bar{height:4px;background:var(--sand);border-radius:3px;margin:.45rem 0 .7rem;}
.cap-fill{height:100%;border-radius:3px;background:linear-gradient(90deg,var(--sage),var(--forest));}
.district-strip{background:linear-gradient(135deg,#1a1208 0%,#2d5016 100%);color:rgba(255,255,255,.85);padding:2.5rem 2rem;text-align:center;}
.district-strip h2{color:#fff;font-size:1.6rem;margin-bottom:.5rem;}
.taluka-chips{display:flex;flex-wrap:wrap;gap:.5rem;justify-content:center;margin-top:1rem;}
.taluka-chip{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);color:rgba(255,255,255,.9);border-radius:20px;padding:.28rem .8rem;font-size:.78rem;}
</style>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <div>
            <span class="hero-map-badge">📍 Nanded District</span>
            <span class="hero-map-badge">Maharashtra</span>
        </div>
        <h1>Give Hope to Children of Marathwada</h1>
        <p>Connect with 10 verified orphanages across 8 talukas of Nanded district. Donate with purpose, volunteer your time, and be the change every child deserves.</p>
        <div class="hero-cta">
            <a href="/hope_haven/orphanages.php" class="btn btn-green">Explore All Orphanages</a>
            <?php if(!isLoggedIn()): ?>
            <a href="/hope_haven/auth/register.php" class="btn btn-ghost">Join as Donor</a>
            <?php else: ?>
            <a href="/hope_haven/donor/dashboard.php" class="btn btn-ghost">Go to Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- STATS -->
<div class="stats-strip">
    <div class="stat-item"><div class="stat-num"><?= $s1['c'] ?></div><div class="stat-label">Orphanages</div></div>
    <div class="stat-item"><div class="stat-num"><?= $s4['c'] ?></div><div class="stat-label">Talukas Covered</div></div>
    <div class="stat-item"><div class="stat-num"><?= number_format($s3['c']) ?>+</div><div class="stat-label">Children Supported</div></div>
    <div class="stat-item"><div class="stat-num"><?= $s2['c'] ?></div><div class="stat-label">Donations Fulfilled</div></div>
</div>

<!-- DISTRICT STRIP -->
<div class="district-strip">
    <h2>🗺️ Nanded District – 16 Talukas of Marathwada</h2>
    <p style="font-size:.9rem;margin-bottom:.5rem;">We are building a care network across the entire district. Currently serving:</p>
    <div class="taluka-chips">
        <?php
        $activeTalukas = mysqli_query($conn,"SELECT DISTINCT taluka FROM orphanages ORDER BY taluka");
        while($row=mysqli_fetch_assoc($activeTalukas)) echo "<span class='taluka-chip'>".clean($row['taluka'])."</span>";
        ?>
    </div>
</div>

<!-- FEATURED ORPHANAGES -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Nanded District Homes</span>
            <h2>Orphanages Needing Your Support</h2>
            <p>Real children, real places, real need — from Sagroli to Mahur, from Kinwat to Degloor.</p>
        </div>
        <div class="cards-grid">
        <?php foreach($orphanages as $o):
            $pct=$o['capacity']>0?round(($o['current_children']/$o['capacity'])*100):0;
        ?>
            <div class="orphanage-card" style="display:flex;flex-direction:column;">
                <div class="card-img-wrap">
                    <img src="<?= clean($o['image_url']) ?>" alt="<?= clean($o['name']) ?>"
                         onerror="this.src='https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&q=80'">
                    <span class="card-taluka-badge">📍 <?= clean($o['taluka']) ?></span>
                    <?php if($o['established_year']): ?><span class="card-year-badge">Est. <?= $o['established_year'] ?></span><?php endif; ?>
                </div>
                <div class="card-body" style="flex:1;display:flex;flex-direction:column;">
                    <h3 style="font-size:1rem;margin-bottom:.3rem;"><?= clean($o['name']) ?></h3>
                    <p style="font-size:.79rem;color:var(--muted);margin-bottom:.5rem;">📍 <?= clean($o['location']) ?></p>
                    <p style="font-size:.86rem;color:var(--mid);margin-bottom:.7rem;line-height:1.5;flex:1;"><?= clean(substr($o['description'],0,140)) ?>...</p>
                    <div style="font-size:.76rem;color:var(--muted);margin-bottom:.25rem;display:flex;justify-content:space-between;">
                        <span>🧒 <?= $o['current_children'] ?>/<?= $o['capacity'] ?> children</span><span><?= $pct ?>%</span>
                    </div>
                    <div class="cap-bar"><div class="cap-fill" style="width:<?= $pct ?>%"></div></div>
                    <div class="card-needs" style="font-size:.79rem;margin-bottom:.9rem;"><strong>Needs: </strong><?= clean(substr($o['needs'],0,90)) ?>...</div>
                    <div class="card-footer" style="margin-top:auto;">
                        <?php if(isLoggedIn() && $_SESSION['role']==='donor'): ?>
                        <a href="/hope_haven/donor/donate.php?oid=<?= $o['id'] ?>" class="btn btn-green btn-sm">💝 Donate</a>
                        <a href="/hope_haven/donor/book_appointment.php?oid=<?= $o['id'] ?>" class="btn btn-sky btn-sm">📅 Visit</a>
                        <?php else: ?>
                        <a href="/hope_haven/auth/login.php" class="btn btn-green btn-sm">Login to Help</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:2.5rem;">
            <a href="/hope_haven/orphanages.php" class="btn btn-terra">View All 10 Orphanages →</a>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section" style="background:var(--warm);border-top:1px solid var(--sand);border-bottom:1px solid var(--sand);">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">How It Works</span>
            <h2>Simple Steps to Make a Difference</h2>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:1.5rem;">
        <?php $steps=[['🔑','Register','Create your free account as Donor, NGO or Orphanage Admin.'],['🔍','Discover','Browse all 10 verified orphanages across Nanded district.'],['💝','Donate','Submit a request – food, clothes, money, medicines & more.'],['☀️','Visit','Book a Sunday Caring Connections visit to meet the children.']];
        foreach($steps as $i=>[$icon,$title,$desc]): ?>
            <div style="background:var(--white);border-radius:var(--radius);padding:1.6rem;text-align:center;box-shadow:var(--shadow-sm);border:1px solid var(--sand);">
                <div style="font-size:2.2rem;margin-bottom:.65rem;"><?= $icon ?></div>
                <div style="font-size:.72rem;font-weight:700;color:var(--clay);letter-spacing:.1em;margin-bottom:.35rem;">STEP <?= $i+1 ?></div>
                <h3 style="font-size:1rem;margin-bottom:.45rem;"><?= $title ?></h3>
                <p style="font-size:.86rem;"><?= $desc ?></p>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CARING CONNECTIONS -->
<section class="section">
    <div class="container" style="max-width:780px;">
        <div class="sunday-feature">
            <div class="sunday-icon">☀️</div>
            <div>
                <h3>Caring Connections – Sunday Visits</h3>
                <p>Our flagship programme invites donors and volunteers to visit orphanages every Sunday. Spend quality time with children, bring gifts, or simply share your warmth. All visits across Nanded district are coordinated through our NGO network and require prior approval.</p>
                <a href="<?= isLoggedIn() ? '/hope_haven/donor/book_appointment.php' : '/hope_haven/auth/register.php' ?>" class="btn btn-ghost btn-sm" style="margin-top:1rem;">Schedule a Sunday Visit →</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
