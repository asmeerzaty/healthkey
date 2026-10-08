<?php require 'includes/bootstrap.php'; // PUBLIC emergency page — no login needed
$t=strtolower($_GET['token']??'');$err=null;$r=null;
if(!preg_match('/^[a-f0-9]{64}$/',$t))$err='This HEALTHKEY QR code is invalid.';
else{$r=q('SELECT * FROM qr_codes WHERE unique_token=?',[$t])->fetch();
 if(!$r||!$r['user_id'])$err='This HEALTHKEY QR code is invalid.';
 elseif($r['status']!=='active')$err='This HEALTHKEY QR code is currently inactive.';}
$p=null;
if(!$err){$p=q('SELECT u.name,p.* FROM patient_profiles p JOIN users u ON u.id=p.user_id WHERE p.user_id=? AND u.is_active=1',[$r['user_id']])->fetch();
 if(!$p)$err='This HEALTHKEY QR code is currently inactive.';
 else q('INSERT INTO emergency_access_logs(qr_id,ip_address,user_agent) VALUES(?,?,?)',[$r['id'],$_SERVER['REMOTE_ADDR']??'',substr($_SERVER['HTTP_USER_AGENT']??'',0,255)]);}
$title='Emergency Info'; require 'includes/header.php';
if($err):?><div class="alert alert-danger text-center fs-5 mx-auto" style="max-width:560px"><?=e($err)?></div>
<?php else:function fact($l,$v,$w=false){echo "<div class='col-md-6'><div class='fact".($w?' warn':'')."'><div class='k'>$l</div><div class='v'>".e($v?:'None listed')."</div></div></div>";}?>
<div class="card sos" style="max-width:760px;margin:auto"><div class="sos-head"><small><i class="bi bi-heart-pulse-fill"></i> HEALTHKEY emergency information</small><h2>Medical ID</h2></div>
<div class="p-4"><div class="d-flex align-items-center gap-3 mb-4"><span class="blood"><?=e($p['blood_type'])?></span><div><div class="text-muted small fw-semibold">Patient</div><div class="sos-name"><?=e($p['name'])?></div><?php if($p['date_of_birth']):?><div class="text-muted">Born <?=e($p['date_of_birth'])?></div><?php endif;?></div></div>
<div class="row g-3"><?php fact('Allergies',$p['allergies'],true);fact('Medical conditions',$p['medical_conditions'],true);fact('Current medication',$p['medications']);fact('Doctor',$p['doctor_name']);if($p['share_notes'])fact('Notes',$p['additional_notes']);?></div>
<div class="mt-4 p-3 rounded-4" style="background:var(--ink);color:#fff"><div class="small" style="color:#9fb3c8">Emergency contact</div><div class="fs-5 fw-semibold mb-2"><?=e($p['emergency_contact_name'])?> <span class="fw-normal" style="color:#9fb3c8">(<?=e($p['emergency_contact_relationship'])?>)</span></div>
<a class="btn btn-lg w-100" style="background:var(--alert);color:#fff" href="tel:<?=e(preg_replace('/[^0-9+]/','',$p['emergency_contact_phone']))?>"><i class="bi bi-telephone-fill"></i> Call <?=e($p['emergency_contact_phone'])?></a></div>
<p class="small text-muted mt-3 mb-0">Information provided by the patient and may not be complete. In an emergency, contact professional medical services.</p></div></div>
<?php endif;
require 'includes/footer.php';
