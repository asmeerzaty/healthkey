<?php $title='Bracelet'; require 'includes/header.php'; require_login();
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['pay'])){
 // Simulated checkout: no real payment gateway. Mark paid, reveal QR, assign a tag to this user.
 if(has_paid($u['id'])){flash('You already purchased HEALTHKEY.','warning');redirect('my-qr.php');}
 if(!profile_complete($u['id'])){flash('Please complete your health profile before activating your HEALTHKEY.','warning');redirect('health-profile.php');}
 $m=in_array($_POST['method']??'',['Demo Card','Demo FPX','Demo E-Wallet'])?$_POST['method']:'Demo Card';
 q("INSERT INTO orders(user_id,order_number,product_name,amount,payment_method,payment_status,qr_revealed) VALUES(?,?,?,?,?,'paid',1)",[$u['id'],'HK-'.strtoupper(bin2hex(random_bytes(4))),PRODUCT_NAME,PRODUCT_PRICE,$m]);
 assign_qr($u['id']);flash('Payment successful! Your QR code is now revealed.');redirect('my-qr.php');}
$paid=has_paid($u['id']);$checkout=isset($_GET['checkout']);?>
<div class="row g-4 align-items-center"><div class="col-md-5"><div class="qr-wrap"><div class="qr-hidden"></div>
<?php if(!$paid):?><div class="qr-lock"><span><i class="bi bi-lock-fill fs-1"></i><br>QR CODE HIDDEN</span></div><?php endif;?></div></div>
<div class="col-md-7"><h2><?=e(PRODUCT_NAME)?></h2><p class="text-muted">A wearable QR bracelet that provides access to important emergency health information. Silicone or stainless steel, low cost, always updatable.</p>
<h3 class="text-primary">RM<?=number_format(PRODUCT_PRICE,2)?></h3>
<?php if($paid):?><a class="btn btn-teal" href="my-qr.php">View my QR</a>
<?php elseif(!$checkout):?><p class="small text-muted">Purchase HEALTHKEY to reveal your unique QR code.</p><a class="btn btn-primary btn-lg" href="?checkout=1">BUY NOW</a>
<?php else:?><div class="card p-3"><h5>Demo checkout</h5><p class="mb-2">Total: <b>RM<?=number_format(PRODUCT_PRICE,2)?></b></p>
<form method="post"><?=csrf()?><input type="hidden" name="pay" value="1"><select name="method" class="form-select mb-2"><option>Demo Card</option><option>Demo FPX</option><option>Demo E-Wallet</option></select>
<button class="btn btn-primary">Confirm &amp; Pay (simulated)</button></form></div><?php endif;?>
<p class="mt-3 small"><a href="claim-qr.php">Already have a physical tag? Claim it</a></p></div></div>
<?php require 'includes/footer.php';
