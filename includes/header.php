<?php require_once __DIR__.'/bootstrap.php'; $u=user(); $title=$title??'HEALTHKEY'; ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> | HEALTHKEY</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?=BASE_URL?>/assets/css/style.css" rel="stylesheet"></head><body>
<?php if(empty($bare)):?><nav class="navbar navbar-expand-lg navbar-dark hk-nav"><div class="container">
<a class="navbar-brand text-white" href="<?=BASE_URL?>/"><span class="brand-dot"><i class="bi bi-qr-code-scan"></i></span>HEALTHKEY</a>
<button class="navbar-toggler border-0" data-bs-toggle="collapse" data-bs-target="#n"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="n"><ul class="navbar-nav ms-auto align-items-lg-center">
<?php if($u&&in_array($u['role'],['admin','superadmin'])):?><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/admin/"><?=$u['role']==='superadmin'?'Owner panel':'Admin panel'?> <span class="hk-badge" data-b="all" style="display:none">0</span></a></li>
<?php elseif($u):?>
<li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/dashboard.php">Dashboard</a></li>
<li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/health-profile.php">Health profile</a></li>
<li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/appointments.php">Appointments</a></li><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/product.php">Bracelet</a></li>
<li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/my-qr.php">My QR</a></li>
<--<li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/stories.php">Stories</a></li>--><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/chatbot.php">Assistant</a></li><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/support.php">Support <span class="hk-badge" style="display:none">0</span></a></li>
<?php endif; if($u):?><?php if($u['role']==='user'):?><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/profile.php"><i class="bi bi-person-circle"></i> <?=e(explode(' ',$u['name'])[0])?></a></li><?php endif;?><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
<?php else:?><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/#how">How it works</a></li><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/#bracelets">Bracelets</a></li><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/stories.php">Stories</a></li><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/login.php">Log in</a></li><li class="nav-item"><a class="nav-link nav-cta" href="<?=BASE_URL?>/register.php">Get started</a></li><?php endif;?>
</ul></div></div></nav><?php endif;?><main class="container py-4 py-lg-5" id="top"><?=flash()?>
