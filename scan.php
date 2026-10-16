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
// English / Bahasa Melayu — responders in Malaysia may need either
$lg=($_GET['lang']??'')==='ms'?'ms':'en';
$L=['en'=>['id'=>'Medical ID','pt'=>'Patient','born'=>'Born','blood'=>'Blood type','al'=>'ALLERGY','cond'=>'Medical conditions','med'=>'Current medication','dr'=>'Doctor','notes'=>'Notes','ec'=>'Emergency contact','call'=>'Call','none'=>'None listed','noal'=>'No known allergies listed','amb'=>'Call 999','disc'=>'Information provided by the patient and may not be complete. In an emergency, contact professional medical services.','bad'=>'This code cannot be used right now. If this is an emergency, call 999.'],
 'ms'=>['id'=>'ID Perubatan','pt'=>'Pesakit','born'=>'Lahir','blood'=>'Jenis darah','al'=>'ALAHAN','cond'=>'Keadaan perubatan','med'=>'Ubat semasa','dr'=>'Doktor','notes'=>'Nota','ec'=>'Waris kecemasan','call'=>'Hubungi','none'=>'Tiada','noal'=>'Tiada alahan disenaraikan','amb'=>'Hubungi 999','disc'=>'Maklumat ini diberikan oleh pesakit dan mungkin tidak lengkap. Dalam kecemasan, hubungi perkhidmatan perubatan profesional.','bad'=>'Kod ini tidak boleh digunakan sekarang. Jika ini kecemasan, hubungi 999.']][$lg];
$title='Emergency Info';$bare=true; require 'includes/header.php';
if($err):?><div class="sosx"><div class="alert alert-danger text-center fs-5"><?=e($err)?></div><p class="text-center text-muted"><?=e($L['bad'])?></p><a class="btn btn-alert w-100 btn-lg" href="tel:999"><i class="bi bi-telephone-fill"></i> <?=e($L['amb'])?></a></div>
<?php else:
 $hasAl=trim((string)$p['allergies'])!==''&&!preg_match('/^\s*(none|no|nil|nkda|tiada|n\/a)\b/i',$p['allergies']);
 $tel=preg_replace('/[^0-9+]/','',$p['emergency_contact_phone']);
 function fact($l,$v,$w=false){echo "<div class='col-sm-6'><div class='fact".($w?' warn':'')."'><div class='k'>".e($l)."</div><div class='v'>".e($v)."</div></div></div>";}?>
<div class="sosx"><div class="topbar"><span><i class="bi bi-heart-pulse-fill text-danger"></i> HEALTHKEY · <?=e($L['id'])?></span><span class="langsw no-print"><a class="<?=$lg==='en'?'on':''?>" href="?token=<?=e($t)?>&amp;lang=en">EN</a><a class="<?=$lg==='ms'?'on':''?>" href="?token=<?=e($t)?>&amp;lang=ms">BM</a></span></div>
<?php if($hasAl):?><div class="alertbar" role="alert"><i class="bi bi-exclamation-triangle-fill"></i><div><div class="k"><?=e($L['al'])?></div><div class="v"><?=e($p['allergies'])?></div></div></div><?php endif;?>
<div class="idcard"><div><div class="text-muted small fw-semibold"><?=e($L['pt'])?></div><div class="sos-name"><?=e($p['name'])?></div><?php if($p['date_of_birth']):?><div class="text-muted"><?=e($L['born'])?> <?=e($p['date_of_birth'])?></div><?php endif;?></div><div><div class="blood"><?=e($p['blood_type'])?></div><small class="d-block text-center text-muted mt-1"><?=e($L['blood'])?></small></div></div>
<div class="row g-3 mt-0"><?php if(!$hasAl)fact($L['al'],$L['noal']);fact($L['cond'],$p['medical_conditions']?:$L['none'],(bool)$p['medical_conditions']);fact($L['med'],$p['medications']?:$L['none']);fact($L['dr'],$p['doctor_name']?:$L['none']);if($p['share_notes']&&$p['additional_notes'])fact($L['notes'],$p['additional_notes']);?></div>
<div class="mt-3 p-3 rounded-4" style="background:var(--ink);color:#fff"><div class="small" style="color:#9fb6c4"><?=e($L['ec'])?></div><div class="fs-5 fw-semibold"><?=e($p['emergency_contact_name'])?> <span class="fw-normal" style="color:#9fb6c4">(<?=e($p['emergency_contact_relationship'])?>)</span></div><div class="fs-5"><?=e($p['emergency_contact_phone'])?></div></div>
<p class="small text-muted mt-3 mb-0"><?=e($L['disc'])?></p></div>
<div class="callbar"><div class="in"><a class="btn btn-teal" href="tel:<?=e($tel)?>"><i class="bi bi-telephone-fill"></i> <?=e($L['call'])?> <?=e($p['emergency_contact_name'])?></a><a class="btn btn-alert" href="tel:999"><i class="bi bi-hospital"></i> <?=e($L['amb'])?></a></div></div>
<?php endif;
require 'includes/footer.php';
