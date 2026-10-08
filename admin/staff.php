<?php require '_top.php'; need_super();
if($_SERVER['REQUEST_METHOD']==='POST'){$id=(int)$_POST['id'];$t=q("SELECT role FROM users WHERE id=?",[$id])->fetch();
 if(!$t||$t['role']!=='admin')flash('Only admin accounts can be changed here.','danger');
 elseif(($_POST['act']??'')==='delete'){q('DELETE FROM users WHERE id=?',[$id]);flash('Admin deleted.');}
 else{q('UPDATE users SET is_active=1-is_active WHERE id=?',[$id]);flash('Admin status updated.');}
 header('Location: staff.php');exit;}
$rows=q("SELECT u.*,(SELECT COUNT(*) FROM staff_messages s WHERE s.admin_id=u.id) msgs FROM users u WHERE role IN('admin','superadmin') ORDER BY role DESC,id")->fetchAll();?>
<div class="page-head"><div><h3 class="mb-0">Staff (admins)</h3><span class="text-muted">Admins handle customers. You manage the admins.</span></div><a class="btn btn-primary" href="staff-form.php"><i class="bi bi-plus-lg"></i> Add admin</a></div>
<div class="table-responsive card p-2"><table class="table align-middle mb-0"><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr>
<?php foreach($rows as $r):?><tr><td><?=e($r['name'])?></td><td><?=e($r['email'])?></td><td><span class="badge <?=$r['role']==='superadmin'?'':'bg-secondary'?>" <?=$r['role']==='superadmin'?'style="background:var(--teal);color:var(--ink)"':''?>><?=$r['role']==='superadmin'?'Owner':'Admin'?></span></td><td><span class="badge <?=$r['is_active']?'bg-success':'bg-secondary'?>"><?=$r['is_active']?'Active':'Deactivated'?></span></td>
<td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="staff-form.php?id=<?=$r['id']?>">Edit</a>
<?php if($r['role']==='admin'):?><a class="btn btn-sm btn-outline-primary" href="staff-chat.php?aid=<?=$r['id']?>">Chat</a>
<form method="post" class="d-inline"><?=csrf()?><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn btn-sm btn-outline-secondary"><?=$r['is_active']?'Deactivate':'Activate'?></button></form>
<form method="post" class="d-inline" onsubmit="return confirm('Delete this admin account?')"><?=csrf()?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form><?php endif;?></td></tr><?php endforeach;?></table></div>
<?php require '_bottom.php';
