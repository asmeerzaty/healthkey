<?php require '_top.php'; need_super(); require_once __DIR__.'/../includes/content.php';
if($_SERVER['REQUEST_METHOD']==='POST'){$id=(int)$_POST['id'];$act=$_POST['act']??'';
 if($act==='delete'){q('DELETE FROM bracelets WHERE id=?',[$id]);flash('Bracelet deleted (past orders keep their details).');}
 elseif($act==='toggle'){q('UPDATE bracelets SET is_active=1-is_active WHERE id=?',[$id]);flash('Visibility updated.');}
 elseif($act==='stock'){q('UPDATE bracelets SET stock=? WHERE id=?',[max(0,(int)$_POST['stock']),$id]);flash('Stock updated.');}
 header('Location: bracelets.php');exit;}
$s='%'.trim($_GET['s']??'').'%';$rows=q('SELECT b.*,(SELECT COUNT(*) FROM orders o WHERE o.bracelet_id=b.id AND o.payment_status=\'paid\') sold FROM bracelets b WHERE name LIKE ? OR material LIKE ? ORDER BY id DESC',[$s,$s])->fetchAll();?>
<div class="page-head"><div><h3 class="mb-0">Bracelets</h3><span class="text-muted">The catalog customers buy from. Only the owner can change it.</span></div><a class="btn btn-primary" href="bracelet-form.php"><i class="bi bi-plus-lg"></i> Add bracelet</a></div>
<form class="mb-3 d-flex gap-2"><input name="s" class="form-control" placeholder="Search name or material" value="<?=e($_GET['s']??'')?>"><button class="btn btn-primary">Search</button></form>
<div class="table-responsive card p-2"><table class="table align-middle mb-0"><tr><th></th><th>Name</th><th>Type / material</th><th>Price</th><th>Stock</th><th>Sold</th><th>Visible</th><th></th></tr>
<?php foreach($rows as $r):?><tr><td><div class="upl"><div class="th" style="width:56px;height:56px"><?=bracelet_art($r,46)?></div></div></td><td><b><?=e($r['name'])?></b><div class="small text-muted"><?=e($r['description'])?></div></td><td><?=e($r['type'])?> · <?=e($r['material'])?></td><td>RM<?=number_format($r['price'],2)?></td>
<td><form method="post" class="d-flex gap-1" style="width:130px"><?=csrf()?><input type="hidden" name="act" value="stock"><input type="hidden" name="id" value="<?=$r['id']?>"><input type="number" min="0" name="stock" value="<?=$r['stock']?>" class="form-control form-control-sm"><button class="btn btn-sm btn-outline-primary">Set</button></form><?php if($r['stock']<=5):?><span class="badge bg-warning text-dark mt-1"><?=$r['stock']?'Low stock':'Sold out'?></span><?php endif;?></td><td><?=$r['sold']?></td>
<td><form method="post"><?=csrf()?><input type="hidden" name="act" value="toggle"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn btn-sm <?=$r['is_active']?'btn-success':'btn-outline-secondary'?>"><?=$r['is_active']?'Shown':'Hidden'?></button></form></td>
<td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="bracelet-form.php?id=<?=$r['id']?>">Edit</a> <form method="post" class="d-inline" onsubmit="return confirm('Delete this bracelet?')"><?=csrf()?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr><?php endforeach;?>
<?php if(!$rows)echo '<tr><td colspan="8" class="text-center text-muted py-4">No bracelets yet.</td></tr>';?></table></div>
<?php require '_bottom.php';
