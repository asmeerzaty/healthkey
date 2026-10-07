<?php require '_top.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(isset($_POST['gen'])){$n=max(1,min(50,(int)$_POST['n']));for($i=0;$i<$n;$i++)q("INSERT INTO qr_codes(unique_token,status) VALUES(?, 'unclaimed')",[new_token()]);flash("$n unclaimed tags generated (for printing).");}
 elseif(isset($_POST['id'])){$r=q('SELECT * FROM qr_codes WHERE id=?',[(int)$_POST['id']])->fetch();
  if($r){if($r['status']==='disabled'){q("UPDATE qr_codes SET status=? WHERE id=?",[!$r['user_id']?'unclaimed':(profile_complete($r['user_id'])?'active':'claimed'),$r['id']]);}else q("UPDATE qr_codes SET status='disabled' WHERE id=?",[$r['id']]);flash('QR status updated.');}}
 header('Location: qr-codes.php');exit;}
[$pg,$off,$per]=page(0);$tot=q('SELECT COUNT(*) c FROM qr_codes')->fetch()['c'];
$rows=q("SELECT c.*,u.name,(SELECT MAX(accessed_at) FROM emergency_access_logs l WHERE l.qr_id=c.id) last_scan FROM qr_codes c LEFT JOIN users u ON u.id=c.user_id ORDER BY c.id DESC LIMIT $per OFFSET $off")->fetchAll();?>
<h3>QR Codes</h3><form method="post" class="d-flex gap-2 mb-3" style="max-width:380px"><?=csrf()?><input type="number" name="n" value="5" min="1" max="50" class="form-control"><button name="gen" value="1" class="btn btn-teal text-nowrap">Generate stock tags</button></form>
<div class="table-responsive card p-2"><table class="table align-middle mb-0"><tr><th>User</th><th>Token</th><th>Status</th><th>Created</th><th>Last scan</th><th></th></tr>
<?php foreach($rows as $r):?><tr><td><?=e($r['name']?:'— unclaimed —')?></td><td><code><?=e(substr($r['unique_token'],0,12))?>…</code> <a class="small" target="_blank" href="<?=BASE_URL?>/scan.php?token=<?=e($r['unique_token'])?>">test</a></td><td><span class="badge bg-secondary"><?=e($r['status'])?></span></td><td><?=e($r['created_at'])?></td><td><?=e($r['last_scan']?:'—')?></td>
<td><form method="post"><?=csrf()?><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn btn-sm btn-outline-danger"><?=$r['status']==='disabled'?'Re-enable':'Deactivate'?></button></form></td></tr><?php endforeach;?></table></div><?php pager($tot);?>
<?php require '_bottom.php';
