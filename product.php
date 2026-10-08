<?php require 'includes/bootstrap.php'; require_once 'includes/content.php'; require_login(); $u=user();
if($u['role']!=='user')redirect('admin/');
$paid=has_paid($u['id']);$list=q('SELECT * FROM bracelets WHERE is_active=1 ORDER BY price,id')->fetchAll();
$bid=(int)($_GET['b']??$_POST['b']??0);$sel=null;foreach($list as $b)if($b['id']==$bid)$sel=$b;
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['pay'])){
 // Simulated checkout. Price/stock come from the DB (never from the browser); stock is decremented atomically.
 if($paid){flash('You already purchased HEALTHKEY.','warning');redirect('my-qr.php');}
 if(!profile_complete($u['id'])){flash('Please complete your health profile before activating your HEALTHKEY.','warning');redirect('health-profile.php');}
 if(!$sel){flash('Please choose a bracelet.','danger');redirect('product.php');}
 $m=in_array($_POST['method']??'',['Demo Card','Demo FPX','Demo E-Wallet'])?$_POST['method']:'Demo Card';
 db()->beginTransaction();
 if(!q('UPDATE bracelets SET stock=stock-1 WHERE id=? AND is_active=1 AND stock>0',[$sel['id']])->rowCount()){db()->rollBack();flash('Sorry, that bracelet just sold out.','danger');redirect('product.php');}
 q("INSERT INTO orders(user_id,bracelet_id,order_number,product_name,amount,payment_method,payment_status,qr_revealed) VALUES(?,?,?,?,?,?, 'paid',1)",[$u['id'],$sel['id'],'HK-'.strtoupper(bin2hex(random_bytes(4))),'HEALTHKEY '.$sel['name'],$sel['price'],$m]);
 db()->commit();assign_qr($u['id']);flash('Payment successful! Your QR code is now revealed.');redirect('my-qr.php');}
$title='Bracelets'; require 'includes/header.php';?>
<div class="row g-4"><div class="col-lg-4"><div class="card p-4 text-center"><div class="qr-wrap"><div class="qr-hidden"></div><?php if(!$paid):?><div class="qr-lock"><span><i class="bi bi-lock-fill fs-1"></i><br>QR CODE HIDDEN</span></div><?php endif;?></div>
<p class="small text-muted mt-3 mb-0"><?=$paid?'Your QR is unlocked.':'Purchase a bracelet to reveal your unique QR code.'?></p><?php if($paid):?><a class="btn btn-teal mt-2" href="my-qr.php">View my QR</a><?php endif;?></div>
<p class="mt-3 small"><a href="claim-qr.php">Already have a physical tag? Claim it</a></p></div>
<div class="col-lg-8"><h2 class="fw-bold">Choose your bracelet</h2><div class="row g-3 mt-1"><?php foreach($list as $b):$on=$sel&&$sel['id']==$b['id'];?>
<div class="col-md-4"><div class="card prod p-3 h-100" style="<?=$on?'outline:3px solid var(--teal)':''?>"><div class="pic" style="background:#<?=e($b['color1'])?>22;padding:.6rem"><?=band($b['color1'],$b['color2'],120)?></div><h6 class="fw-bold mb-0"><?=e($b['name'])?></h6><div class="small text-muted"><?=e($b['material'])?> · <?=e(ucfirst($b['type']))?></div><div class="pr my-2">RM<?=number_format($b['price'],2)?></div>
<?php if($b['stock']<=0):?><button class="btn btn-outline-secondary btn-sm" disabled>Sold out</button><?php elseif($paid):?><button class="btn btn-outline-secondary btn-sm" disabled>Owned</button><?php else:?><a class="btn <?=$on?'btn-teal':'btn-outline-primary'?> btn-sm" href="?b=<?=$b['id']?>"><?=$on?'Selected':'Select'?></a><?php if($b['stock']<=5):?><div class="small text-danger mt-1">Only <?=$b['stock']?> left</div><?php endif;endif;?></div></div><?php endforeach;if(!$list)echo '<p class="text-muted">No bracelets available right now.</p>';?></div>
<?php if($sel&&!$paid&&$sel['stock']>0):?><div class="card p-4 mt-4"><h5>Demo checkout</h5><p class="mb-2"><?=e($sel['name'])?> — <b>RM<?=number_format($sel['price'],2)?></b></p>
<form method="post"><?=csrf()?><input type="hidden" name="pay" value="1"><input type="hidden" name="b" value="<?=$sel['id']?>"><select name="method" class="form-select mb-2"><option>Demo Card</option><option>Demo FPX</option><option>Demo E-Wallet</option></select><button class="btn btn-primary">Confirm &amp; Pay (simulated)</button></form></div><?php endif;?></div></div>
<?php require 'includes/footer.php';
