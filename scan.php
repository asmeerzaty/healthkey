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
if($err):?><div class="alert alert-danger text-center fs-5"><?=e($err)?></div>
<?php else:function row($l,$v){echo "<div class='col-md-6'><div class='text-muted small'>$l</div><div class='fs-5 fw-semibold'>".e($v?:'—')."</div></div>";}?>
<div class="card emergency p-4"><h2 class="text-danger"><i class="bi bi-heart-pulse-fill"></i> HEALTHKEY — Emergency Health Information</h2><hr><div class="row g-3">
<?php row('Patient',$p['name']);row('Blood Type',$p['blood_type']);row('Date of Birth',$p['date_of_birth']);row('Allergies',$p['allergies']);row('Medical Conditions',$p['medical_conditions']);row('Current Medication',$p['medications']);
row('Doctor',$p['doctor_name']);if($p['share_notes'])row('Notes',$p['additional_notes']);?></div><hr>
<h5>Emergency Contact</h5><p class="fs-5 mb-2"><?=e($p['emergency_contact_name'])?> (<?=e($p['emergency_contact_relationship'])?>)</p>
<a class="btn btn-danger btn-lg" href="tel:<?=e(preg_replace('/[^0-9+]/','',$p['emergency_contact_phone']))?>"><i class="bi bi-telephone-fill"></i> <?=e($p['emergency_contact_phone'])?></a>
<p class="small text-muted mt-3 mb-0">Information provided by the patient and may not be complete. In an emergency, contact professional medical services.</p></div><?php endif;
require 'includes/footer.php';
