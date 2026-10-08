<?php require 'includes/content.php'; $S=stories();$slug=$_GET['s']??'';
if(!isset($S[$slug])){http_response_code(404);$title='Not found';require 'includes/header.php';echo '<div class="alert alert-warning">Story not found. <a href="stories.php">Back to all stories</a></div>';require 'includes/footer.php';exit;}
$s=$S[$slug];$title=$s['name'].' — '.$s['cond'];require 'includes/header.php';$keys=array_keys($S);$i=array_search($slug,$keys);$next=$keys[($i+1)%count($keys)];?>
<a href="<?=BASE_URL?>/stories.php" class="small fw-semibold text-decoration-none"><i class="bi bi-arrow-left"></i> All stories</a>
<div class="s-hero mt-3" style="background:#<?=$s['c'][3]?>"><div><span class="tag"><?=e($s['cond'])?></span><blockquote>“<?=e($s['quote'])?>”</blockquote><b><?=e($s['name'])?></b> <span class="text-muted">· illustrative scenario</span></div><div class="text-center"><?=person($s['c'][0],$s['c'][1],$s['c'][2],'ffffff',230)?></div></div>
<div class="row g-5 mt-1"><div class="col-lg-7 prose"><?php foreach($s['body'] as $p)echo '<p>'.e($p).'</p>';?>
<div class="card p-4 mt-4" style="background:var(--ink);color:#fff"><h5 class="fw-bold">Set up yours in minutes</h5><p class="mb-3" style="color:#aebfd0">Add your details, get your QR, wear it every day.</p><a class="btn btn-teal align-self-start" href="<?=BASE_URL?>/<?=user()?'product.php':'register.php'?>">Get your HEALTHKEY</a></div>
<a class="d-block mt-4 fw-semibold text-decoration-none" href="<?=BASE_URL?>/story.php?s=<?=e($next)?>">Next story: “<?=e($S[$next]['quote'])?>” <i class="bi bi-arrow-right"></i></a>
<p class="small text-muted mt-3">This is an illustrative scenario written for the HEALTHKEY prototype.</p></div>
<div class="col-lg-5"><div class="card mini"><div class="hd"><i class="bi bi-heart-pulse-fill"></i> What the responder saw</div><dl><dt>Blood type</dt><dd><?=e($s['scan'][0])?></dd><dt>Allergies</dt><dd class="text-danger"><?=e($s['scan'][1])?></dd><dt>Conditions</dt><dd><?=e($s['scan'][2])?></dd><dt>Medication</dt><dd><?=e($s['scan'][3])?></dd><dt>Emergency contact</dt><dd class="mb-0"><?=e($s['scan'][4])?></dd></dl></div></div></div>
<?php require 'includes/footer.php';
