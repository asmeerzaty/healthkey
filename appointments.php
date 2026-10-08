<?php require 'includes/bootstrap.php'; require_login(); $u=user();
if($u['role']!=='user')redirect('admin/appointments.php');
$uid=$u['id'];$ST=['upcoming','completed','cancelled'];
// ---------- CREATE / UPDATE / DELETE (every query is scoped to the logged-in user = ownership check) ----------
if($_SERVER['REQUEST_METHOD']==='POST'){$act=$_POST['act']??'';$id=(int)($_POST['id']??0);
 if($act==='delete'){q('DELETE FROM appointments WHERE id=? AND user_id=?',[$id,$uid]);flash('Appointment deleted.');redirect('appointments.php');}
 if($act==='status'&&in_array($_POST['status']??'',$ST)){q('UPDATE appointments SET status=? WHERE id=? AND user_id=?',[$_POST['status'],$id,$uid]);flash('Status updated.');redirect('appointments.php');}
 if($act==='save'){
  $f=['title'=>trim($_POST['title']??''),'doctor_name'=>trim($_POST['doctor_name']??''),'location'=>trim($_POST['location']??''),'appointment_date'=>str_replace('T',' ',$_POST['appointment_date']??''),'notes'=>trim($_POST['notes']??'')];
  if($f['title']===''||!strtotime($f['appointment_date'])){flash('Title and a valid date/time are required.','danger');redirect('appointments.php?'.($id?'edit='.$id:'new=1'));}
  if($id){if(!q('SELECT 1 FROM appointments WHERE id=? AND user_id=?',[$id,$uid])->fetch()){flash('Appointment not found.','danger');redirect('appointments.php');}
   q('UPDATE appointments SET title=?,doctor_name=?,location=?,appointment_date=?,notes=? WHERE id=? AND user_id=?',[...array_values($f),$id,$uid]);flash('Appointment updated.');}
  else{q('INSERT INTO appointments(title,doctor_name,location,appointment_date,notes,user_id) VALUES(?,?,?,?,?,?)',[...array_values($f),$uid]);flash('Appointment added.');}
  redirect('appointments.php');}}
// ---------- READ ----------
$edit=isset($_GET['edit'])?q('SELECT * FROM appointments WHERE id=? AND user_id=?',[(int)$_GET['edit'],$uid])->fetch():null;
$show=isset($_GET['new'])||$edit;$v=$edit?:['id'=>0,'title'=>'','doctor_name'=>'','location'=>'','appointment_date'=>'','notes'=>''];
$rows=q('SELECT * FROM appointments WHERE user_id=? ORDER BY appointment_date DESC',[$uid])->fetchAll();
$title='Appointments'; require 'includes/header.php';$badge=['upcoming'=>'bg-primary','completed'=>'bg-success','cancelled'=>'bg-secondary'];?>
<div class="page-head"><div><h2 class="mb-0">Appointments</h2><span class="text-muted">Keep track of doctor visits and check-ups.</span></div><?php if(!$show):?><a class="btn btn-primary" href="?new=1"><i class="bi bi-plus-lg"></i> New appointment</a><?php endif;?></div>
<?php if($show):?><div class="card p-4 mb-4"><h5><?=$edit?'Edit appointment':'New appointment'?></h5>
<form method="post" class="row g-3"><?=csrf()?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?=(int)$v['id']?>">
<div class="col-md-6"><label class="form-label">Title *</label><input name="title" class="form-control" required maxlength="120" value="<?=e($v['title'])?>"></div>
<div class="col-md-6"><label class="form-label">Date &amp; time *</label><input type="datetime-local" name="appointment_date" class="form-control" required value="<?=$v['appointment_date']?date('Y-m-d\TH:i',strtotime($v['appointment_date'])):''?>"></div>
<div class="col-md-6"><label class="form-label">Doctor</label><input name="doctor_name" class="form-control" value="<?=e($v['doctor_name'])?>"></div>
<div class="col-md-6"><label class="form-label">Hospital / clinic</label><input name="location" class="form-control" value="<?=e($v['location'])?>"></div>
<div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"><?=e($v['notes'])?></textarea></div>
<div class="col-12"><button class="btn btn-primary">Save</button> <a class="btn btn-outline-primary" href="appointments.php">Cancel</a></div></form></div><?php endif;?>
<div class="card p-2"><div class="table-responsive"><table class="table align-middle mb-0"><tr><th>When</th><th>Title</th><th>Doctor</th><th>Where</th><th>Status</th><th></th></tr>
<?php foreach($rows as $r):?><tr><td class="text-nowrap"><?=e(date('j M Y, g:i A',strtotime($r['appointment_date'])))?></td><td><b><?=e($r['title'])?></b><?php if($r['notes']):?><div class="small text-muted"><?=e($r['notes'])?></div><?php endif;?></td><td><?=e($r['doctor_name']?:'—')?></td><td><?=e($r['location']?:'—')?></td><td><span class="badge <?=$badge[$r['status']]?>"><?=e($r['status'])?></span></td>
<td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="?edit=<?=$r['id']?>">Edit</a>
<?php if($r['status']==='upcoming'):?><form method="post" class="d-inline"><?=csrf()?><input type="hidden" name="act" value="status"><input type="hidden" name="id" value="<?=$r['id']?>"><input type="hidden" name="status" value="completed"><button class="btn btn-sm btn-outline-success">Done</button></form><?php endif;?>
<form method="post" class="d-inline" onsubmit="return confirm('Delete this appointment?')"><?=csrf()?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr><?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="6" class="text-center text-muted py-4">No appointments yet. Add your first one.</td></tr><?php endif;?></table></div></div>
<?php require 'includes/footer.php';
