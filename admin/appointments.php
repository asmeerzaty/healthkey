<?php require '_top.php';
if($_SERVER['REQUEST_METHOD']==='POST'){$id=(int)$_POST['id'];
 if(($_POST['act']??'')==='delete'){q('DELETE FROM appointments WHERE id=?',[$id]);flash('Appointment deleted.');}
 elseif(in_array($_POST['status']??'',['upcoming','completed','cancelled'])){q('UPDATE appointments SET status=? WHERE id=?',[$_POST['status'],$id]);flash('Status updated.');}
 header('Location: appointments.php');exit;}
$s='%'.trim($_GET['s']??'').'%';[$pg,$off,$per]=page(0);
$tot=q('SELECT COUNT(*) c FROM appointments a JOIN users u ON u.id=a.user_id WHERE u.name LIKE ? OR a.title LIKE ?',[$s,$s])->fetch()['c'];
$rows=q("SELECT a.*,u.name FROM appointments a JOIN users u ON u.id=a.user_id WHERE u.name LIKE ? OR a.title LIKE ? ORDER BY a.appointment_date DESC LIMIT $per OFFSET $off",[$s,$s])->fetchAll();?>
<h3>Appointments</h3><form class="mb-3 d-flex gap-2"><input name="s" class="form-control" placeholder="Search patient or title" value="<?=e($_GET['s']??'')?>"><button class="btn btn-primary">Search</button></form>
<div class="table-responsive card p-2"><table class="table align-middle mb-0"><tr><th>Patient</th><th>When</th><th>Title</th><th>Doctor / place</th><th>Status</th><th></th></tr>
<?php foreach($rows as $r):?><tr><td><a href="user-view.php?id=<?=$r['user_id']?>"><?=e($r['name'])?></a></td><td class="text-nowrap"><?=e(date('j M Y, g:i A',strtotime($r['appointment_date'])))?></td><td><?=e($r['title'])?></td><td><?=e(trim($r['doctor_name'].' '.$r['location'])?:'—')?></td>
<td><form method="post" class="d-flex gap-1"><?=csrf()?><input type="hidden" name="id" value="<?=$r['id']?>"><select name="status" class="form-select form-select-sm"><?php foreach(['upcoming','completed','cancelled'] as $x)echo '<option '.($x===$r['status']?'selected':'').'>'.$x.'</option>';?></select><button class="btn btn-sm btn-primary">Set</button></form></td>
<td><form method="post" onsubmit="return confirm('Delete this appointment?')"><?=csrf()?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr><?php endforeach;?></table></div><?php pager($tot);?>
<?php require '_bottom.php';
