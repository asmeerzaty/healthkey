<?php $title='Claim QR'; require 'includes/header.php'; require_login();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $t=strtolower(trim($_POST['token']??''));if(preg_match('~token=([a-f0-9]{64})~',$t,$m))$t=$m[1]; // accept pasted full URL
 $r=preg_match('/^[a-f0-9]{64}$/',$t)?q('SELECT * FROM qr_codes WHERE unique_token=?',[$t])->fetch():null;
 if(!$r)flash('This HEALTHKEY QR code is invalid.','danger');
 elseif($r['user_id'])flash($r['user_id']==$u['id']?'You already own this QR.':'This QR has already been claimed by another account.','danger');
 elseif($r['status']==='disabled')flash('This HEALTHKEY QR code is currently inactive.','danger');
 elseif(qr_of($u['id']))flash('Your account already has a QR.','warning');
 else{ // atomic claim: only succeeds if still unowned
  $n=q("UPDATE qr_codes SET user_id=?,status='claimed' WHERE id=? AND user_id IS NULL",[$u['id'],$r['id']])->rowCount();
  if($n){sync_qr($u['id']);flash('QR claimed! It becomes active once your health profile is complete.');redirect('my-qr.php');}
  flash('Claim failed. Please try again.','danger');}
}?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card p-4"><h3>Claim your QR</h3>
<p class="text-muted">Enter the code printed on your tag (or paste the URL it opens) to declare ownership.</p>
<form method="post"><?=csrf()?><input name="token" class="form-control mb-3" placeholder="64-character token or scan URL" required><button class="btn btn-primary">Claim</button></form></div></div></div>
<?php require 'includes/footer.php';
