<?php $title='Dashboard'; require 'includes/header.php'; require_login();
if($u['role']==='admin')redirect('admin/');
$pc=profile_complete($u['id']);$qr=qr_of($u['id']);$o=q('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC LIMIT 1',[$u['id']])->fetch();
$paid=has_paid($u['id']);$last=$qr?q('SELECT MAX(accessed_at) m FROM emergency_access_logs WHERE qr_id=?',[$qr['id']])->fetch()['m']:null;?>
<div class="page-head"><div><h2 class="mb-0">Hi, <?=e(explode(' ',$u['name'])[0])?></h2><span class="text-muted">Here's the state of your HEALTHKEY.</span></div></div><div class="row g-3 mt-1">
<div class="col-md-3"><div class="card tile"><span class="ico"><i class="bi bi-heart-pulse"></i></span><h6>Health Profile</h6><span class="badge <?=$pc?'bg-success':'bg-warning text-dark'?> align-self-start"><?=$pc?'Complete':'Incomplete'?></span><a href="health-profile.php" class="mt-auto pt-2">Edit</a></div></div>
<div class="col-md-3"><div class="card tile"><span class="ico"><i class="bi bi-qr-code"></i></span><h6>HEALTHKEY QR</h6><span class="badge <?=$qr&&$qr['status']==='active'?'bg-success':'bg-secondary'?> align-self-start"><?=$qr?($qr['status']==='active'?'QR Active':ucfirst($qr['status'])):'QR Locked'?></span><a href="<?=$qr?'my-qr.php':'product.php'?>" class="mt-auto pt-2"><?=$qr?'View QR':'Get yours'?></a></div></div>
<div class="col-md-3"><div class="card tile"><span class="ico"><i class="bi bi-bag-check"></i></span><h6>Order</h6><span class="badge bg-info text-dark align-self-start"><?=$o?ucfirst($o['payment_status']):'No order'?></span><a href="product.php" class="mt-auto pt-2">Bracelet page</a></div></div>
<div class="col-md-3"><div class="card tile"><span class="ico"><i class="bi bi-chat-dots"></i></span><h6>Chatbot / Admin</h6><small class="text-muted">General info only</small><a href="chatbot.php" class="mt-auto pt-2">AI assistant</a><a href="support.php" class="pt-0">Contact admin</a></div></div></div>
<?php if(!$qr):?><div class="card p-3 mt-3"><b>Already have a bracelet tag?</b> <a href="claim-qr.php">Claim your QR</a> to declare ownership.</div><?php endif;?>
<?php if($last):?><div class="alert alert-info mt-3">Your QR was last scanned on <?=e($last)?>.</div><?php endif;?>
<?php require 'includes/footer.php';
