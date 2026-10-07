<?php $title='Health Profile'; require 'includes/header.php'; require_login();
$f=['date_of_birth','blood_type','allergies','medical_conditions','medications','emergency_contact_name','emergency_contact_phone','emergency_contact_relationship','doctor_name','additional_notes'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 $v=[];foreach($f as $k)$v[$k]=trim($_POST[$k]??'')?:null;$sn=isset($_POST['share_notes'])?1:0;
 if(!$v['blood_type']||!$v['emergency_contact_name']||!$v['emergency_contact_phone']){flash('Blood type and emergency contact name & phone are required.','danger');}
 else{if(profile_of($u['id'])){$set=implode(',',array_map(fn($k)=>"$k=?",$f));q("UPDATE patient_profiles SET $set,share_notes=? WHERE user_id=?",[...array_values($v),$sn,$u['id']]);}
  else q('INSERT INTO patient_profiles('.implode(',',$f).',share_notes,user_id) VALUES('.str_repeat('?,',count($f)).'?,?)',[...array_values($v),$sn,$u['id']]);
  sync_qr($u['id']);flash('Health profile saved.');redirect('health-profile.php');}
}
$p=profile_of($u['id'])?:array_fill_keys($f,'')+['share_notes'=>0];
function fld($l,$k,$p,$t='text',$req=false){echo "<div class='col-md-6'><label class='form-label'>$l".($req?' *':'')."</label>";
 if($t==='area')echo "<textarea name='$k' class='form-control' rows='2'>".e($p[$k])."</textarea>";else echo "<input type='$t' name='$k' class='form-control' value='".e($p[$k])."'>";echo "</div>";}?>
<div class="card p-4"><h3><i class="bi bi-heart-pulse"></i> Health Information</h3>
<p class="text-muted small">Everything except additional notes is shown on your public emergency page. Only enter what you're comfortable sharing with a responder.</p>
<form method="post"><?=csrf()?><div class="row g-3">
<div class="col-md-6"><label class="form-label">Full Name</label><input class="form-control" value="<?=e($u['name'])?>" disabled></div>
<?php fld('Date of Birth','date_of_birth',$p,'date');fld('Blood Type (e.g. O+)','blood_type',$p,'text',true);fld('Doctor Name','doctor_name',$p);
fld('Allergies','allergies',$p,'area');fld('Medical Conditions','medical_conditions',$p,'area');fld('Current Medications','medications',$p,'area');
fld('Emergency Contact Name','emergency_contact_name',$p,'text',true);fld('Emergency Contact Phone','emergency_contact_phone',$p,'text',true);fld('Relationship','emergency_contact_relationship',$p);
fld('Additional Medical Notes (private by default)','additional_notes',$p,'area');?>
<div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="share_notes" id="sn" <?=$p['share_notes']?'checked':''?>><label class="form-check-label" for="sn">Show additional notes on the public emergency page</label></div></div>
<div class="col-12"><button class="btn btn-primary">Save</button></div></div></form></div>
<?php require 'includes/footer.php';
