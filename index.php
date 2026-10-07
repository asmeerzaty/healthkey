<?php $title='Home'; require 'includes/header.php'; ?>
<div class="text-center py-5"><h1 class="display-5 fw-bold">A key to your <span style="color:var(--p)">health info</span></h1>
<p class="lead text-muted">A wearable QR bracelet that shows your emergency health information when scanned.</p>
<?php if($u):?><a class="btn btn-primary btn-lg" href="<?=BASE_URL?>/dashboard.php">Go to Dashboard</a><?php else:?><a class="btn btn-primary btn-lg me-2" href="<?=BASE_URL?>/register.php">Get Started</a><a class="btn btn-outline-primary btn-lg" href="<?=BASE_URL?>/login.php">Login</a><?php endif;?></div>
<div class="row g-3 text-center"><?php foreach([['1. Register','Create a profile and enter medical info.'],['2. Purchase','Buy the bracelet — your QR is revealed.'],['3. Wear daily','QR stays the same; info stays updatable.'],['4. Emergency','Responders scan and see key info.']] as $s):?>
<div class="col-md-3"><div class="card p-3 h-100"><h5><?=$s[0]?></h5><p class="text-muted mb-0"><?=$s[1]?></p></div></div><?php endforeach;?></div>
<p class="text-muted small mt-4 text-center">Paramedics arrive but have no clue about your condition or history — HEALTHKEY fixes that.</p>
<?php require 'includes/footer.php';
