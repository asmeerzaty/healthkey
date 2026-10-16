<?php require 'includes/bootstrap.php'; require_login(); $u=user();
$qr=qr_of($u['id']);// Ownership: query is keyed on the session user, never on a URL parameter.
if(!$qr){flash('You have no QR yet. Purchase a bracelet or claim a tag.','warning');redirect('product.php');}
if($_SERVER['REQUEST_METHOD']==='POST'){$a=$_POST['act']??'';
 if($a==='pause'&&$qr['status']==='active'){q("UPDATE qr_codes SET status='paused' WHERE id=? AND user_id=?",[$qr['id'],$u['id']]);flash('QR paused. Anyone scanning it now sees it as inactive.');}
 elseif($a==='resume'&&$qr['status']==='paused'){q("UPDATE qr_codes SET status='claimed' WHERE id=? AND user_id=?",[$qr['id'],$u['id']]);sync_qr($u['id']);flash('QR resumed.');}
 elseif($a==='rotate'&&in_array($qr['status'],['active','claimed','paused'],true)){q('UPDATE qr_codes SET unique_token=? WHERE id=? AND user_id=?',[new_token(),$qr['id'],$u['id']]);flash('New QR created. The old one stopped working immediately. Download and print the new one.');}
 redirect('my-qr.php');}
$url=scan_url($qr['unique_token']);$st=$qr['status'];
$hist=q('SELECT accessed_at,user_agent FROM emergency_access_logs WHERE qr_id=? ORDER BY id DESC LIMIT 8',[$qr['id']])->fetchAll();
$total=q('SELECT COUNT(*) c FROM emergency_access_logs WHERE qr_id=?',[$qr['id']])->fetch()['c'];
$title='My QR';require 'includes/header.php';?>
<div class="row g-4 justify-content-center"><div class="col-lg-5"><div class="card p-4 text-center"><h3 class="mb-2">Your HEALTHKEY QR</h3>
<span class="badge align-self-center mb-3 <?=$st==='active'?'bg-success':($st==='paused'?'bg-secondary':'bg-warning text-dark')?>"><?=e(ucfirst($st))?></span>
<?php if($st==='claimed'):?><div class="alert alert-warning">Please complete your health profile before activating your HEALTHKEY. <a href="health-profile.php">Complete now</a></div><?php endif;?>
<?php if($st==='paused'):?><div class="alert alert-secondary">This QR is paused. Scans show it as inactive until you resume it.</div><?php endif;?>
<div id="qr" class="d-flex justify-content-center mb-3" style="<?=$st==='paused'?'opacity:.35':''?>"></div>
<p class="small text-break"><a href="<?=e($url)?>" target="_blank"><?=e($url)?></a></p>
<div class="no-print"><button class="btn btn-primary" onclick="dl()"><i class="bi bi-download"></i> Download</button> <button class="btn btn-outline-primary" onclick="print()"><i class="bi bi-printer"></i> Print</button></div>
<p class="small text-muted mt-3 mb-0">The QR holds only a random link, no medical data. Updating your profile never changes it.</p></div>
<?php if(in_array($st,['active','claimed','paused'],true)):?><div class="danger-zone mt-3 no-print"><h6 class="fw-bold">Lost your bracelet?</h6><p class="small text-muted">Pause the QR while you look for it, or replace it for good. Replacing makes the old printed QR stop working at once.</p>
<div class="d-flex gap-2 flex-wrap"><?php if($st==='active'):?><form method="post"><?=csrf()?><input type="hidden" name="act" value="pause"><button class="btn btn-outline-primary btn-sm"><i class="bi bi-pause-circle"></i> Pause QR</button></form><?php elseif($st==='paused'):?><form method="post"><?=csrf()?><input type="hidden" name="act" value="resume"><button class="btn btn-teal btn-sm"><i class="bi bi-play-circle"></i> Resume QR</button></form><?php endif;?>
<form method="post" onsubmit="return confirm('Replace your QR? The old printed QR will stop working immediately.')"><?=csrf()?><input type="hidden" name="act" value="rotate"><button class="btn btn-outline-danger btn-sm"><i class="bi bi-arrow-repeat"></i> Replace QR</button></form></div></div><?php endif;?></div>
<div class="col-lg-5"><div class="card p-4"><div class="d-flex gap-3 align-items-start"><span class="tile"><span class="ico mb-0"><i class="bi bi-broadcast"></i></span></span><div><h5 class="fw-bold mb-1">Works with no signal too</h5><p class="small text-muted mb-2">Generate a short text of your essentials and write it to an NFC tag on your bracelet.</p><a class="btn btn-teal btn-sm" href="nfc.php">Make my NFC text</a></div></div></div>
<div class="card p-4 mt-3"><div class="d-flex justify-content-between align-items-center mb-2"><h5 class="fw-bold mb-0">Scan history</h5><span class="small text-muted"><?=$total?> total</span></div>
<?php if($hist):?><ul class="hist"><?php foreach($hist as $h):?><li><span><i class="bi bi-qr-code-scan text-muted"></i> <?=e(date('j M Y, g:i A',strtotime($h['accessed_at'])))?></span><span class="text-muted"><?=e(ua_label($h['user_agent']))?></span></li><?php endforeach;?></ul><?php else:?><p class="text-muted mb-0">No one has scanned your QR yet. That is good news.</p><?php endif;?></div></div></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>new QRCode(document.getElementById('qr'),{text:<?=json_encode($url)?>,width:240,height:240,correctLevel:QRCode.CorrectLevel.M});
function dl(){const c=document.querySelector('#qr canvas');const a=document.createElement('a');a.download='healthkey-qr.png';a.href=c.toDataURL('image/png');a.click();}</script>
<?php require 'includes/footer.php';
