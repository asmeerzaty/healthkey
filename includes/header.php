<?php require_once __DIR__.'/bootstrap.php'; $u=user(); $title=$title??'HEALTHKEY'; ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> | HEALTHKEY</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?=BASE_URL?>/assets/css/style.css" rel="stylesheet"></head><body>
<nav class="navbar navbar-expand-lg navbar-dark hk-nav"><div class="container">
<a class="navbar-brand fw-bold" href="<?=BASE_URL?>/"><i class="bi bi-shield-plus"></i> HEALTHKEY</a>
<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#n"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="n"><ul class="navbar-nav ms-auto">
<?php if($u&&$u['role']==='admin'):?><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/admin/">Admin Panel</a></li>
<?php elseif($u):?>
<li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/dashboard.php">Dashboard</a></li>
<li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/health-profile.php">Health Profile</a></li>
<li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/product.php">Bracelet</a></li>
<li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/my-qr.php">My QR</a></li>
<li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/chatbot.php">Chatbot</a></li>
<?php endif; if($u):?><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/logout.php">Logout (<?=e(explode(' ',$u['name'])[0])?>)</a></li>
<?php else:?><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/index.php">Dashboard</a></li><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/login.php">Login</a></li><li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>/register.php">Register</a></li><?php endif;?> 
</ul></div></div></nav><main class="container py-4"><?=flash()?>
