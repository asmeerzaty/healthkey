<?php $title='A key to your health info'; require 'includes/header.php'; ?>
<section class="hero"><div>
<h1>Your health story, <em>one scan</em> away.</h1>
<p class="lead">A bracelet with a QR code that shows paramedics your blood type, allergies and emergency contact — even when you can't speak for yourself.</p>
<?php if($u):?><a class="btn btn-primary btn-lg" href="<?=BASE_URL?>/dashboard.php">Open dashboard</a><?php else:?><a class="btn btn-primary btn-lg me-2" href="<?=BASE_URL?>/register.php">Create your HEALTHKEY</a><a class="btn btn-outline-primary btn-lg" href="<?=BASE_URL?>/login.php">Log in</a><?php endif;?></div>
<div class="band"><span class="pulse"><i class="bi bi-broadcast"></i> Scanned just now</span><div class="band-card">
<div class="d-flex justify-content-between align-items-center"><b class="fs-5">HEALTHKEY</b><span class="badge" style="background:var(--teal);color:var(--ink)">Active</span></div>
<div class="qr"><svg viewBox="0 0 21 21" width="100%" shape-rendering="crispEdges"><path fill="#0A1F33" d="M0 0h7v7H0zM14 0h7v7h-7zM0 14h7v7H0zM1 1v5h5V1zM15 1v5h5V1zM1 15v5h5v-5zM2 2h3v3H2zM16 2h3v3h-3zM2 16h3v3H2zM8 0h2v2H8zM11 1h2v2h-2zM8 4h3v2H8zM12 5h1v3h-1zM0 8h2v2H0zM3 8h3v1H3zM8 8h2v3H8zM11 9h3v2h-3zM15 8h2v2h-2zM18 9h3v2h-3zM8 13h2v2H8zM11 12h2v3h-2zM14 14h3v2h-3zM18 13h3v2h-3zM9 17h3v2H9zM13 17h2v4h-2zM16 18h3v1h-3zM19 16h2v2h-2zM8 20h4v1H8z"/></svg></div>
<div class="small" style="color:#9fb3c8">Blood type · Allergies · Emergency contact</div></div></div></section>
<section class="steps"><?php foreach([['bi-person-vcard','Create your profile','Add the medical details a responder would need.'],['bi-bag-check','Get your bracelet','Your unique QR is revealed once you buy.'],['bi-watch','Wear it daily','Edit your info anytime — the QR never changes.'],['bi-phone-vibrate','Scan in an emergency','No app or login needed to see the essentials.']] as $s):?>
<div class="card step"><i class="bi <?=$s[0]?>"></i><h5><?=$s[1]?></h5><p><?=$s[2]?></p></div><?php endforeach;?></section>
<section class="why"><h3>Paramedics shouldn't have to guess.</h3><p>When help arrives and you can't explain your condition, time is wasted, tests get repeated, or something dangerous gets missed. HEALTHKEY puts the key facts on your wrist.</p></section>
<?php require 'includes/footer.php';
