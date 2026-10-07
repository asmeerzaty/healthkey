<?php $title='My QR'; require 'includes/header.php'; require_login();
$qr=qr_of($u['id']);// Ownership: query is keyed on the session user, never on a URL parameter.
if(!$qr){flash('You have no QR yet. Purchase a bracelet or claim a tag.','warning');redirect('product.php');}
$url=scan_url($qr['unique_token']);?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card p-4 text-center"><h3>Your HEALTHKEY QR</h3>
<span class="badge align-self-center mb-3 <?=$qr['status']==='active'?'bg-success':'bg-warning text-dark'?>"><?=e(ucfirst($qr['status']))?></span>
<?php if($qr['status']==='claimed'):?><div class="alert alert-warning">Please complete your health profile before activating your HEALTHKEY. <a href="health-profile.php">Complete now</a></div><?php endif;?>
<div id="qr" class="d-flex justify-content-center mb-3"></div>
<p class="small text-break"><a href="<?=e($url)?>" target="_blank"><?=e($url)?></a></p>
<div class="no-print"><button class="btn btn-primary" onclick="dl()"><i class="bi bi-download"></i> Download</button> <button class="btn btn-outline-primary" onclick="print()"><i class="bi bi-printer"></i> Print</button></div>
<p class="small text-muted mt-3 mb-0">The QR holds only a random link — no medical data. Updating your profile never changes the QR.</p></div></div></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>new QRCode(document.getElementById('qr'),{text:<?=json_encode($url)?>,width:240,height:240,correctLevel:QRCode.CorrectLevel.M});
function dl(){const c=document.querySelector('#qr canvas');const a=document.createElement('a');a.download='healthkey-qr.png';a.href=c.toDataURL('image/png');a.click();}</script>
<?php require 'includes/footer.php';
