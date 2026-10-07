<?php require '_top.php';
if($_SERVER['REQUEST_METHOD']==='POST'){q("UPDATE users SET is_active=1-is_active WHERE id=? AND role='user'",[(int)$_POST['id']]);flash('User status updated.');header('Location: users.php');exit;}
$s='%'.trim($_GET['s']??'').'%';[$pg,$off,$per]=page(0);
$tot=q('SELECT COUNT(*) c FROM users WHERE name LIKE ? OR email LIKE ?',[$s,$s])->fetch()['c'];
$rows=q("SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC LIMIT $per OFFSET $off",[$s,$s])->fetchAll();?>
<h3>Users</h3><form class="mb-3 d-flex gap-2"><input name="s" class="form-control" placeholder="Search name or email" value="<?=e($_GET['s']??'')?>"><button class="btn btn-primary">Search</button></form>
<div class="table-responsive card p-2"><table class="table align-middle mb-0"><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr>
<?php foreach($rows as $r):?><tr><td><?=$r['id']?></td><td><?=e($r['name'])?></td><td><?=e($r['email'])?></td><td><?=$r['role']?></td><td><span class="badge <?=$r['is_active']?'bg-success':'bg-secondary'?>"><?=$r['is_active']?'Active':'Deactivated'?></span></td>
<td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="user-view.php?id=<?=$r['id']?>">View</a>
<?php if($r['role']==='user'):?><form method="post" class="d-inline"><?=csrf()?><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn btn-sm btn-outline-danger"><?=$r['is_active']?'Deactivate':'Activate'?></button></form><?php endif;?></td></tr><?php endforeach;?></table></div><?php pager($tot);?>
<?php require '_bottom.php';
