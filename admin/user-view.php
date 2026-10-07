<?php require '_top.php';
$r=q('SELECT * FROM users WHERE id=?',[(int)($_GET['id']??0)])->fetch();if(!$r){echo '<div class="alert alert-danger">User not found.</div>';require '_bottom.php';exit;}
$p=profile_of($r['id']);$qr=qr_of($r['id']);?>
<h3><?=e($r['name'])?></h3><div class="card p-3 mb-3"><b>Email:</b> <?=e($r['email'])?> &nbsp; <b>Phone:</b> <?=e($r['phone'])?> &nbsp; <b>Joined:</b> <?=e($r['created_at'])?> &nbsp; <b>QR:</b> <?=$qr?e($qr['status']):'none'?></div>
<div class="card p-3"><h5>Health Profile</h5><?php if(!$p):?><p class="text-muted">Not completed.</p><?php else:?><dl class="row mb-0"><?php foreach($p as $k=>$v){if(in_array($k,['id','user_id']))continue;echo '<dt class="col-sm-4">'.e(ucwords(str_replace('_',' ',$k))).'</dt><dd class="col-sm-8">'.e($v?:'—').'</dd>';}?></dl><?php endif;?></div>
<?php require '_bottom.php';
